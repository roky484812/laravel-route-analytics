<?php

use Illuminate\Support\Facades\Route;
use Roky\LaravelRouteAnalytics\Http\Controllers\DashboardController;

Route::prefix('route-analytics')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('route-analytics.dashboard');
});
