@php $expense = $expense ?? null; @endphp

<div class="row g-3">
    <div class="col-md-4">
        <label>Category<span class="text-error">*</span></label>
        <select name="category" required class="form-select">
            @foreach (['Transport','Delivery','Warehouse','Salary','Electricity','Packaging','Maintenance','Other'] as $cat)
                <option value="{{ $cat }}" {{ old('category', $expense->category ?? '') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-4">
        <label>Date<span class="text-error">*</span></label>
        <input type="date" name="expense_date" required class="form-control" value="{{ old('expense_date', $expense->expense_date->format('Y-m-d') ?? date('Y-m-d')) }}">
    </div>
    <div class="col-md-4">
        <label>Amount<span class="text-error">*</span></label>
        <div class="input-group">
            <span class="input-group-text">₹</span>
            <input type="number" step="0.01" name="amount" required class="form-control" value="{{ old('amount', $expense->amount ?? '') }}">
        </div>
    </div>
    <div class="col-md-4">
        <label>Payment Method<span class="text-error">*</span></label>
        <select name="payment_method" required class="form-select">
            @foreach (['cash' => 'Cash', 'bank_transfer' => 'Bank Transfer', 'card' => 'Card', 'cheque' => 'Cheque', 'online' => 'Online', 'other' => 'Other'] as $val => $label)
                <option value="{{ $val }}" {{ old('payment_method', $expense->payment_method ?? '') == $val ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-md-8">
        <label>Attachment (receipt/invoice)</label>
        <input type="file" name="attachment" class="form-control">
        @if (isset($expense) && $expense->attachment)
            <div class="form-text"><a href="{{ asset('storage/'.$expense->attachment) }}" target="_blank">View current attachment</a></div>
        @endif
    </div>
    <div class="col-12">
        <label>Description</label>
        <textarea name="description" class="form-control">{{ old('description', $expense->description ?? '') }}</textarea>
    </div>
</div>
