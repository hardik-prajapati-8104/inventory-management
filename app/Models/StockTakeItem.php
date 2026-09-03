<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTakeItem extends Model
{
    protected $fillable = ['stock_take_id', 'spare_part_id', 'system_quantity', 'counted_quantity', 'difference'];

    public function stockTake()
    {
        return $this->belongsTo(StockTake::class);
    }

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }
}
