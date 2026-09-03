<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleMake extends Model
{
    protected $fillable = ['name', 'logo', 'status'];
    protected $casts = ['status' => 'boolean'];

    public function models()
    {
        return $this->hasMany(VehicleModel::class);
    }
}
