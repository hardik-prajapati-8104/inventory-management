@php $supplier = $supplier ?? null; @endphp

<div class="row g-3">
    <div class="col-md-4">
        <label>Company Name<span class="text-error">*</span></label>
        <input type="text" name="company_name" required class="form-control" value="{{ old('company_name', $supplier->company_name ?? '') }}">
        @error('company_name') <div class="text-error">{{ $message }}</div> @enderror
    </div>
    <div class="col-md-4">
        <label>Supplier Code <span class="small text-muted">(auto if blank)</span></label>
        <input type="text" name="supplier_code" class="form-control" value="{{ old('supplier_code', $supplier->supplier_code ?? '') }}">
    </div>
    <div class="col-md-4">
        <label>Contact Person</label>
        <input type="text" name="contact_person" class="form-control" value="{{ old('contact_person', $supplier->contact_person ?? '') }}">
    </div>

    <div class="col-md-3">
        <label>Mobile</label>
        <input type="text" name="mobile" class="form-control" value="{{ old('mobile', $supplier->mobile ?? '') }}">
    </div>
    <div class="col-md-3">
        <label>WhatsApp</label>
        <input type="text" name="whatsapp" class="form-control" value="{{ old('whatsapp', $supplier->whatsapp ?? '') }}">
    </div>
    <div class="col-md-3">
        <label>Email</label>
        <input type="email" name="email" class="form-control" value="{{ old('email', $supplier->email ?? '') }}">
    </div>
    <div class="col-md-3">
        <label>Tax / VAT Number</label>
        <input type="text" name="tax_number" class="form-control" value="{{ old('tax_number', $supplier->tax_number ?? '') }}">
    </div>

    <div class="col-md-8">
        <label>Address</label>
        <textarea name="address" class="form-control">{{ old('address', $supplier->address ?? '') }}</textarea>
    </div>
    <div class="col-md-2">
        <label>City</label>
        <input type="text" name="city" class="form-control" value="{{ old('city', $supplier->city ?? '') }}">
    </div>
    <div class="col-md-2">
        <label>Country</label>
        <input type="text" name="country" class="form-control" value="{{ old('country', $supplier->country ?? '') }}">
    </div>

    <div class="col-md-3">
        <label>Opening Balance</label>
        <input type="number" step="0.01" name="opening_balance" class="form-control" value="{{ old('opening_balance', $supplier->opening_balance ?? 0) }}">
    </div>
    <div class="col-md-3">
        <label>Credit Limit</label>
        <input type="number" step="0.01" name="credit_limit" class="form-control" value="{{ old('credit_limit', $supplier->credit_limit ?? '') }}">
    </div>
    <div class="col-md-3">
        <label>Payment Terms</label>
        <input type="text" name="payment_terms" class="form-control" placeholder="e.g. Net 30" value="{{ old('payment_terms', $supplier->payment_terms ?? '') }}">
    </div>
    <div class="col-md-3">
        <label>Status</label>
        <select name="status" class="form-select">
            <option value="1" {{ old('status', $supplier->status ?? 1) == 1 ? 'selected' : '' }}>Active</option>
            <option value="0" {{ old('status', $supplier->status ?? 1) == 0 ? 'selected' : '' }}>Inactive</option>
        </select>
    </div>

    <div class="col-md-6">
        <label>Bank Details</label>
        <textarea name="bank_details" class="form-control">{{ old('bank_details', $supplier->bank_details ?? '') }}</textarea>
    </div>
    <div class="col-md-6">
        <label>Notes</label>
        <textarea name="notes" class="form-control">{{ old('notes', $supplier->notes ?? '') }}</textarea>
    </div>
</div>
