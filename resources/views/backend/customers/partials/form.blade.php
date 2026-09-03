@php $customer = $customer ?? null; @endphp

<div class="row g-3">
    <div class="col-md-4">
        <label>Customer Name<span class="text-error">*</span></label>
        <input type="text" name="customer_name" required class="form-control" value="{{ old('customer_name', $customer->customer_name ?? '') }}">
        @error('customer_name') <div class="text-error">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label>Customer Code <span class="small text-muted">(auto if blank)</span></label>
        <input type="text" name="customer_code" class="form-control" value="{{ old('customer_code', $customer->customer_code ?? '') }}">
    </div>
    <div class="col-md-4">
        <label>Company Name</label>
        <input type="text" name="company_name" class="form-control" value="{{ old('company_name', $customer->company_name ?? '') }}">
    </div>

    <div class="col-md-3">
        <label>Mobile</label>
        <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $customer->mobile ?? '') }}">
    </div>
    <div class="col-md-3">
        <label>WhatsApp</label>
        <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $customer->whatsapp ?? '') }}">
    </div>
    <div class="col-md-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $customer->email ?? '') }}">
    </div>
    <div class="col-md-3">
        <label>Tax / VAT Number</label>
        <input type="text" name="tax_number" class="form-control" value="{{ old('tax_number', $customer->tax_number ?? '') }}">
    </div>

    <div class="col-md-8">
        <label>Address</label>
        <textarea name="address" class="form-control">{{ old('address', $customer->address ?? '') }}</textarea>
    </div>
    <div class="col-md-2">
        <label>City</label>
        <input type="text" name="city" class="form-control" value="{{ old('city', $customer->city ?? '') }}">
    </div>
    <div class="col-md-2">
        <label>Country</label>
        <input type="text" name="country" class="form-control" value="{{ old('country', $customer->country ?? '') }}">
    </div>

    <div class="col-md-3">
        <label>Opening Balance</label>
        <input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ old('opening_balance', $customer->opening_balance ?? 0) }}">
    </div>
    <div class="col-md-3">
        <label>Credit Limit</label>
        <input type="number" step="0.01" name="credit_limit" class="form-control" value="{{ old('credit_limit', $customer->credit_limit ?? '') }}">
    </div>
    <div class="col-md-3">
        <label>Standing Discount %</label>
        <input type="number" step="0.01" name="discount_percentage" class="form-control" value="{{ old('discount_percentage', $customer->discount_percentage ?? 0) }}">
    </div>
    <div class="col-md-3">
        <label>Status</label>
        <select name="status" class="form-select">
            <option value="1" {{ old('status', $customer->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('status', $customer->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    <div class="col-12">
        <label>Notes</label>
        <textarea name="notes" class="form-control">{{ old('notes', $customer->notes ?? '') }}</textarea>
    </div>
</div>
