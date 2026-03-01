<?php

namespace Webkul\Marketplace\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Webkul\Marketplace\Repositories\VendorRepository;
use Webkul\Sales\Repositories\OrderRepository;

class VendorOrderController extends Controller
{
    public function __construct(
        protected VendorRepository $vendorRepository,
        protected OrderRepository $orderRepository,
    ) {}

    public function index(Request $request)
    {
        $customer = Auth::guard('customer')->user();

        $vendor = $this->vendorRepository->findApprovedByCustomer($customer->id);

        // Get orders that contain at least one item belonging to this vendor
        $orders = $this->orderRepository->scopeQuery(function ($query) use ($vendor) {
            return $query->whereHas('items.product', function ($q) use ($vendor) {
                $q->where('vendor_id', $vendor->id);
            })->latest();
        })->paginate(15);

        return view('marketplace::vendor.orders.index', compact('vendor', 'orders'));
    }

    public function show(int $id)
    {
        $customer = Auth::guard('customer')->user();

        $vendor = $this->vendorRepository->findApprovedByCustomer($customer->id);

        $order = $this->orderRepository->findOrFail($id);

        // Only show the items that belong to this vendor
        $vendorItems = $order->items->filter(
            fn ($item) => $item->product && (int) $item->product->vendor_id === (int) $vendor->id
        );

        return view('marketplace::vendor.orders.show', compact('vendor', 'order', 'vendorItems'));
    }
}
