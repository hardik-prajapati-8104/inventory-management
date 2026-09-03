<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Unit;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            'Engine Parts', 'Brake Parts', 'Suspension Parts', 'Electrical Parts',
            'Body Parts', 'Steering Parts', 'Transmission Parts', 'Cooling System',
            'AC Parts', 'Filters', 'Accessories',
        ];

        foreach ($categories as $name) {
            Category::firstOrCreate(['name' => $name]);
        }

        $units = [
            ['name' => 'Piece', 'short_code' => 'pcs'],
            ['name' => 'Set', 'short_code' => 'set'],
            ['name' => 'Pair', 'short_code' => 'pr'],
            ['name' => 'Box', 'short_code' => 'box'],
            ['name' => 'Kit', 'short_code' => 'kit'],
            ['name' => 'Liter', 'short_code' => 'ltr'],
            ['name' => 'Meter', 'short_code' => 'm'],
        ];

        foreach ($units as $unit) {
            Unit::firstOrCreate(['name' => $unit['name']], $unit);
        }
    }
}
