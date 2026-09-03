<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ImportLog extends Model
{
    protected $fillable = ['type', 'source_type', 'original_filename', 'total_rows', 'imported_count', 'restocked_count', 'skipped_count', 'errors', 'created_by'];
    protected $casts = ['errors' => 'array'];

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }
}
