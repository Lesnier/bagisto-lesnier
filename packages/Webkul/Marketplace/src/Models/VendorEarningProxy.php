<?php

namespace Webkul\Marketplace\Models;

class VendorEarningProxy
{
    public static function modelClass(): string
    {
        return config('marketplace.models.vendor_earning', VendorEarning::class);
    }
}
