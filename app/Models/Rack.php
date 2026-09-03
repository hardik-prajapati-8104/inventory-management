<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rack extends Model
{
    protected $fillable = ['warehouse_id', 'warehouse_zone_id', 'name', 'status'];
    protected $casts = ['status' => 'boolean'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function zone()
    {
        return $this->belongsTo(WarehouseZone::class, 'warehouse_zone_id');
    }

    public function shelves()
    {
        return $this->hasMany(Shelf::class);
    }
}
