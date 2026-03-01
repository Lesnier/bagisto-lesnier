@extends('marketplace::vendor.layouts.app')

@section('title', __('marketplace::app.vendor.orders.detail-title', ['id' => $order->id]))
@section('page-title', '#' . $order->id . ' — ' . __('marketplace::app.vendor.orders.detail-title', ['id' => $order->id]))

@section('content')

<style>
    .order-meta { background:#fff; border:1px solid #e5e7eb; border-radius:.5rem; padding:1.25rem; margin-bottom:1.5rem; display:grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap:1rem; }
    .meta-item .label { font-size:.75rem; text-transform:uppercase; letter-spacing:.04em; color:#64748b; }
    .meta-item .value { font-size:.95rem; font-weight:600; color:#1e293b; margin-top:.2rem; }
    table { width:100%; border-collapse:collapse; background:#fff; border:1px solid #e5e7eb; border-radius:.5rem; }
    th { background:#f1f5f9; text-align:left; padding:.65rem 1rem; font-size:.78rem; font-weight:600; color:#64748b; text-transform:uppercase; }
    td { padding:.75rem 1rem; font-size:.875rem; color:#374151; border-top:1px solid #f1f5f9; }
    .back-link { display:inline-block; margin-bottom:1rem; font-size:.85rem; color:#6366f1; text-decoration:none; }
</style>

<a class="back-link" href="{{ route('vendor.orders.index') }}">← {{ __('marketplace::app.vendor.orders.back') }}</a>

<div class="order-meta">
    <div class="meta-item">
        <div class="label">{{ __('marketplace::app.vendor.orders.customer') }}</div>
        <div class="value">{{ $order->customer_first_name }} {{ $order->customer_last_name }}</div>
    </div>
    <div class="meta-item">
        <div class="label">{{ __('marketplace::app.vendor.orders.status') }}</div>
        <div class="value">{{ $order->status }}</div>
    </div>
    <div class="meta-item">
        <div class="label">{{ __('marketplace::app.vendor.orders.grand-total') }}</div>
        <div class="value">{{ core()->formatBasePrice($order->grand_total) }}</div>
    </div>
    <div class="meta-item">
        <div class="label">{{ __('marketplace::app.vendor.orders.date') }}</div>
        <div class="value">{{ $order->created_at->format('d/m/Y H:i') }}</div>
    </div>
</div>

<h2 style="font-size:1rem; font-weight:600; color:#1e293b; margin-bottom:.75rem;">
    {{ __('marketplace::app.vendor.orders.your-items') }}
</h2>

<table>
    <thead>
        <tr>
            <th>{{ __('marketplace::app.vendor.orders.product') }}</th>
            <th>{{ __('marketplace::app.vendor.orders.sku') }}</th>
            <th>{{ __('marketplace::app.vendor.orders.qty') }}</th>
            <th>{{ __('marketplace::app.vendor.orders.price') }}</th>
            <th>{{ __('marketplace::app.vendor.orders.subtotal') }}</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($vendorItems as $item)
        <tr>
            <td>{{ $item->name }}</td>
            <td>{{ $item->sku }}</td>
            <td>{{ $item->qty_ordered }}</td>
            <td>{{ core()->formatBasePrice($item->price) }}</td>
            <td>{{ core()->formatBasePrice($item->total) }}</td>
        </tr>
        @endforeach
    </tbody>
</table>

@endsection
