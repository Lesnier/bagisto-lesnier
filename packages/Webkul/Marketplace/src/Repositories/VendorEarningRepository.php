<?php

namespace Webkul\Marketplace\Repositories;

use Webkul\Core\Eloquent\Repository;
use Webkul\Marketplace\Models\VendorEarning;

class VendorEarningRepository extends Repository
{
    /**
     * Specify the model class name.
     */
    public function model(): string
    {
        return VendorEarning::class;
    }

    /**
     * Get paginated earnings for a specific vendor, with optional status filter.
     *
     * @param  int         $vendorId
     * @param  string|null $status   pending|paid|refunded
     * @param  int         $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function getForVendor(int $vendorId, ?string $status = null, int $perPage = 15)
    {
        $query = $this->model
            ->with('order')
            ->where('vendor_id', $vendorId)
            ->latest();

        if ($status) {
            $query->where('status', $status);
        }

        return $query->paginate($perPage);
    }

    /**
     * Get aggregate totals for a vendor's earnings.
     *
     * @return array{total: float, pending: float, paid: float, refunded: float}
     */
    public function getTotals(int $vendorId): array
    {
        $rows = $this->model
            ->where('vendor_id', $vendorId)
            ->selectRaw('status, SUM(vendor_amount) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        return [
            'total'    => (float) array_sum($rows->toArray()),
            'pending'  => (float) ($rows['pending']  ?? 0),
            'paid'     => (float) ($rows['paid']     ?? 0),
            'refunded' => (float) ($rows['refunded'] ?? 0),
        ];
    }
}
