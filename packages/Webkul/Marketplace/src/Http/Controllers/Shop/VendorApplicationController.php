<?php

namespace Webkul\Marketplace\Http\Controllers\Shop;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Webkul\Marketplace\Http\Controllers\Controller;
use Webkul\Marketplace\Repositories\VendorRepository;

class VendorApplicationController extends Controller
{
    public function __construct(
        protected VendorRepository $vendorRepository
    ) {}

    /**
     * Show the form for creating a new vendor application.
     */
    public function create()
    {
        // If the user is not authenticated, redirect to login
        if (! Auth::guard('customer')->check()) {
            return redirect()->route('shop.customer.session.index')
                ->with('warning', __('marketplace::app.shop.vendor.apply.login-required'));
        }

        $customer = Auth::guard('customer')->user();
        
        // Check if the user already has a pending or approved vendor record
        $existingVendor = $this->vendorRepository->findOneByField('customer_id', $customer->id);

        if ($existingVendor) {
            if ($existingVendor->status === 'approved') {
                return redirect()->route('vendor.dashboard')
                    ->with('info', __('marketplace::app.shop.vendor.apply.already-approved'));
            }

            return redirect()->route('shop.home.index')
                ->with('info', __('marketplace::app.shop.vendor.apply.already-pending'));
        }

        return view('marketplace::shop.vendor.apply');
    }

    /**
     * Store a newly created vendor application in storage.
     */
    public function store(Request $request)
    {
        if (! Auth::guard('customer')->check()) {
            return redirect()->route('shop.customer.session.index');
        }

        $request->validate([
            'shop_name'        => 'required|string|max:255|unique:vendors,shop_name',
            'shop_description' => 'required|string|min:20'
        ]);

        $customerId = Auth::guard('customer')->id();

        // Extra guard
        if ($this->vendorRepository->findOneByField('customer_id', $customerId)) {
            return redirect()->route('shop.home.index')
                ->with('warning', __('marketplace::app.shop.vendor.apply.already-applied'));
        }

        $slug = Str::slug($request->shop_name);
        
        // Ensure slug is unique
        $originalSlug = $slug;
        $counter = 1;
        while ($this->vendorRepository->findOneByField('shop_slug', $slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }

        $this->vendorRepository->create([
            'customer_id'           => $customerId,
            'shop_name'             => $request->shop_name,
            'shop_slug'             => $slug,
            'shop_description'      => $request->shop_description,
            'status'                => 'pending',
            'commission_percentage' => 10, // default commission
        ]);

        return redirect()->route('shop.home.index')
            ->with('success', __('marketplace::app.shop.vendor.apply.success'));
    }
}
