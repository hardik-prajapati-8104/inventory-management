<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GoodsReceiptItem extends Model
{
    protected $fillable = [
        'goods_receipt_id', 'purchase_order_item_id', 'spare_part_id',
        'quantity_ordered', 'quantity_received', 'quantity_damaged', 'quantity_short', 'remarks',
    ];

    public function goodsReceipt()
    {
        return $this->belongsTo(GoodsReceipt::class);
    }

    public function purchaseOrderItem()
    {
        return $this->belongsTo(PurchaseOrderItem::class);
    }

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }
}
