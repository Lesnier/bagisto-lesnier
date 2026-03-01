<?php

namespace Webkul\Marketplace\Models;

class VendorProxy
{
    public static function modelClass(): string
    {
        return config('marketplace.models.vendor', Vendor::class);
    }
}
