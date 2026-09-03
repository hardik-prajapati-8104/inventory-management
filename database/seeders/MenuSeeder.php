<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Seeder;

/**
 * Recreates the sidebar that used to be hard-coded in
 * `sidebar.blade.php` as rows in the `menus` table, so a fresh install
 * looks identical to before Menu Management existed — the sidebar is
 * dynamic now, but nothing about what it shows changes until an admin
 * edits it from Menu Management.
 *
 * Permissions here are copied from the first `->can(...)` check in each
 * item's target controller (e.g. Categories' index requires
 * 'spare-part.view', not 'category.view' — that's not a typo, the
 * controller genuinely reuses the spare-part permission), so a role that
 * couldn't open a page before still won't see it in the sidebar now.
 */
class MenuSeeder extends Seeder
{
    public function run(): void
    {
        // idempotent: running this twice (or after the sidebar has already
        // been customized) should not duplicate rows.
        if (Menu::count() > 0) {
            $this->command?->info('Menus already seeded — skipping. Delete all rows first if you want a clean reseed.');
            return;
        }

        $order = 0;
        $next = function () use (&$order) {
            $order += 10;
            return $order;
        };

        Menu::create(['type' => 'link', 'name' => 'Dashboard', 'icon' => 'bi-speedometer2', 'route_name' => 'admin.dashboard', 'permission' => 'dashboard.view', 'sort_order' => $next()]);

        Menu::create(['type' => 'heading', 'name' => 'Master Data', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Spare Parts', 'icon' => 'bi-gear-wide-connected', 'route_name' => 'admin.spare-parts.index', 'permission' => 'spare-part.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Categories', 'icon' => 'bi-tags', 'route_name' => 'admin.categories.index', 'permission' => 'spare-part.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Brands', 'icon' => 'bi-award', 'route_name' => 'admin.brands.index', 'permission' => 'spare-part.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Manufacturers', 'icon' => 'bi-building', 'route_name' => 'admin.manufacturers.index', 'permission' => 'spare-part.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Units', 'icon' => 'bi-rulers', 'route_name' => 'admin.units.index', 'permission' => 'spare-part.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Vehicle Management', 'icon' => 'bi-car-front', 'route_name' => 'admin.vehicles.index', 'permission' => 'vehicle.view', 'sort_order' => $next()]);

        Menu::create(['type' => 'heading', 'name' => 'Inventory', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Current Stock', 'icon' => 'bi-box-seam', 'route_name' => 'admin.stock.index', 'permission' => 'stock.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Stock Movement', 'icon' => 'bi-arrow-left-right', 'route_name' => 'admin.stock.movements', 'permission' => 'stock.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Stock Adjustment', 'icon' => 'bi-sliders', 'route_name' => 'admin.stock-adjustments.index', 'permission' => 'stock-adjustment.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Stock Transfer', 'icon' => 'bi-truck', 'route_name' => 'admin.stock-transfers.index', 'permission' => 'stock-transfer.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Stock Take', 'icon' => 'bi-clipboard-check', 'route_name' => 'admin.stock-takes.index', 'permission' => 'stock.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Low Stock', 'icon' => 'bi-exclamation-triangle', 'route_name' => 'admin.stock.low', 'permission' => 'stock.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Reorder Suggestions', 'icon' => 'bi-arrow-repeat', 'route_name' => 'admin.stock.reorder-suggestions', 'permission' => 'stock.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Damaged Stock', 'icon' => 'bi-x-octagon', 'route_name' => 'admin.stock.damaged', 'permission' => 'stock.view', 'sort_order' => $next()]);

        Menu::create(['type' => 'heading', 'name' => 'Purchases', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Suppliers', 'icon' => 'bi-people', 'route_name' => 'admin.suppliers.index', 'permission' => 'supplier.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Purchase Orders', 'icon' => 'bi-file-earmark-text', 'route_name' => 'admin.purchase-orders.index', 'permission' => 'purchase-order.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Goods Receipt', 'icon' => 'bi-box-arrow-in-down', 'route_name' => 'admin.goods-receipts.index', 'permission' => 'purchase.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Purchases', 'icon' => 'bi-cart-plus', 'route_name' => 'admin.purchases.index', 'permission' => 'purchase.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Purchase Returns', 'icon' => 'bi-arrow-counterclockwise', 'route_name' => 'admin.purchase-returns.index', 'permission' => 'purchase-return.view', 'sort_order' => $next()]);

        Menu::create(['type' => 'heading', 'name' => 'Sales', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Customers', 'icon' => 'bi-person-badge', 'route_name' => 'admin.customers.index', 'permission' => 'customer.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Sales', 'icon' => 'bi-cart-check', 'route_name' => 'admin.sales.index', 'permission' => 'sale.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Sales Returns', 'icon' => 'bi-arrow-return-left', 'route_name' => 'admin.sales-returns.index', 'permission' => 'sale-return.view', 'sort_order' => $next()]);

        Menu::create(['type' => 'heading', 'name' => 'Warehouses', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Warehouses', 'icon' => 'bi-building-gear', 'route_name' => 'admin.warehouses.index', 'permission' => 'warehouse.view', 'sort_order' => $next()]);

        Menu::create(['type' => 'heading', 'name' => 'Reports', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Stock Valuation', 'icon' => 'bi-graph-up', 'route_name' => 'admin.reports.stock-valuation', 'permission' => 'report.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Purchase Reports', 'icon' => 'bi-bar-chart', 'route_name' => 'admin.reports.purchases', 'permission' => 'report.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Sales Reports', 'icon' => 'bi-bar-chart-line', 'route_name' => 'admin.reports.sales', 'permission' => 'report.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Profit Reports', 'icon' => 'bi-piggy-bank', 'route_name' => 'admin.reports.profit', 'permission' => 'report.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Supplier Outstanding', 'icon' => 'bi-file-bar-graph', 'route_name' => 'admin.reports.outstanding-suppliers', 'permission' => 'report.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Customer Outstanding', 'icon' => 'bi-file-bar-graph', 'route_name' => 'admin.reports.outstanding-customers', 'permission' => 'report.view', 'sort_order' => $next()]);

        Menu::create(['type' => 'link', 'name' => 'Expenses', 'icon' => 'bi-receipt', 'route_name' => 'admin.expenses.index', 'permission' => 'expense.view', 'sort_order' => $next()]);

        Menu::create(['type' => 'heading', 'name' => 'Administration', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Users & Permissions', 'icon' => 'bi-person-gear', 'route_name' => 'admin.admin.index', 'permission' => 'admin.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Menu Management', 'icon' => 'bi-list-nested', 'route_name' => 'admin.menus.index', 'permission' => 'menu.view', 'sort_order' => $next()]);

        Menu::create(['type' => 'link', 'name' => 'Notifications', 'icon' => 'bi-bell', 'route_name' => 'admin.notifications.index', 'permission' => null, 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Audit Logs', 'icon' => 'bi-journal-text', 'route_name' => 'admin.audit-logs.index', 'permission' => 'audit-log.view', 'sort_order' => $next()]);
        Menu::create(['type' => 'link', 'name' => 'Settings', 'icon' => 'bi-gear', 'route_name' => 'admin.settings.index', 'permission' => 'settings.view', 'sort_order' => $next()]);
    }
}
