<?php

use App\Http\Controllers\Backend\Auth\LoginController;
use App\Http\Controllers\Backend\AdminsController;
use App\Http\Controllers\Backend\AuditLogController;
use App\Http\Controllers\Backend\BarcodeController;
use App\Http\Controllers\Backend\BrandsController;
use App\Http\Controllers\Backend\CategoriesController;
use App\Http\Controllers\Backend\CustomersController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\ExpensesController;
use App\Http\Controllers\Backend\GoodsReceiptsController;
use App\Http\Controllers\Backend\ManufacturersController;
use App\Http\Controllers\Backend\MenusController;
use App\Http\Controllers\Backend\NotificationsController;
use App\Http\Controllers\Backend\PurchaseOrdersController;
use App\Http\Controllers\Backend\PurchaseReturnsController;
use App\Http\Controllers\Backend\PurchasesController;
use App\Http\Controllers\Backend\ReportsController;
use App\Http\Controllers\Backend\SalesController;
use App\Http\Controllers\Backend\SalesReturnsController;
use App\Http\Controllers\Backend\SettingsController;
use App\Http\Controllers\Backend\SparePartsController;
use App\Http\Controllers\Backend\SparePartsImportController;
use App\Http\Controllers\Backend\StockAdjustmentsController;
use App\Http\Controllers\Backend\StockController;
use App\Http\Controllers\Backend\StockTakesController;
use App\Http\Controllers\Backend\StockTransfersController;
use App\Http\Controllers\Backend\SuppliersController;
use App\Http\Controllers\Backend\UnitsController;
use App\Http\Controllers\Backend\VehiclesController;
use App\Http\Controllers\Backend\WarehousesController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Admin (Backend) Routes
|--------------------------------------------------------------------------
| Register this file from bootstrap/app.php (Laravel 13):
|
|   ->withRouting(
|       web: __DIR__.'/../routes/web.php',
|       then: function () {
|           Route::middleware('web')->prefix('admin')->name('admin.')
|               ->group(base_path('routes/admin.php'));
|       },
|   )
|
| Note the prefix/name group above already exists, so this file does NOT
| repeat ->prefix('admin')->name('admin.') itself.
*/

Route::middleware('guest:admin')->group(function () {
    Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('login', [LoginController::class, 'login'])->name('login.submit');
});

Route::middleware('auth:admin')->group(function () {
    Route::post('logout', [LoginController::class, 'logout'])->name('logout');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');

    Route::get('change-password', [AdminsController::class, 'changePassword'])->name('change-password');

    Route::get('notifications', [NotificationsController::class, 'index'])->name('notifications.index');
    Route::post('notifications/send-digest-now', [NotificationsController::class, 'sendDigestNow'])->name('notifications.send-digest-now');

    Route::post('update-field-status/{table}/{id}/{status}/{field?}', [AdminsController::class, 'updateFieldStatus'])
        ->name('update-field-status');

    Route::resource('admin', AdminsController::class)->except(['show']);

    Route::get('settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::post('settings', [SettingsController::class, 'update'])->name('settings.update');

    Route::get('audit-logs', [AuditLogController::class, 'index'])->name('audit-logs.index');

    /*
    |--------------------------------------------------------------------
    | Menu Management — dynamic, permission-based, drag-and-drop sidebar
    |--------------------------------------------------------------------
    */
    Route::get('menus', [MenusController::class, 'index'])->name('menus.index');
    Route::get('menus/create', [MenusController::class, 'create'])->name('menus.create');
    Route::post('menus', [MenusController::class, 'store'])->name('menus.store');
    Route::get('menus/check-route', [MenusController::class, 'checkRoute'])->name('menus.check-route');
    Route::post('menus/reorder', [MenusController::class, 'reorder'])->name('menus.reorder');
    Route::get('menus/{id}/edit', [MenusController::class, 'edit'])->name('menus.edit');
    Route::put('menus/{id}', [MenusController::class, 'update'])->name('menus.update');
    Route::delete('menus/{id}', [MenusController::class, 'destroy'])->name('menus.destroy');

    /*
    |--------------------------------------------------------------------
    | Phase 2 — Master Data
    |--------------------------------------------------------------------
    */
    Route::get('categories', [CategoriesController::class, 'index'])->name('categories.index');
    Route::post('categories', [CategoriesController::class, 'store'])->name('categories.store');
    Route::put('categories/{id}', [CategoriesController::class, 'update'])->name('categories.update');
    Route::delete('categories/{id}', [CategoriesController::class, 'destroy'])->name('categories.destroy');

    Route::get('brands', [BrandsController::class, 'index'])->name('brands.index');
    Route::post('brands', [BrandsController::class, 'store'])->name('brands.store');
    Route::put('brands/{id}', [BrandsController::class, 'update'])->name('brands.update');
    Route::delete('brands/{id}', [BrandsController::class, 'destroy'])->name('brands.destroy');

    Route::get('manufacturers', [ManufacturersController::class, 'index'])->name('manufacturers.index');
    Route::post('manufacturers', [ManufacturersController::class, 'store'])->name('manufacturers.store');
    Route::put('manufacturers/{id}', [ManufacturersController::class, 'update'])->name('manufacturers.update');
    Route::delete('manufacturers/{id}', [ManufacturersController::class, 'destroy'])->name('manufacturers.destroy');

    Route::get('units', [UnitsController::class, 'index'])->name('units.index');
    Route::post('units', [UnitsController::class, 'store'])->name('units.store');
    Route::put('units/{id}', [UnitsController::class, 'update'])->name('units.update');
    Route::delete('units/{id}', [UnitsController::class, 'destroy'])->name('units.destroy');

    Route::get('vehicles', [VehiclesController::class, 'index'])->name('vehicles.index');
    Route::post('vehicles/makes', [VehiclesController::class, 'storeMake'])->name('vehicles.makes.store');
    Route::get('vehicles/makes/{make}/models', [VehiclesController::class, 'modelsForMake'])->name('vehicles.makes.models');
    Route::post('vehicles/models', [VehiclesController::class, 'storeModel'])->name('vehicles.models.store');
    Route::post('vehicles/variants', [VehiclesController::class, 'storeVariant'])->name('vehicles.variants.store');
    Route::delete('vehicles/variants/{id}', [VehiclesController::class, 'destroyVariant'])->name('vehicles.variants.destroy');
    Route::get('vehicles/search', [VehiclesController::class, 'searchVariants'])->name('vehicles.search');

    Route::get('spare-parts', [SparePartsController::class, 'index'])->name('spare-parts.index');
    Route::get('spare-parts/create', [SparePartsController::class, 'create'])->name('spare-parts.create');
    Route::post('spare-parts', [SparePartsController::class, 'store'])->name('spare-parts.store');
    Route::get('spare-parts/check-duplicate', [SparePartsController::class, 'checkDuplicate'])->name('spare-parts.check-duplicate');
    Route::get('spare-parts/lookup-by-code', [SparePartsController::class, 'lookupByCode'])->name('spare-parts.lookup-by-code');

    Route::get('spare-parts/import', [SparePartsImportController::class, 'create'])->name('spare-parts.import.create');
    Route::get('spare-parts/import/template', [SparePartsImportController::class, 'template'])->name('spare-parts.import.template');
    Route::post('spare-parts/import/preview', [SparePartsImportController::class, 'preview'])->name('spare-parts.import.preview');
    Route::post('spare-parts/import/confirm', [SparePartsImportController::class, 'confirm'])->name('spare-parts.import.confirm');

    Route::get('spare-parts/{id}/duplicate', [SparePartsController::class, 'duplicate'])->name('spare-parts.duplicate');
    Route::get('spare-parts/{id}/edit', [SparePartsController::class, 'edit'])->name('spare-parts.edit');
    Route::put('spare-parts/{id}', [SparePartsController::class, 'update'])->name('spare-parts.update');
    Route::delete('spare-parts/{id}', [SparePartsController::class, 'destroy'])->name('spare-parts.destroy');

    Route::get('spare-parts/barcodes/bulk', [BarcodeController::class, 'bulk'])->name('spare-parts.barcodes.bulk');
    Route::get('spare-parts/{id}/barcode', [BarcodeController::class, 'show'])->name('spare-parts.barcode');

    /*
    |--------------------------------------------------------------------
    | Phase 3 — Inventory
    |--------------------------------------------------------------------
    */
    Route::get('warehouses', [WarehousesController::class, 'index'])->name('warehouses.index');
    Route::post('warehouses', [WarehousesController::class, 'store'])->name('warehouses.store');
    Route::put('warehouses/{id}', [WarehousesController::class, 'update'])->name('warehouses.update');
    Route::delete('warehouses/{id}', [WarehousesController::class, 'destroy'])->name('warehouses.destroy');
    Route::post('warehouses/zones', [WarehousesController::class, 'storeZone'])->name('warehouses.zones.store');
    Route::post('warehouses/racks', [WarehousesController::class, 'storeRack'])->name('warehouses.racks.store');
    Route::post('warehouses/shelves', [WarehousesController::class, 'storeShelf'])->name('warehouses.shelves.store');
    Route::post('warehouses/bins', [WarehousesController::class, 'storeBin'])->name('warehouses.bins.store');
    Route::get('warehouses/ajax/racks-for/{warehouse}', [WarehousesController::class, 'racksForWarehouse'])->name('warehouses.ajax.racks');
    Route::get('warehouses/ajax/shelves-for/{rack}', [WarehousesController::class, 'shelvesForRack'])->name('warehouses.ajax.shelves');
    Route::get('warehouses/ajax/bins-for/{shelf}', [WarehousesController::class, 'binsForShelf'])->name('warehouses.ajax.bins');

    Route::get('stock', [StockController::class, 'index'])->name('stock.index');
    Route::get('stock-movements', [StockController::class, 'movements'])->name('stock.movements');
    Route::get('low-stock', [StockController::class, 'lowStock'])->name('stock.low');
    Route::get('out-of-stock', [StockController::class, 'outOfStock'])->name('stock.out');
    Route::get('damaged-stock', [StockController::class, 'damagedStock'])->name('stock.damaged');
    Route::get('reorder-suggestions', [StockController::class, 'reorderSuggestions'])->name('stock.reorder-suggestions');
    Route::post('reorder-suggestions/convert', [StockController::class, 'convertReorderToPO'])->name('stock.reorder-suggestions.convert');

    Route::get('stock-adjustments', [StockAdjustmentsController::class, 'index'])->name('stock-adjustments.index');
    Route::get('stock-adjustments/create', [StockAdjustmentsController::class, 'create'])->name('stock-adjustments.create');
    Route::post('stock-adjustments', [StockAdjustmentsController::class, 'store'])->name('stock-adjustments.store');
    Route::post('stock-adjustments/{id}/approve', [StockAdjustmentsController::class, 'approve'])->name('stock-adjustments.approve');
    Route::post('stock-adjustments/{id}/reject', [StockAdjustmentsController::class, 'reject'])->name('stock-adjustments.reject');

    Route::get('stock-transfers', [StockTransfersController::class, 'index'])->name('stock-transfers.index');
    Route::get('stock-transfers/create', [StockTransfersController::class, 'create'])->name('stock-transfers.create');
    Route::post('stock-transfers', [StockTransfersController::class, 'store'])->name('stock-transfers.store');
    Route::post('stock-transfers/{id}/approve', [StockTransfersController::class, 'approve'])->name('stock-transfers.approve');
    Route::post('stock-transfers/{id}/cancel', [StockTransfersController::class, 'cancel'])->name('stock-transfers.cancel');

    Route::get('stock-takes', [StockTakesController::class, 'index'])->name('stock-takes.index');
    Route::get('stock-takes/create', [StockTakesController::class, 'create'])->name('stock-takes.create');
    Route::post('stock-takes', [StockTakesController::class, 'store'])->name('stock-takes.store');
    Route::get('stock-takes/{id}/count', [StockTakesController::class, 'count'])->name('stock-takes.count');
    Route::post('stock-takes/{id}/count', [StockTakesController::class, 'saveCounts'])->name('stock-takes.save-counts');
    Route::post('stock-takes/{id}/approve', [StockTakesController::class, 'approve'])->name('stock-takes.approve');

    /*
    |--------------------------------------------------------------------
    | Phase 4 — Purchasing
    |--------------------------------------------------------------------
    */
    Route::get('suppliers', [SuppliersController::class, 'index'])->name('suppliers.index');
    Route::get('suppliers/create', [SuppliersController::class, 'create'])->name('suppliers.create');
    Route::post('suppliers', [SuppliersController::class, 'store'])->name('suppliers.store');
    Route::get('suppliers/{id}/edit', [SuppliersController::class, 'edit'])->name('suppliers.edit');
    Route::put('suppliers/{id}', [SuppliersController::class, 'update'])->name('suppliers.update');
    Route::delete('suppliers/{id}', [SuppliersController::class, 'destroy'])->name('suppliers.destroy');
    Route::get('suppliers/{id}/ledger', [SuppliersController::class, 'ledger'])->name('suppliers.ledger');

    Route::get('purchase-orders', [PurchaseOrdersController::class, 'index'])->name('purchase-orders.index');
    Route::get('purchase-orders/create', [PurchaseOrdersController::class, 'create'])->name('purchase-orders.create');
    Route::post('purchase-orders', [PurchaseOrdersController::class, 'store'])->name('purchase-orders.store');
    Route::get('purchase-orders/{id}', [PurchaseOrdersController::class, 'show'])->name('purchase-orders.show');
    Route::post('purchase-orders/{id}/approve', [PurchaseOrdersController::class, 'approve'])->name('purchase-orders.approve');
    Route::post('purchase-orders/{id}/cancel', [PurchaseOrdersController::class, 'cancel'])->name('purchase-orders.cancel');

    Route::get('goods-receipts', [GoodsReceiptsController::class, 'index'])->name('goods-receipts.index');
    Route::get('purchase-orders/{purchaseOrder}/receive', [GoodsReceiptsController::class, 'create'])->name('goods-receipts.create');
    Route::post('purchase-orders/{purchaseOrder}/receive', [GoodsReceiptsController::class, 'store'])->name('goods-receipts.store');
    Route::get('goods-receipts/{id}', [GoodsReceiptsController::class, 'show'])->name('goods-receipts.show');

    Route::get('purchases', [PurchasesController::class, 'index'])->name('purchases.index');
    Route::get('purchases/create', [PurchasesController::class, 'create'])->name('purchases.create');
    Route::post('purchases', [PurchasesController::class, 'store'])->name('purchases.store');
    Route::get('purchases/{id}', [PurchasesController::class, 'show'])->name('purchases.show');
    Route::post('purchases/{id}/payments', [PurchasesController::class, 'storePayment'])->name('purchases.payments.store');

    Route::get('purchase-returns', [PurchaseReturnsController::class, 'index'])->name('purchase-returns.index');
    Route::get('purchase-returns/create', [PurchaseReturnsController::class, 'create'])->name('purchase-returns.create');
    Route::post('purchase-returns', [PurchaseReturnsController::class, 'store'])->name('purchase-returns.store');
    Route::post('purchase-returns/{id}/approve', [PurchaseReturnsController::class, 'approve'])->name('purchase-returns.approve');

    /*
    |--------------------------------------------------------------------
    | Phase 5 — Sales
    |--------------------------------------------------------------------
    */
    Route::get('customers', [CustomersController::class, 'index'])->name('customers.index');
    Route::get('customers/create', [CustomersController::class, 'create'])->name('customers.create');
    Route::post('customers', [CustomersController::class, 'store'])->name('customers.store');
    Route::get('customers/{id}/edit', [CustomersController::class, 'edit'])->name('customers.edit');
    Route::put('customers/{id}', [CustomersController::class, 'update'])->name('customers.update');
    Route::delete('customers/{id}', [CustomersController::class, 'destroy'])->name('customers.destroy');
    Route::get('customers/{id}/ledger', [CustomersController::class, 'ledger'])->name('customers.ledger');

    Route::get('sales', [SalesController::class, 'index'])->name('sales.index');
    Route::get('sales/create', [SalesController::class, 'create'])->name('sales.create');
    Route::post('sales', [SalesController::class, 'store'])->name('sales.store');
    Route::get('sales/{id}', [SalesController::class, 'show'])->name('sales.show');
    Route::post('sales/{id}/payments', [SalesController::class, 'storePayment'])->name('sales.payments.store');

    Route::get('sales-returns', [SalesReturnsController::class, 'index'])->name('sales-returns.index');
    Route::get('sales-returns/create', [SalesReturnsController::class, 'create'])->name('sales-returns.create');
    Route::post('sales-returns', [SalesReturnsController::class, 'store'])->name('sales-returns.store');
    Route::post('sales-returns/{id}/approve', [SalesReturnsController::class, 'approve'])->name('sales-returns.approve');

    /*
    |--------------------------------------------------------------------
    | Phase 6 — Reports
    |--------------------------------------------------------------------
    */
    Route::get('reports', [ReportsController::class, 'index'])->name('reports.index');
    Route::get('reports/stock-valuation', [ReportsController::class, 'stockValuation'])->name('reports.stock-valuation');
    Route::get('reports/purchases', [ReportsController::class, 'purchases'])->name('reports.purchases');
    Route::get('reports/supplier-price-comparison', [ReportsController::class, 'supplierPriceComparison'])->name('reports.supplier-price-comparison');
    Route::get('reports/sales', [ReportsController::class, 'sales'])->name('reports.sales');
    Route::get('reports/profit', [ReportsController::class, 'profit'])->name('reports.profit');
    Route::get('reports/outstanding-suppliers', [ReportsController::class, 'outstandingSuppliers'])->name('reports.outstanding-suppliers');
    Route::get('reports/outstanding-customers', [ReportsController::class, 'outstandingCustomers'])->name('reports.outstanding-customers');

    /*
    |--------------------------------------------------------------------
    | Expenses (Section 30)
    |--------------------------------------------------------------------
    */
    Route::get('expenses', [ExpensesController::class, 'index'])->name('expenses.index');
    Route::get('expenses/create', [ExpensesController::class, 'create'])->name('expenses.create');
    Route::post('expenses', [ExpensesController::class, 'store'])->name('expenses.store');
    Route::get('expenses/{id}/edit', [ExpensesController::class, 'edit'])->name('expenses.edit');
    Route::put('expenses/{id}', [ExpensesController::class, 'update'])->name('expenses.update');
    Route::delete('expenses/{id}', [ExpensesController::class, 'destroy'])->name('expenses.destroy');
});
