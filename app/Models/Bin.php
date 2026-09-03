<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bin extends Model
{
    protected $fillable = ['shelf_id', 'name', 'status'];
    protected $casts = ['status' => 'boolean'];

    public function shelf()
    {
        return $this->belongsTo(Shelf::class);
    }
}
