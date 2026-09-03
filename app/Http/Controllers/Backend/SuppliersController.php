<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SuppliersController extends Controller
{
    public $user;

    public function __construct()
    {
         $this->user = Auth::guard('admin')->user();
    }

    public function index(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('supplier.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Suppliers !');
        }

        $query = Supplier::query();

        if ($term = $request->get('q')) {
            $query->where(function ($q) use ($term) {
                $q->where('company_name', 'like', "%$term%")
                    ->orWhere('supplier_code', 'like', "%$term%")
                    ->orWhere('mobile', 'like', "%$term%")
                    ->orWhere('email', 'like', "%$term%");
            });
        }

        $suppliers = $query->orderBy('company_name')->paginate(25)->withQueryString();

        return view('backend.suppliers.index', compact('suppliers'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('supplier.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Suppliers !');
        }

        return view('backend.suppliers.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('supplier.create')) {
            abort(403);
        }

        $validated = $this->validateSupplier($request);
        $validated['supplier_code'] = $request->filled('supplier_code') ? $request->supplier_code : $this->generateCode();

        $supplier = Supplier::create($validated);

        AuditLog::record('create', 'Suppliers', $supplier, "Created supplier \"{$supplier->company_name}\"");

        session()->flash('success', $supplier->company_name.' has been added !!');
        return redirect()->route('admin.suppliers.index');
    }

    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('supplier.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit Suppliers !');
        }

        $supplier = Supplier::findOrFail($id);

        return view('backend.suppliers.edit', compact('supplier'));
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('supplier.edit')) {
            abort(403);
        }

        $supplier = Supplier::findOrFail($id);
        $original = $supplier->only(['company_name', 'mobile', 'email']);

        $validated = $this->validateSupplier($request, $id);
        $supplier->update($validated);

        AuditLog::record('update', 'Suppliers', $supplier, "Updated supplier \"{$supplier->company_name}\"", $original, $supplier->only(['company_name', 'mobile', 'email']));

        session()->flash('success', $supplier->company_name.' has been updated !!');
        return redirect()->route('admin.suppliers.index');
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('supplier.delete')) {
            abort(403);
        }

        $supplier = Supplier::findOrFail($id);

        if ($supplier->purchases()->exists()) {
            session()->flash('error', 'Cannot delete a supplier with purchase history. Deactivate instead.');
            return back();
        }

        AuditLog::record('delete', 'Suppliers', $supplier, "Deleted supplier \"{$supplier->company_name}\"");
        $supplier->delete();

        session()->flash('success', 'Supplier has been deleted !!');
        return back();
    }

    /**
     * Section 10: Purchases, Purchase Returns, Payments, Outstanding Balance —
     * the full supplier ledger on one page.
     */
    public function ledger(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('supplier.view')) {
            abort(403);
        }

        $supplier = Supplier::with([
            'purchases' => fn ($q) => $q->latest(),
            'purchaseOrders' => fn ($q) => $q->latest(),
            'purchaseReturns' => fn ($q) => $q->latest(),
        ])->findOrFail($id);

        return view('backend.suppliers.ledger', compact('supplier'));
    }

    private function validateSupplier(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'company_name' => 'required|max:150',
            'supplier_code' => 'nullable|max:30|unique:suppliers,supplier_code,'.($ignoreId ?? 'NULL').',id',
            'contact_person' => 'nullable|max:100',
            'mobile' => 'nullable|max:30',
            'whatsapp' => 'nullable|max:30',
            'email' => 'nullable|email|max:100',
            'address' => 'nullable',
            'city' => 'nullable|max:60',
            'country' => 'nullable|max:60',
            'tax_number' => 'nullable|max:60',
            'opening_balance' => 'nullable|numeric',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms' => 'nullable|max:100',
            'bank_details' => 'nullable',
            'notes' => 'nullable',
            'status' => 'nullable|boolean',
        ]);
    }

    private function generateCode(): string
    {
        $next = (Supplier::max('id') ?? 0) + 1;
        return 'SUP-'.str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
