<?php

namespace Webkul\Marketplace\Policies;

use Webkul\Customer\Models\Customer;
use Webkul\Marketplace\Models\Vendor;
use Webkul\Marketplace\Models\VendorEarning;
use Webkul\Product\Models\Product;

class VendorPolicy
{
    /**
     * Determine if the customer can manage (create/update/delete) something
     * in the vendor dashboard at all.
     *
     * Returns true only when the customer has an approved vendor profile.
     */
    public function manage(Customer $customer): bool
    {
        return Vendor::where('customer_id', $customer->id)
            ->where('status', 'approved')
            ->exists();
    }

    /**
     * Determine if the customer can manage the given product.
     *
     * Ensures the product belongs to the customer's own vendor profile.
     */
    public function manageProduct(Customer $customer, Product $product): bool
    {
        $vendor = Vendor::where('customer_id', $customer->id)
            ->where('status', 'approved')
            ->first();

        if (! $vendor) {
            return false;
        }

        return (int) $product->vendor_id === (int) $vendor->id;
    }

    /**
     * Determine if the customer can view the given earning record.
     *
     * Ensures the earning belongs to the customer's own vendor profile.
     */
    public function viewEarning(Customer $customer, VendorEarning $earning): bool
    {
        $vendor = Vendor::where('customer_id', $customer->id)
            ->where('status', 'approved')
            ->first();

        if (! $vendor) {
            return false;
        }

        return (int) $earning->vendor_id === (int) $vendor->id;
    }
}
