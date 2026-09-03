<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class SparePart extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = [
        'part_number', 'sku', 'barcode', 'oem_number', 'alternate_number',
        'name', 'short_description', 'description',
        'category_id', 'sub_category_id', 'brand_id', 'manufacturer_id', 'unit_id', 'part_type',
        'purchase_price', 'wholesale_price', 'retail_price', 'min_selling_price', 'max_selling_price',
        'tax_percentage', 'discount_percentage',
        'opening_stock', 'current_stock', 'minimum_stock', 'maximum_stock', 'reorder_level',
        'reserved_stock', 'damaged_stock',
        'warehouse', 'rack', 'shelf', 'bin',
        'warehouse_id', 'rack_id', 'shelf_id', 'bin_id',
        'main_image', 'slug', 'seo_title', 'seo_description', 'keywords',
        'status', 'is_published', 'created_by',
    ];

    protected $casts = [
        'purchase_price' => 'decimal:2',
        'wholesale_price' => 'decimal:2',
        'retail_price' => 'decimal:2',
        'min_selling_price' => 'decimal:2',
        'max_selling_price' => 'decimal:2',
        'tax_percentage' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'is_published' => 'boolean',
    ];

    protected static function booted()
    {
        static::saving(function (SparePart $part) {
            if (empty($part->slug) && $part->name) {
                $part->slug = Str::slug($part->name).'-'.Str::random(5);
            }
        });
    }

    public function category()
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory()
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function manufacturer()
    {
        return $this->belongsTo(Manufacturer::class);
    }

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function images()
    {
        return $this->hasMany(SparePartImage::class)->orderBy('sort_order');
    }

    public function vehicles()
    {
        return $this->belongsToMany(VehicleVariant::class, 'spare_part_vehicle', 'spare_part_id', 'vehicle_variant_id')
            ->withPivot(['id', 'oem_number', 'position', 'notes'])
            ->withTimestamps();
    }

    public function createdBy()
    {
        return $this->belongsTo(Admin::class, 'created_by');
    }

    public function stock()
    {
        return $this->hasMany(Stock::class);
    }

    public function movements()
    {
        return $this->hasMany(StockMovement::class);
    }

    public function warehouseLocation()
    {
        return $this->belongsTo(Warehouse::class, 'warehouse_id');
    }

    public function rackLocation()
    {
        return $this->belongsTo(Rack::class, 'rack_id');
    }

    public function shelfLocation()
    {
        return $this->belongsTo(Shelf::class, 'shelf_id');
    }

    public function binLocation()
    {
        return $this->belongsTo(Bin::class, 'bin_id');
    }

    public function scopeLowStock($query)
    {
        return $query->whereColumn('current_stock', '<=', 'minimum_stock');
    }

    public function scopeOutOfStock($query)
    {
        return $query->where('current_stock', '<=', 0);
    }

    public function scopePublished($query)
    {
        return $query->where('is_published', true)->where('status', 'active');
    }

    public function getProfitPercentAttribute(): float
    {
        if (! $this->purchase_price || (float) $this->purchase_price == 0.0) {
            return 0;
        }

        return round((($this->retail_price - $this->purchase_price) / $this->purchase_price) * 100, 2);
    }
}
