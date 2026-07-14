<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;

Route::view('/', 'welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', DashboardController::class)
        ->name('dashboard');

    Route::view('/admin/dashboard', 'admin.dashboard')
        ->middleware('role:admin')
        ->name('admin.dashboard');

    Route::view('/pharmacy/dashboard', 'pharmacy.dashboard')
        ->middleware('role:pharmacy-owner')
        ->name('pharmacy.dashboard');

    Route::view('/customer/dashboard', 'customer.dashboard')
        ->middleware('role:customer')
        ->name('customer.dashboard');
});

require __DIR__.'/settings.php';