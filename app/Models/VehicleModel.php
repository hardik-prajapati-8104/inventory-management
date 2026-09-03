<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleModel extends Model
{
    // Explicit table name — Laravel would otherwise guess "vehicle_models", which
    // happens to already be correct, but we pin it since the class name collides
    // conceptually with Eloquent's own "Model" base class.
    protected $table = 'vehicle_models';

    protected $fillable = ['vehicle_make_id', 'vehicle_type_id', 'name', 'status'];
    protected $casts = ['status' => 'boolean'];

    public function make()
    {
        return $this->belongsTo(VehicleMake::class, 'vehicle_make_id');
    }

    public function type()
    {
        return $this->belongsTo(VehicleType::class, 'vehicle_type_id');
    }

    public function variants()
    {
        return $this->hasMany(VehicleVariant::class);
    }
}
