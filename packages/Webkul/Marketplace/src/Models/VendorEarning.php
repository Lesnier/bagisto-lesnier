<?php

namespace Webkul\Marketplace\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Webkul\Sales\Models\OrderProxy;

class VendorEarning extends Model
{
    use HasFactory;

    protected $table = 'vendor_earnings';

    protected $fillable = [
        'vendor_id',
        'order_id',
        'order_total',
        'commission_amount',
        'vendor_amount',
        'commission_percentage',
        'status',
        'paid_at',
        'notes',
    ];

    protected $casts = [
        'order_total' => 'float',
        'commission_amount' => 'float',
        'vendor_amount' => 'float',
        'commission_percentage' => 'float',
        'paid_at' => 'datetime',
    ];

    /**
     * Get the vendor that owns the earning.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Get the order that the earning is from.
     */
    public function order(): BelongsTo
    {
        return $this->belongsTo(OrderProxy::modelClass());
    }
}
