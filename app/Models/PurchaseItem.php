<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseItem extends Model
{
    protected $fillable = ['purchase_id', 'spare_part_id', 'quantity', 'purchase_price', 'discount', 'tax', 'total'];

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }
}
