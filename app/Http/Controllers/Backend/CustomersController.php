<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomersController extends Controller
{
    public $user;

    public function __construct()
    {
         $this->user = Auth::guard('admin')->user();
    }

    public function index(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('customer.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Customers !');
        }

        $query = Customer::query();

        if ($term = $request->get('q')) {
            $query->where(function ($q) use ($term) {
                $q->where('customer_name', 'like', "%$term%")
                    ->orWhere('customer_code', 'like', "%$term%")
                    ->orWhere('mobile', 'like', "%$term%")
                    ->orWhere('email', 'like', "%$term%");
            });
        }

        $customers = $query->orderBy('customer_name')->paginate(15)->withQueryString();

        return view('backend.customers.index', compact('customers'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('customer.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Customers !');
        }

        return view('backend.customers.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('customer.create')) {
            abort(403);
        }

        $validated = $this->validateCustomer($request);
        $validated['customer_code'] = $request->filled('customer_code') ? $request->customer_code : $this->generateCode();

        $customer = Customer::create($validated);

        AuditLog::record('create', 'Customers', $customer, "Created customer \"{$customer->customer_name}\"");

        // Quick-add from the Sale form (Section 36 pattern reused for customers).
        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'customer' => $customer]);
        }

        session()->flash('success', $customer->customer_name.' has been added !!');
        return redirect()->route('admin.customers.index');
    }

    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('customer.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit Customers !');
        }

        $customer = Customer::findOrFail($id);

        return view('backend.customers.edit', compact('customer'));
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('customer.edit')) {
            abort(403);
        }

        $customer = Customer::findOrFail($id);
        $original = $customer->only(['customer_name', 'mobile', 'email']);

        $validated = $this->validateCustomer($request, $id);
        $customer->update($validated);

        AuditLog::record('update', 'Customers', $customer, "Updated customer \"{$customer->customer_name}\"", $original, $customer->only(['customer_name', 'mobile', 'email']));

        session()->flash('success', $customer->customer_name.' has been updated !!');
        return redirect()->route('admin.customers.index');
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('customer.delete')) {
            abort(403);
        }

        $customer = Customer::findOrFail($id);

        if ($customer->sales()->exists()) {
            session()->flash('error', 'Cannot delete a customer with sales history. Deactivate instead.');
            return back();
        }

        AuditLog::record('delete', 'Customers', $customer, "Deleted customer \"{$customer->customer_name}\"");
        $customer->delete();

        session()->flash('success', 'Customer has been deleted !!');
        return back();
    }

    public function ledger(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('customer.view')) {
            abort(403);
        }

        $customer = Customer::with([
            'sales' => fn ($q) => $q->latest(),
            'salesReturns' => fn ($q) => $q->latest(),
        ])->findOrFail($id);

        return view('backend.customers.ledger', compact('customer'));
    }

    private function validateCustomer(Request $request, ?int $ignoreId = null): array
    {
        return $request->validate([
            'customer_name' => 'required|max:150',
            'customer_code' => 'nullable|max:30|unique:customers,customer_code,'.($ignoreId ?? 'NULL').',id',
            'company_name' => 'nullable|max:150',
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
            'discount_percentage' => 'nullable|numeric|min:0|max:100',
            'notes' => 'nullable',
            'status' => 'nullable|boolean',
        ]);
    }

    private function generateCode(): string
    {
        $next = (Customer::max('id') ?? 0) + 1;
        return 'CUS-'.str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
