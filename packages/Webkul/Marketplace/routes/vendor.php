<?php

use Illuminate\Support\Facades\Route;
use Webkul\Marketplace\Http\Controllers\VendorDashboardController;
use Webkul\Marketplace\Http\Controllers\VendorProductController;
use Webkul\Marketplace\Http\Controllers\VendorOrderController;
use Webkul\Marketplace\Http\Controllers\VendorEarningController;
use Webkul\Marketplace\Http\Controllers\VendorSettingsController;

Route::middleware(['web', 'customer', 'vendor'])
    ->prefix('vendor')
    ->name('vendor.')
    ->group(function () {
        Route::get('/', [VendorDashboardController::class, 'index'])->name('dashboard');
        Route::resource('products', VendorProductController::class);
        Route::get('products-template', [VendorProductController::class, 'downloadTemplate'])->name('products.template');
        Route::post('products-import', [VendorProductController::class, 'import'])->name('products.import');
        Route::resource('orders', VendorOrderController::class)->only(['index', 'show']);
        Route::get('earnings', [VendorEarningController::class, 'index'])->name('earnings.index');
        Route::get('settings', [VendorSettingsController::class, 'edit'])->name('settings.edit');
        Route::post('settings', [VendorSettingsController::class, 'update'])->name('settings.update');
    });
