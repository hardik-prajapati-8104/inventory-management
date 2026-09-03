<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    protected $fillable = [
        'po_number', 'supplier_id', 'po_date', 'expected_delivery_date', 'warehouse_id',
        'payment_terms', 'notes', 'status', 'created_by',
    ];
    protected $casts = ['po_date' => 'date', 'expected_delivery_date' => 'date'];

    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    public function goodsReceipts()
    {
        return $this->hasMany(GoodsReceipt::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function getTotalAttribute(): float
    {
        return (float) $this->items->sum('total');
    }

    /**
     * Recomputes header status from item-level received quantities — called
     * after every Goods Receipt confirmation against this PO.
     */
    public function refreshStatus(): void
    {
        $items = $this->items()->get();
        $fullyReceived = $items->every(fn ($i) => $i->quantity_received >= $i->quantity);
        $partiallyReceived = $items->contains(fn ($i) => $i->quantity_received > 0);

        $this->status = $fullyReceived ? 'received' : ($partiallyReceived ? 'partially_received' : $this->status);
        $this->save();
    }
}
