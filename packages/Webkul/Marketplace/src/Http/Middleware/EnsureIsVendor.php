<?php

namespace Webkul\Marketplace\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;
use Webkul\Marketplace\Models\Vendor;

class EnsureIsVendor
{
    /**
     * Handle an incoming request.
     *
     * Verifies that the currently authenticated customer has an associated
     * vendor profile with an "approved" status. If not, the user is
     * redirected to the homepage with an error message.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $customer = Auth::guard('customer')->user();

        if (! $customer) {
            return redirect()->route('shop.customer.session.index')
                ->with('error', __('marketplace::app.vendor.auth.login-required'));
        }

        $vendor = Vendor::where('customer_id', $customer->id)
            ->where('status', 'approved')
            ->first();

        if (! $vendor) {
            return redirect()->route('shop.home.index')
                ->with('error', __('marketplace::app.vendor.auth.not-a-vendor'));
        }

        // Make the vendor instance available to all controllers via the request
        $request->merge(['current_vendor' => $vendor]);

        // Share the vendor with all views rendered during this request
        view()->share('currentVendor', $vendor);

        return $next($request);
    }
}
