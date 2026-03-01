<?php

namespace Webkul\Marketplace\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webkul\Marketplace\Repositories\VendorRepository;

class VendorSettingsController extends Controller
{
    public function __construct(
        protected VendorRepository $vendorRepository,
    ) {}

    public function edit()
    {
        $customer = Auth::guard('customer')->user();

        $vendor = $this->vendorRepository->findApprovedByCustomer($customer->id);

        return view('marketplace::vendor.settings.edit', compact('vendor'));
    }

    public function update(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $vendor = $this->vendorRepository->findApprovedByCustomer($customer->id);

        $validated = $request->validate([
            'shop_name'            => 'required|string|max:255',
            'shop_description'     => 'nullable|string|max:2000',
            'bank_account_holder'  => 'nullable|string|max:255',
            'bank_name'            => 'nullable|string|max:255',
            'bank_account_number'  => 'nullable|string|max:100',
            'bank_routing_number'  => 'nullable|string|max:100',
        ]);

        $this->vendorRepository->update($validated, $vendor->id);

        session()->flash('success', __('marketplace::app.vendor.settings.updated'));

        return redirect()->route('vendor.settings.edit');
    }
}
