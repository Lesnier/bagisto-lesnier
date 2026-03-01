<?php

namespace Webkul\Marketplace\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webkul\Marketplace\Repositories\VendorEarningRepository;
use Webkul\Marketplace\Repositories\VendorRepository;

class VendorEarningController extends Controller
{
    public function __construct(
        protected VendorRepository $vendorRepository,
        protected VendorEarningRepository $earningRepository,
    ) {}

    public function index(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $vendor = $this->vendorRepository->findApprovedByCustomer($customer->id);

        $status = $request->query('status'); // pending|paid|refunded or null

        $earnings = $this->earningRepository->getForVendor($vendor->id, $status);

        $totals = $this->earningRepository->getTotals($vendor->id);

        return view('marketplace::vendor.earnings.index', compact('vendor', 'earnings', 'totals', 'status'));
    }
}
