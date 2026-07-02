<?php

use App\Http\Controllers\Admin\MealController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth', 'verified'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('meals', MealController::class)->except('show');
});
