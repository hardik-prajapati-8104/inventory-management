<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesReturnItem extends Model
{
    protected $fillable = ['sales_return_id', 'spare_part_id', 'quantity', 'return_reason', 'condition', 'refund_amount'];

    public function salesReturn()
    {
        return $this->belongsTo(SalesReturn::class);
    }

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }
}
