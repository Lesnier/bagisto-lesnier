<?php

namespace Webkul\Marketplace\Listeners;

use Webkul\Marketplace\Models\VendorEarning;
use Webkul\Sales\Models\Order;

class UpdateVendorEarningStatus
{
    /**
     * Handle the sales.order.update-status.after event.
     *
     * Maps Bagisto's order statuses to vendor earning statuses:
     *  - completed  → paid (vendor earned the money)
     *  - cancelled  → refunded (reverse the earning)
     *  - closed     → refunded
     *
     * @param  \Webkul\Sales\Models\Order  $order
     */
    public function handle(Order $order): void
    {
        $earning = VendorEarning::where('order_id', $order->id)->first();

        if (! $earning) {
            return;
        }

        $newStatus = match ($order->status) {
            'completed'         => 'paid',
            'cancelled', 'closed' => 'refunded',
            default             => null,
        };

        if ($newStatus === null) {
            return;
        }

        $update = ['status' => $newStatus];

        if ($newStatus === 'paid' && ! $earning->paid_at) {
            $update['paid_at'] = now();
        }

        $earning->update($update);
    }
}
