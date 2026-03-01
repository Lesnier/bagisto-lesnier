<?php

namespace Webkul\Marketplace\Providers;

use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class EventServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application events.
     */
    public function boot(): void
    {
        /**
         * When an order is saved (checkout completed), automatically generate
         * a VendorEarning record for each item that belongs to a vendor.
         */
        Event::listen(
            'checkout.order.save.after',
            'Webkul\Marketplace\Listeners\CreateVendorEarning@handle'
        );

        /**
         * When an order status changes, update the earning status accordingly
         * (e.g. completed → paid, cancelled/refunded → refunded).
         */
        Event::listen(
            'sales.order.update-status.after',
            'Webkul\Marketplace\Listeners\UpdateVendorEarningStatus@handle'
        );
    }
}
