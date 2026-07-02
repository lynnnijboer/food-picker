<?php

use App\Http\Controllers\WeekMenuController;
use Illuminate\Support\Facades\Route;

Route::get('/', [WeekMenuController::class, 'index'])->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

require __DIR__.'/settings.php';
require __DIR__.'/admin.php';
