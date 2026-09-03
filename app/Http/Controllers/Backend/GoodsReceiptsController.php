<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\GoodsReceipt;
use App\Models\GoodsReceiptItem;
use App\Models\PurchaseOrder;
use App\Services\StockService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class GoodsReceiptsController extends Controller
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
        if (is_null($this->user) || ! $this->user->can('purchase.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Goods Receipts !');
        }

        $goodsReceipts = GoodsReceipt::with(['purchaseOrder', 'supplier', 'warehouse'])->latest()->paginate(20);

        return view('backend.goods-receipts.index', compact('goodsReceipts'));
    }

    /**
     * Only approved POs with something still pending can be received against.
     */
    public function create(int $purchaseOrderId)
    {
        if (is_null($this->user) || ! $this->user->can('purchase.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Goods Receipts !');
        }

        $po = PurchaseOrder::with(['supplier', 'warehouse', 'items.sparePart'])->findOrFail($purchaseOrderId);

        if (! in_array($po->status, ['approved', 'partially_received'])) {
            session()->flash('error', 'This purchase order must be approved before receiving goods against it.');
            return redirect()->route('admin.purchase-orders.show', $po->id);
        }

        return view('backend.goods-receipts.create', compact('po'));
    }

    /**
     * Section 13: confirming a GRN is the ONLY place a Purchase Order's goods
     * turn into stock. Every accepted line calls StockService::move() with
     * type PURCHASE, referencing this GRN — the exact "Current Stock =
     * Current Stock + Received Quantity" rule from the spec, expressed as a
     * ledger write instead of a direct column update.
     */
    public function store(Request $request, int $purchaseOrderId)
    {
        if (is_null($this->user) || ! $this->user->can('purchase.create')) {
            abort(403);
        }

        $po = PurchaseOrder::with('items')->findOrFail($purchaseOrderId);

        $request->validate([
            'receiving_date' => 'required|date',
            'supplier_invoice_number' => 'nullable|max:60',
            'remarks' => 'nullable',
            'purchase_order_item_id' => 'required|array|min:1',
            'quantity_received' => 'required|array',
            'quantity_received.*' => 'integer|min:0',
            'quantity_damaged' => 'nullable|array',
            'quantity_short' => 'nullable|array',
        ]);

        $grn = DB::transaction(function () use ($request, $po) {
            $grn = GoodsReceipt::create([
                'grn_number' => $this->nextNumber(),
                'purchase_order_id' => $po->id,
                'supplier_id' => $po->supplier_id,
                'receiving_date' => $request->receiving_date,
                'warehouse_id' => $po->warehouse_id,
                'received_by' => $this->user->id,
                'supplier_invoice_number' => $request->supplier_invoice_number,
                'remarks' => $request->remarks,
                'status' => 'confirmed',
                'confirmed_at' => now(),
            ]);

            foreach ($request->purchase_order_item_id as $i => $poItemId) {
                $receivedQty = (int) $request->quantity_received[$i];
                if ($receivedQty <= 0) {
                    continue;
                }

                $poItem = $po->items->firstWhere('id', $poItemId);
                if (! $poItem) {
                    continue;
                }

                $damagedQty = (int) ($request->quantity_damaged[$i] ?? 0);
                $shortQty = (int) ($request->quantity_short[$i] ?? 0);

                GoodsReceiptItem::create([
                    'goods_receipt_id' => $grn->id,
                    'purchase_order_item_id' => $poItem->id,
                    'spare_part_id' => $poItem->spare_part_id,
                    'quantity_ordered' => $poItem->quantity,
                    'quantity_received' => $receivedQty,
                    'quantity_damaged' => $damagedQty,
                    'quantity_short' => $shortQty,
                ]);

                // Good units go into sellable stock; damaged units are logged
                // but do NOT increase sellable stock — they need a Purchase
                // Return or a DAMAGE adjustment to leave the building.
                $sellableQty = max(0, $receivedQty - $damagedQty);

                if ($sellableQty > 0) {
                    $this->stockService->move(
                        $poItem->sparePart,
                        $grn->warehouse,
                        'PURCHASE',
                        $sellableQty,
                        $grn,
                        "GRN {$grn->grn_number} against PO {$po->po_number}"
                    );
                }

                $poItem->increment('quantity_received', $receivedQty);
            }

            $po->refreshStatus();

            return $grn;
        });

        AuditLog::record('create', 'Goods Receipts', $grn, "Confirmed GRN {$grn->grn_number} against {$po->po_number} — stock updated");

        session()->flash('success', "Goods Receipt {$grn->grn_number} confirmed — stock has been updated. You can now raise the Purchase Invoice.");
        return redirect()->route('admin.purchases.create', ['goods_receipt_id' => $grn->id]);
    }

    public function show(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('purchase.view')) {
            abort(403);
        }

        $grn = GoodsReceipt::with(['purchaseOrder', 'supplier', 'warehouse', 'items.sparePart', 'receivedBy'])->findOrFail($id);

        return view('backend.goods-receipts.show', compact('grn'));
    }

    private function nextNumber(): string
    {
        $next = (GoodsReceipt::max('id') ?? 0) + 1;
        return 'GRN-'.now()->format('ym').'-'.str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
