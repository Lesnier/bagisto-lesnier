<?php

namespace Webkul\Marketplace\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Webkul\Marketplace\Http\Controllers\Controller;
use Webkul\Marketplace\Repositories\VendorEarningRepository;

class EarningController extends Controller
{
    public function __construct(
        protected VendorEarningRepository $earningRepository
    ) {}

    public function index(Request $request)
    {
        $status = $request->query('status'); // pending, paid, refunded

        $query = $this->earningRepository->with('vendor');
        
        if ($status) {
            $query = $query->where('status', $status);
        }

        $earnings = $query->orderBy('id', 'desc')->paginate(limit: 20);

        return view('marketplace::admin.earnings.index', compact('earnings', 'status'));
    }

    public function pay(int $id)
    {
        $this->earningRepository->update([
            'status'  => 'paid',
            'paid_at' => now(),
        ], $id);

        session()->flash('success', __('marketplace::app.admin.earnings.paid'));
        return redirect()->back();
    }
}
