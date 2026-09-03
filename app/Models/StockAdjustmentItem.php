<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockAdjustmentItem extends Model
{
    protected $fillable = ['stock_adjustment_id', 'spare_part_id', 'current_quantity', 'adjustment_type', 'adjustment_quantity'];

    public function adjustment()
    {
        return $this->belongsTo(StockAdjustment::class, 'stock_adjustment_id');
    }

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }
}
