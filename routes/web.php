
<?php

use App\Console\Commands\SendDailyDigest;
use App\Http\Controllers\Frontend\ComingSoonController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schedule;

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', [ComingSoonController::class, 'index'])->name('frontend.coming-soon');



Schedule::command(SendDailyDigest::class)
    ->dailyAt('08:00')
    ->timezone('Asia/Kolkata')
    ->withoutOverlapping();