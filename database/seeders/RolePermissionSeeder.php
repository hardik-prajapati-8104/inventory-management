<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Cache;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Module.action pattern, guard 'admin'. Modules not yet built (spare-part,
        // vehicle, supplier, customer, purchase, sale, inventory, warehouse, report,
        // expense) are seeded now so roles can be assigned to them ahead of Phase 2+.
        $modules = [
            'dashboard' => ['view'],
            'admin' => ['view', 'create', 'edit', 'delete'],
            'role' => ['view', 'create', 'edit', 'delete'],
            'settings' => ['view', 'edit'],
            'audit-log' => ['view'],

            'spare-part' => ['view', 'create', 'edit', 'delete', 'import', 'export'],
            'vehicle' => ['view', 'create', 'edit', 'delete'],
            'supplier' => ['view', 'create', 'edit', 'delete'],
            'customer' => ['view', 'create', 'edit', 'delete'],

            'purchase-order' => ['view', 'create', 'edit', 'delete', 'approve'],
            'purchase' => ['view', 'create', 'edit', 'delete'],
            'purchase-return' => ['view', 'create', 'edit', 'delete', 'approve'],

            'sale' => ['view', 'create', 'edit', 'delete'],
            'sale-return' => ['view', 'create', 'edit', 'delete', 'approve'],

            'stock' => ['view'],
            'stock-adjustment' => ['view', 'create', 'approve'],
            'stock-transfer' => ['view', 'create', 'approve'],
            'warehouse' => ['view', 'create', 'edit', 'delete'],

            'report' => ['view'],
            'expense' => ['view', 'create', 'edit', 'delete'],
        ];

        foreach ($modules as $module => $actions) {
            foreach ($actions as $action) {
                Permission::firstOrCreate(['name' => "$module.$action", 'guard_name' => 'admin']);
            }
        }

        $allPermissionNames = Permission::where('guard_name', 'admin')->pluck('name')->toArray();

        // Super Admin — every permission, plus is_super_admin=1 on the Admin model
        // bypasses checks entirely via Gate::before (see AuthServiceProvider).
        $superAdmin = Role::firstOrCreate(['name' => 'Super Admin', 'guard_name' => 'admin']);
        $superAdmin->syncPermissions($allPermissionNames);

        Role::firstOrCreate(['name' => 'Admin', 'guard_name' => 'admin'])
            ->syncPermissions(array_filter($allPermissionNames, fn ($p) => ! str_starts_with($p, 'settings.') && ! str_starts_with($p, 'role.')));

        Role::firstOrCreate(['name' => 'Inventory Manager', 'guard_name' => 'admin'])
            ->syncPermissions(Permission::where('guard_name', 'admin')
                ->where(function ($q) {
                    $q->where('name', 'like', 'spare-part.%')
                        ->orWhere('name', 'like', 'stock%')
                        ->orWhere('name', 'like', 'warehouse.%')
                        ->orWhere('name', 'like', 'report.%')
                        ->orWhere('name', 'dashboard.view');
                })->pluck('name')->toArray());

        Role::firstOrCreate(['name' => 'Purchase Manager', 'guard_name' => 'admin'])
            ->syncPermissions(Permission::where('guard_name', 'admin')
                ->where(function ($q) {
                    $q->where('name', 'like', 'supplier.%')
                        ->orWhere('name', 'like', 'purchase%')
                        ->orWhere('name', 'like', 'report.%')
                        ->orWhere('name', 'dashboard.view');
                })->pluck('name')->toArray());

        Role::firstOrCreate(['name' => 'Sales Manager', 'guard_name' => 'admin'])
            ->syncPermissions(Permission::where('guard_name', 'admin')
                ->where(function ($q) {
                    $q->where('name', 'like', 'customer.%')
                        ->orWhere('name', 'like', 'sale%')
                        ->orWhere('name', 'like', 'report.%')
                        ->orWhere('name', 'dashboard.view');
                })->pluck('name')->toArray());

        Role::firstOrCreate(['name' => 'Data Entry Operator', 'guard_name' => 'admin'])
            ->syncPermissions([
                'dashboard.view',
                'spare-part.view', 'spare-part.create', 'spare-part.edit',
                'customer.view', 'customer.create',
                'supplier.view', 'supplier.create',
                'purchase.view', 'purchase.create',
                'sale.view', 'sale.create',
            ]);

        Cache::forget('admins');
    }
}
