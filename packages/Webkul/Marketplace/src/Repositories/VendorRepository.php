<?php

namespace Webkul\Marketplace\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Models\Vendor;

class VendorRepository extends Repository
{
    /**
     * Specify the model class name.
     */
    public function model(): string
    {
        return Vendor::class;
    }

    /**
     * Find the vendor belonging to a specific customer.
     */
    public function findByCustomer(int $customerId): ?Vendor
    {
        return $this->model->where('customer_id', $customerId)->first();
    }

    /**
     * Find the approved vendor belonging to a specific customer.
     */
    public function findApprovedByCustomer(int $customerId): ?Vendor
    {
        return $this->model->where('customer_id', $customerId)
            ->where('status', 'approved')
            ->first();
    }

    /**
     * Get summary stats for a vendor dashboard.
     *
     * Returns an array with:
     *  - total_earnings   float
     *  - pending_earnings float
     *  - paid_earnings    float
     *  - total_orders     int
     *  - total_products   int
     */
    public function getDashboardStats(Vendor $vendor): array
    {
        return [
            'total_earnings'   => $vendor->getTotalEarnings(),
            'pending_earnings' => $vendor->getPendingEarnings(),
            'paid_earnings'    => $vendor->getPaidEarnings(),
            'total_orders'     => $vendor->earnings()->distinct('order_id')->count('order_id'),
            'total_products'   => $vendor->products()->count(),
        ];
    }
}
