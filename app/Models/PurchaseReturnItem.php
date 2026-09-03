<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReturnItem extends Model
{
    protected $fillable = ['purchase_return_id', 'spare_part_id', 'quantity', 'reason', 'amount'];

    public function purchaseReturn()
    {
        return $this->belongsTo(PurchaseReturn::class);
    }

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }
}
