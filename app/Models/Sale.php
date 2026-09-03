<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    protected $fillable = [
        'invoice_number', 'customer_id', 'invoice_date', 'warehouse_id', 'salesperson_id',
        'subtotal', 'discount_total', 'tax_total', 'grand_total', 'cost_total',
        'paid_amount', 'due_amount', 'payment_status', 'notes', 'created_by',
    ];
    protected $casts = ['invoice_date' => 'date'];

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function salesperson()
    {
        return $this->belongsTo(Admin::class, 'salesperson_id');
    }

    public function items()
    {
        return $this->hasMany(SaleItem::class);
    }

    public function payments()
    {
        return $this->hasMany(CustomerPayment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function getProfitAttribute(): float
    {
        return (float) $this->grand_total - (float) $this->cost_total - (float) $this->tax_total;
    }

    public function refreshPaymentStatus(): void
    {
        $this->paid_amount = (float) $this->payments()->sum('amount');
        $this->due_amount = max(0, (float) $this->grand_total - $this->paid_amount);

        $this->payment_status = match (true) {
            $this->paid_amount <= 0 => 'unpaid',
            $this->due_amount <= 0 => 'paid',
            default => 'partially_paid',
        };

        $this->save();
    }
}
