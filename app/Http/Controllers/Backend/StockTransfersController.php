<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\SparePart;
use App\Models\StockTransfer;
use App\Models\StockTransferItem;
use App\Models\Warehouse;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StockTransfersController extends Controller
{
    public $user;
    private StockService $stockService;

    public function __construct(StockService $stockService)
    {
        $this->stockService = $stockService;

         $this->user = Auth::guard('admin')->user();
    }

    public function index()
    {
        if (is_null($this->user) || ! $this->user->can('stock-transfer.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Stock Transfers !');
        }

        $transfers = StockTransfer::with(['fromWarehouse', 'toWarehouse', 'requestedBy', 'items'])->latest()->paginate(15);

        return view('backend.stock-transfers.index', compact('transfers'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('stock-transfer.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Stock Transfers !');
        }

        $warehouses = Warehouse::where('status', 1)->orderBy('name')->get();
        $spareParts = SparePart::orderBy('name')->get(['id', 'name', 'part_number', 'current_stock']);

        return view('backend.stock-transfers.create', compact('warehouses', 'spareParts'));
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('stock-transfer.create')) {
            abort(403);
        }

        $request->validate([
            'from_warehouse_id' => 'required|exists:warehouses,id',
            'to_warehouse_id' => 'required|exists:warehouses,id|different:from_warehouse_id',
            'transfer_date' => 'required|date',
            'remarks' => 'nullable',
            'spare_part_id' => 'required|array|min:1',
            'spare_part_id.*' => 'exists:spare_parts,id',
            'quantity' => 'required|array',
            'quantity.*' => 'integer|min:1',
        ]);

        $transfer = DB::transaction(function () use ($request) {
            $transfer = StockTransfer::create([
                'transfer_number' => $this->nextNumber(),
                'from_warehouse_id' => $request->from_warehouse_id,
                'to_warehouse_id' => $request->to_warehouse_id,
                'transfer_date' => $request->transfer_date,
                'remarks' => $request->remarks,
                'status' => 'pending',
                'requested_by' => $this->user->id,
            ]);

            foreach ($request->spare_part_id as $i => $partId) {
                StockTransferItem::create([
                    'stock_transfer_id' => $transfer->id,
                    'spare_part_id' => $partId,
                    'quantity' => $request->quantity[$i],
                ]);
            }

            return $transfer;
        });

        AuditLog::record('create', 'Stock Transfers', $transfer, "Requested stock transfer {$transfer->transfer_number}");

        session()->flash('success', "Transfer {$transfer->transfer_number} requested — awaiting approval.");
        return redirect()->route('admin.stock-transfers.index');
    }

    /**
     * Approving reserves nothing extra in this MVP — it moves straight to
     * "received" in one step via StockService::transfer(), which writes both
     * the TRANSFER_OUT and TRANSFER_IN movements atomically. A stricter
     * two-step (approve -> physically move -> receive) flow can split this
     * into approve()/receive() once multi-day transfers are common enough
     * to need it.
     */
    public function approve(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('stock-transfer.approve')) {
            abort(403, 'Sorry !! You are Unauthorized to approve Stock Transfers !');
        }

        $transfer = StockTransfer::with(['items.sparePart', 'fromWarehouse', 'toWarehouse'])->findOrFail($id);

        if ($transfer->status !== 'pending') {
            session()->flash('error', 'This transfer has already been processed.');
            return back();
        }

        DB::transaction(function () use ($transfer) {
            foreach ($transfer->items as $item) {
                $this->stockService->transfer(
                    $item->sparePart,
                    $transfer->fromWarehouse,
                    $transfer->toWarehouse,
                    $item->quantity,
                    $transfer,
                    "Stock Transfer {$transfer->transfer_number}"
                );
            }

            $transfer->update([
                'status' => 'received',
                'approved_by' => $this->user->id,
                'approved_at' => now(),
                'received_by' => $this->user->id,
                'received_at' => now(),
            ]);
        });

        AuditLog::record('approve', 'Stock Transfers', $transfer, "Approved & completed stock transfer {$transfer->transfer_number}");

        session()->flash('success', "Transfer {$transfer->transfer_number} completed — stock moved.");
        return back();
    }

    public function cancel(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('stock-transfer.approve')) {
            abort(403);
        }

        $transfer = StockTransfer::findOrFail($id);

        if ($transfer->status !== 'pending') {
            session()->flash('error', 'Only a pending transfer can be cancelled.');
            return back();
        }

        $transfer->update(['status' => 'cancelled']);
        AuditLog::record('cancel', 'Stock Transfers', $transfer, "Cancelled stock transfer {$transfer->transfer_number}");

        session()->flash('success', "Transfer {$transfer->transfer_number} cancelled.");
        return back();
    }

    private function nextNumber(): string
    {
        $next = (StockTransfer::max('id') ?? 0) + 1;
        return 'TRF-'.now()->format('ym').'-'.str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
