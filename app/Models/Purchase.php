<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    protected $fillable = [
        'invoice_number', 'supplier_id', 'purchase_order_id', 'goods_receipt_id',
        'invoice_date', 'due_date', 'warehouse_id',
        'subtotal', 'discount_total', 'tax_total', 'grand_total', 'paid_amount', 'due_amount',
        'payment_status', 'stock_received_directly', 'notes', 'created_by',
    ];
    protected $casts = ['invoice_date' => 'date', 'due_date' => 'date', 'stock_received_directly' => 'boolean'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }

    public function payments()
    {
        return $this->hasMany(PurchasePayment::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
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

        if ($this->payment_status !== 'paid' && $this->due_date && $this->due_date->isPast()) {
            $this->payment_status = 'overdue';
        }

        $this->save();
    }
}
