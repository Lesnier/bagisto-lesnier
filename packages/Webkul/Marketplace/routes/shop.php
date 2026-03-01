<?php

use Illuminate\Support\Facades\Route;
use Webkul\Marketplace\Http\Controllers\Shop\VendorController;
use Webkul\Marketplace\Http\Controllers\Shop\VendorApplicationController;

Route::group(['middleware' => ['web', 'theme', 'locale', 'currency']], function () {
    Route::get('marketplace/apply', [VendorApplicationController::class, 'create'])->name('marketplace.vendor.apply.create');
    Route::post('marketplace/apply', [VendorApplicationController::class, 'store'])->name('marketplace.vendor.apply.store');

    Route::get('marketplace/{slug}', [VendorController::class, 'show'])
        ->name('marketplace.vendor.profile')
        ->withoutMiddleware(['cacheResponse']);
});
