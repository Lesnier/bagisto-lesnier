<?php

namespace Webkul\Marketplace\Http\Controllers\Shop;

use Webkul\Marketplace\Http\Controllers\Controller;
use Webkul\Marketplace\Repositories\VendorRepository;
use Webkul\Product\Repositories\ProductRepository;

class VendorController extends Controller
{
    public function __construct(
        protected VendorRepository $vendorRepository,
        protected ProductRepository $productRepository,
    ) {}

    public function show(string $slug)
    {
        // 1. Find the approved vendor by slug, fail (404) if not found or not approved
        $vendor = $this->vendorRepository->findOneWhere([
            'shop_slug' => $slug,
            'status'    => 'approved'
        ]);

        if (! $vendor) {
            abort(404, 'Vendor not found.');
        }

        // 2. Fetch the vendor's products (only visible/active)
        $products = $this->productRepository->scopeQuery(function ($query) use ($vendor) {
            return $query->where('vendor_id', $vendor->id)
                         ->where('status', 1); // 1 is active/visible in Bagisto
        })->paginate(limit: 12);

        // 3. Render the public storefront view
        return view('marketplace::shop.vendor.profile', compact('vendor', 'products'));
    }
}
