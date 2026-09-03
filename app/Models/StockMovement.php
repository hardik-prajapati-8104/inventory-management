<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockMovement extends Model
{
    protected $fillable = [
        'spare_part_id', 'warehouse_id', 'type', 'quantity', 'stock_before', 'stock_after',
        'reference_type', 'reference_id', 'notes', 'created_by',
    ];

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function reference()
    {
        return $this->morphTo();
    }

    public static function inboundTypes(): array
    {
        return ['OPENING_STOCK', 'PURCHASE', 'SALES_RETURN', 'TRANSFER_IN', 'ADJUSTMENT_IN'];
    }

    public static function outboundTypes(): array
    {
        return ['SALE', 'PURCHASE_RETURN', 'TRANSFER_OUT', 'ADJUSTMENT_OUT', 'DAMAGE'];
    }
}
