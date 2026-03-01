<?php

use Illuminate\Support\Facades\Route;
use Webkul\Marketplace\Http\Controllers\Admin\VendorController;
use Webkul\Marketplace\Http\Controllers\Admin\EarningController;

Route::group([
    'prefix'     => config('app.admin_url') . '/marketplace',
    'middleware' => ['web', 'admin'],
], function () {

    // Vendor Management
    Route::get('vendors', [VendorController::class, 'index'])->name('admin.marketplace.vendors.index');
    Route::get('vendors/{id}', [VendorController::class, 'edit'])->name('admin.marketplace.vendors.edit');
    Route::put('vendors/{id}', [VendorController::class, 'update'])->name('admin.marketplace.vendors.update');
    Route::put('vendors/{id}/approve', [VendorController::class, 'approve'])->name('admin.marketplace.vendors.approve');
    Route::put('vendors/{id}/reject', [VendorController::class, 'reject'])->name('admin.marketplace.vendors.reject');

    // Earnings Overview
    Route::get('earnings', [EarningController::class, 'index'])->name('admin.marketplace.earnings.index');
    Route::post('earnings/{id}/pay', [EarningController::class, 'pay'])->name('admin.marketplace.earnings.pay');

});
