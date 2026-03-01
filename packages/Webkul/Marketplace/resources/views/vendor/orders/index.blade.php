@extends('marketplace::vendor.layouts.app')

@section('title', __('marketplace::app.vendor.orders.title'))
@section('page-title', __('marketplace::app.vendor.orders.title'))

@section('content')

<style>
    table { width:100%; border-collapse:collapse; background:#fff; border:1px solid #e5e7eb; border-radius:.5rem; }
    th { background:#f1f5f9; text-align:left; padding:.65rem 1rem; font-size:.78rem; font-weight:600; color:#64748b; text-transform:uppercase; }
    td { padding:.75rem 1rem; font-size:.875rem; color:#374151; border-top:1px solid #f1f5f9; }
    a.btn { padding:.35rem .85rem; border-radius:.375rem; font-size:.8rem; background:#1e293b; color:#fff; text-decoration:none; }
    .badge { display:inline-block; padding:.2rem .6rem; border-radius:9999px; font-size:.7rem; font-weight:600; text-transform:uppercase; background:#e0e7ff; color:#3730a3; }
</style>

@if ($orders->isEmpty())
    <p style="color:#94a3b8; font-size:.875rem;">{{ __('marketplace::app.vendor.orders.empty') }}</p>
@else
    <table>
        <thead>
            <tr>
                <th>#{{ __('marketplace::app.vendor.orders.id') }}</th>
                <th>{{ __('marketplace::app.vendor.orders.customer') }}</th>
                <th>{{ __('marketplace::app.vendor.orders.grand-total') }}</th>
                <th>{{ __('marketplace::app.vendor.orders.status') }}</th>
                <th>{{ __('marketplace::app.vendor.orders.date') }}</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach ($orders as $order)
            <tr>
                <td>#{{ $order->id }}</td>
                <td>{{ $order->customer_first_name }} {{ $order->customer_last_name }}</td>
                <td>{{ core()->formatBasePrice($order->grand_total) }}</td>
                <td><span class="badge">{{ $order->status }}</span></td>
                <td>{{ $order->created_at->format('d/m/Y') }}</td>
                <td><a class="btn" href="{{ route('vendor.orders.show', $order->id) }}">{{ __('marketplace::app.vendor.orders.view') }}</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:1rem;">
        {{ $orders->links() }}
    </div>
@endif

@endsection
