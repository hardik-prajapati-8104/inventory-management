<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VehicleVariant extends Model
{
    protected $fillable = [
        'vehicle_model_id', 'name', 'generation', 'engine_type', 'engine_capacity',
        'fuel_type', 'transmission', 'drive_type', 'start_year', 'end_year', 'status',
    ];
    protected $casts = ['status' => 'boolean'];

    public function model_()
    {
        return $this->belongsTo(VehicleModel::class, 'vehicle_model_id');
    }

    public function spareParts()
    {
        return $this->belongsToMany(SparePart::class, 'spare_part_vehicle')
            ->withPivot(['oem_number', 'position', 'notes'])
            ->withTimestamps();
    }

    /**
     * Human readable label used in Select2 dropdowns and the compatibility list,
     * e.g. "Toyota Corolla 1.8L (2018-2020)".
     */
    public function getLabelAttribute(): string
    {
        $make = $this->model_->make->name ?? '';
        $model = $this->model_->name ?? '';
        $years = $this->start_year
            ? " ({$this->start_year}".($this->end_year ? "-{$this->end_year}" : '+').')'
            : '';

        return trim("{$make} {$model} {$this->name}{$years}");
    }
}
