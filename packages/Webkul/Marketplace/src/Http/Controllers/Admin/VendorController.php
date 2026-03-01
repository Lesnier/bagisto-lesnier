<?php

namespace Webkul\Marketplace\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Webkul\Marketplace\Http\Controllers\Controller;
use Webkul\Marketplace\Repositories\VendorRepository;

class VendorController extends Controller
{
    public function __construct(
        protected VendorRepository $vendorRepository
    ) {}

    public function index()
    {
        $vendors = $this->vendorRepository->paginate(limit: 15);
        return view('marketplace::admin.vendors.index', compact('vendors'));
    }

    public function edit(int $id)
    {
        $vendor = $this->vendorRepository->findOrFail($id);
        return view('marketplace::admin.vendors.edit', compact('vendor'));
    }

    public function update(Request $request, int $id)
    {
        $this->vendorRepository->update($request->all(), $id);
        session()->flash('success', __('marketplace::app.admin.vendors.updated'));
        return redirect()->route('admin.marketplace.vendors.index');
    }

    public function approve(int $id)
    {
        $this->vendorRepository->update(['status' => 'approved', 'rejection_reason' => null], $id);
        session()->flash('success', __('marketplace::app.admin.vendors.approved'));
        return redirect()->back();
    }

    public function reject(Request $request, int $id)
    {
        $request->validate([
            'rejection_reason' => 'required|string',
        ], [
            'rejection_reason.required' => 'La razón de rechazo es obligatoria.',
        ]);
        
        $this->vendorRepository->update([
            'status'           => 'rejected',
            'rejection_reason' => $request->input('rejection_reason'),
        ], $id);

        session()->flash('success', __('marketplace::app.admin.vendors.rejected'));
        return redirect()->back();
    }
}
