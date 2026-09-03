<?php

namespace Database\Factories;

use App\Models\SparePart;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

class SparePartFactory extends Factory
{
    protected $model = SparePart::class;

    public function definition(): array
    {
        $name = fake()->words(3, true);

        return [
            'part_number' => 'PN-'.strtoupper(fake()->unique()->bothify('####??')),
            'sku' => 'SKU-'.strtoupper(fake()->unique()->bothify('####??')),
            'name' => ucfirst($name),
            'slug' => Str::slug($name).'-'.Str::random(5),
            'purchase_price' => fake()->randomFloat(2, 100, 1000),
            'retail_price' => fake()->randomFloat(2, 150, 1500),
            'minimum_stock' => 10,
            'maximum_stock' => 100,
            'current_stock' => 0, // stock is always seeded through StockService, never set directly — see tests
            'status' => 'active',
        ];
    }
}
