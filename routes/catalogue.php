<?php

use App\Http\Controllers\Public\CatalogueController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Catalogue Routes
|--------------------------------------------------------------------------
| Register from bootstrap/app.php alongside routes/admin.php, but with NO
| auth middleware and NO 'admin' prefix — this is the public-facing site:
|
|   ->withRouting(
|       web: __DIR__.'/../routes/web.php',
|       then: function () {
|           Route::middleware('web')->prefix('admin')->name('admin.')
|               ->group(base_path('routes/admin.php'));
|
|           Route::middleware('web')->name('catalogue.')
|               ->group(base_path('routes/catalogue.php'));
|       },
|   )
*/

Route::get('parts', [CatalogueController::class, 'index'])->name('index');
Route::get('parts/{slug}', [CatalogueController::class, 'show'])->name('show');
