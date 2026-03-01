<?php

namespace Webkul\Marketplace\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Webkul\Customer\Models\CustomerProxy;
use Webkul\Product\Models\ProductProxy;

class Vendor extends Model
{
    use HasFactory;

    protected $table = 'vendors';

    protected $fillable = [
        'customer_id',
        'shop_name',
        'shop_description',
        'shop_slug',
        'shop_image',
        'commission_percentage',
        'bank_account_holder',
        'bank_name',
        'bank_account_number',
        'bank_routing_number',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'commission_percentage' => 'float',
    ];

    /**
     * Get the customer that owns the vendor.
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(CustomerProxy::modelClass());
    }

    /**
     * Get the products for the vendor.
     */
    public function products(): HasMany
    {
        return $this->hasMany(ProductProxy::modelClass(), 'vendor_id');
    }

    /**
     * Get the earnings for the vendor.
     */
    public function earnings(): HasMany
    {
        return $this->hasMany(VendorEarning::class, 'vendor_id');
    }

    /**
     * Get total earnings for the vendor.
     */
    public function getTotalEarnings(): float
    {
        return $this->earnings()->where('status', '!=', 'refunded')->sum('vendor_amount') ?? 0;
    }

    /**
     * Get pending earnings for the vendor.
     */
    public function getPendingEarnings(): float
    {
        return $this->earnings()->where('status', 'pending')->sum('vendor_amount') ?? 0;
    }

    /**
     * Get paid earnings for the vendor.
     */
    public function getPaidEarnings(): float
    {
        return $this->earnings()->where('status', 'paid')->sum('vendor_amount') ?? 0;
    }
}
