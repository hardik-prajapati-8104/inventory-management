<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'supplier_code', 'company_name', 'contact_person', 'mobile', 'whatsapp', 'email',
        'address', 'city', 'country', 'tax_number', 'opening_balance', 'credit_limit',
        'payment_terms', 'bank_details', 'notes', 'status',
    ];
    protected $casts = ['status' => 'boolean', 'opening_balance' => 'decimal:2', 'credit_limit' => 'decimal:2'];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function purchaseReturns()
    {
        return $this->hasMany(PurchaseReturn::class);
    }

    /**
     * Outstanding balance: opening balance + every unpaid invoice's due amount
     * minus returns not yet reflected in an invoice. Simple sum for MVP —
     * a dedicated supplier_ledger view can replace this once volumes grow.
     */
    public function getOutstandingBalanceAttribute(): float
    {
        return (float) $this->opening_balance + (float) $this->purchases()->sum('due_amount');
    }
}
