<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'code', 'manager', 'contact_number', 'address', 'city', 'country',
        'is_default', 'status',
    ];
    protected $casts = ['status' => 'boolean', 'is_default' => 'boolean'];

    public function zones()
    {
        return $this->hasMany(WarehouseZone::class);
    }

    public function racks()
    {
        return $this->hasMany(Rack::class);
    }

    public function stock()
    {
        return $this->hasMany(Stock::class);
    }

    public static function default(): ?self
    {
        return self::where('is_default', true)->first() ?? self::orderBy('id')->first();
    }
}
