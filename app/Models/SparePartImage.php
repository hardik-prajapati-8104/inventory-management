<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SparePartImage extends Model
{
    protected $fillable = ['spare_part_id', 'path', 'sort_order'];

    public function sparePart()
    {
        return $this->belongsTo(SparePart::class);
    }
}
