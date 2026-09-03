<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $table = 'stock';

    protected $fillable = ['spare_part_id', 'warehouse_id', 'current_stock', 'reserved_stock', 'damaged_stock'];

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function scopeLowStock($query)
    {
        return $query->whereHas('sparePart', fn ($q) => $q->whereColumn('spare_parts.minimum_stock', '>=', 'stock.current_stock'));
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', '<=', 0);
    }
}
