<?php

namespace Database\Seeders;

use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run(): void
    {
        $admin = Admin::firstOrCreate(
            ['email' => 'admin@vsp.local'],
            [
                'first_name' => 'Super',
                'last_name' => 'Admin',
                'username' => 'superadmin',
                'password' => Hash::make('Password@123'), // change immediately after first login
                'status' => 1,
                'login' => 1,
                'is_super_admin' => 1,
            ]
        );

        $admin->assignRole('Super Admin');
    }
}
