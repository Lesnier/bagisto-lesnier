<?php

namespace Webkul\Marketplace\Listeners;

use Webkul\Marketplace\Models\Vendor;
use Webkul\Marketplace\Models\VendorEarning;
use Webkul\Sales\Models\Order;

class CreateVendorEarning
{
    /**
     * Handle the checkout.order.save.after event.
     *
     * Iterates each ordered item, checks if its product belongs to a vendor,
     * and creates a VendorEarning record with the calculated amounts.
     *
     * @param  \Webkul\Sales\Models\Order  $order
     */
    public function handle(Order $order): void
    {
        foreach ($order->items as $item) {
            $product = $item->product;

            if (! $product || ! $product->vendor_id) {
                continue;
            }

            $vendor = Vendor::where('id', $product->vendor_id)
                ->where('status', 'approved')
                ->first();

            if (! $vendor) {
                continue;
            }

            // Avoid duplicates in case the event fires more than once
            $alreadyExists = VendorEarning::where('vendor_id', $vendor->id)
                ->where('order_id', $order->id)
                ->exists();

            if ($alreadyExists) {
                continue;
            }

            $orderTotal          = (float) $item->total;
            $commissionPct       = (float) $vendor->commission_percentage;
            $commissionAmount    = $orderTotal * ($commissionPct / 100);
            $vendorAmount        = $orderTotal - $commissionAmount;

            VendorEarning::create([
                'vendor_id'             => $vendor->id,
                'order_id'              => $order->id,
                'order_total'           => $orderTotal,
                'commission_percentage' => $commissionPct,
                'commission_amount'     => $commissionAmount,
                'vendor_amount'         => $vendorAmount,
                'status'                => 'pending',
            ]);
        }
    }
}
