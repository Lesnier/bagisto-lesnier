<?php

namespace Webkul\Marketplace\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Webkul\Marketplace\Repositories\VendorRepository;
use Webkul\Marketplace\Repositories\VendorEarningRepository;

class VendorDashboardController extends Controller
{
    public function __construct(
        protected VendorRepository $vendorRepository,
        protected VendorEarningRepository $earningRepository,
    ) {}

    public function index()
    {
        $customer = Auth::guard('customer')->user();

        $vendor = $this->vendorRepository->findApprovedByCustomer($customer->id);

        $stats = $this->vendorRepository->getDashboardStats($vendor);

        $recentEarnings = $this->earningRepository->getForVendor($vendor->id, null, 5);

        return view('marketplace::vendor.dashboard.index', compact('vendor', 'stats', 'recentEarnings'));
    }
}
