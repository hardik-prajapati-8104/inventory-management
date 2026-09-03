<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Shelf extends Model
{
    protected $fillable = ['rack_id', 'name', 'status'];
    protected $casts = ['status' => 'boolean'];

    public function rack()
    {
        return $this->belongsTo(Rack::class);
    }

    public function bins()
    {
        return $this->hasMany(Bin::class);
    }
}
