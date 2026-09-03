<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\AuditLog;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ExpensesController extends Controller
{
    public $user;

    public function __construct()
    {
         $this->user = Auth::guard('admin')->user();
    }

    public function index(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('expense.view')) {
            abort(403, 'Sorry !! You are Unauthorized to view Expenses !');
        }

        $query = Expense::query();

        if ($category = $request->get('category')) {
            $query->where('category', $category);
        }

        if ($from = $request->get('from')) {
            $query->whereDate('expense_date', '>=', $from);
        }
        if ($to = $request->get('to')) {
            $query->whereDate('expense_date', '<=', $to);
        }

        $expenses = $query->latest('expense_date')->paginate(15)->withQueryString();
        $total = (clone $query)->sum('amount');

        return view('backend.expenses.index', compact('expenses', 'total'));
    }

    public function create()
    {
        if (is_null($this->user) || ! $this->user->can('expense.create')) {
            abort(403, 'Sorry !! You are Unauthorized to create Expenses !');
        }

        return view('backend.expenses.create');
    }

    public function store(Request $request)
    {
        if (is_null($this->user) || ! $this->user->can('expense.create')) {
            abort(403);
        }

        $validated = $this->validateExpense($request);

        if ($request->hasFile('attachment')) {
            $validated['attachment'] = $request->file('attachment')->store('expenses', 'public');
        }

        $validated['expense_number'] = $this->nextNumber();
        $validated['created_by'] = $this->user->id;

        $expense = Expense::create($validated);

        AuditLog::record('create', 'Expenses', $expense, "Recorded {$expense->category} expense of ₹{$expense->amount}");

        session()->flash('success', 'Expense recorded.');
        return redirect()->route('admin.expenses.index');
    }

    public function edit(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('expense.edit')) {
            abort(403, 'Sorry !! You are Unauthorized to edit Expenses !');
        }

        $expense = Expense::findOrFail($id);

        return view('backend.expenses.edit', compact('expense'));
    }

    public function update(Request $request, int $id)
    {
        if (is_null($this->user) || ! $this->user->can('expense.edit')) {
            abort(403);
        }

        $expense = Expense::findOrFail($id);
        $original = $expense->only(['category', 'amount']);

        $validated = $this->validateExpense($request);

        if ($request->hasFile('attachment')) {
            if ($expense->attachment) {
                Storage::disk('public')->delete($expense->attachment);
            }
            $validated['attachment'] = $request->file('attachment')->store('expenses', 'public');
        }

        $expense->update($validated);

        AuditLog::record('update', 'Expenses', $expense, "Updated expense {$expense->expense_number}", $original, $expense->only(['category', 'amount']));

        session()->flash('success', 'Expense updated.');
        return redirect()->route('admin.expenses.index');
    }

    public function destroy(int $id)
    {
        if (is_null($this->user) || ! $this->user->can('expense.delete')) {
            abort(403);
        }

        $expense = Expense::findOrFail($id);
        AuditLog::record('delete', 'Expenses', $expense, "Deleted expense {$expense->expense_number}");
        $expense->delete();

        session()->flash('success', 'Expense deleted.');
        return back();
    }

    private function validateExpense(Request $request): array
    {
        return $request->validate([
            'category' => 'required|in:Transport,Delivery,Warehouse,Salary,Electricity,Packaging,Maintenance,Other',
            'expense_date' => 'required|date',
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|in:cash,bank_transfer,card,cheque,online,other',
            'description' => 'nullable',
            'attachment' => 'nullable|file|max:5120',
        ]);
    }

    private function nextNumber(): string
    {
        $next = (Expense::max('id') ?? 0) + 1;
        return 'EXP-'.now()->format('ym').'-'.str_pad($next, 4, '0', STR_PAD_LEFT);
    }
}
