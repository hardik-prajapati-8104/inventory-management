<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    protected $fillable = ['purchase_order_id', 'spare_part_id', 'quantity', 'quantity_received', 'purchase_price', 'discount', 'tax', 'total'];

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }

    public function getPendingQuantityAttribute(): int
    {
        return max(0, $this->quantity - $this->quantity_received);
    }
}
