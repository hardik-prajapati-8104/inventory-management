<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleItem extends Model
{
    protected $fillable = ['sale_id', 'spare_part_id', 'quantity', 'selling_price', 'cost_price', 'discount', 'tax', 'total'];

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }

    public function getProfitAttribute(): float
    {
        return ($this->selling_price - $this->cost_price) * $this->quantity - $this->discount;
    }
}
