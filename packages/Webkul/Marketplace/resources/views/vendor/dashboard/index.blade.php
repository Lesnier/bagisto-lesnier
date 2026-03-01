@extends('marketplace::vendor.layouts.app')

@section('title', __('marketplace::app.vendor.dashboard.title'))
@section('page-title', __('marketplace::app.vendor.dashboard.title'))

@section('content')

<style>
    .stats-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: 1rem; margin-bottom: 2rem; }
    .stat-card {
        background: #fff; border-radius: .5rem; border: 1px solid #e5e7eb;
        padding: 1.25rem; display: flex; flex-direction: column; gap: .4rem;
    }
    .stat-card .label { font-size: .75rem; text-transform: uppercase; letter-spacing:.05em; color: #64748b; }
    .stat-card .value { font-size: 1.6rem; font-weight: 700; color: #1e293b; }
    .stat-card .sub   { font-size: .8rem; color: #94a3b8; }

    .section-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: .75rem; }
    .section-header h2 { font-size: 1rem; font-weight: 600; color: #1e293b; }
    .section-header a  { font-size: .8rem; color: #6366f1; text-decoration: none; }

    table { width: 100%; border-collapse: collapse; background: #fff; border-radius: .5rem; overflow: hidden; border: 1px solid #e5e7eb; }
    th { background: #f1f5f9; text-align: left; padding: .65rem 1rem; font-size: .78rem; font-weight: 600; color: #64748b; text-transform: uppercase; }
    td { padding: .75rem 1rem; font-size: .875rem; color: #374151; border-top: 1px solid #f1f5f9; }
    .badge { display:inline-block; padding:.2rem .6rem; border-radius:9999px; font-size:.7rem; font-weight:600; text-transform:uppercase; }
    .badge-pending  { background:#fef9c3; color:#854d0e; }
    .badge-paid     { background:#d1fae5; color:#065f46; }
    .badge-refunded { background:#fce7f3; color:#9d174d; }
</style>

{{-- Stats row --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="label">{{ __('marketplace::app.vendor.dashboard.total-earnings') }}</div>
        <div class="value">{{ core()->formatBasePrice($stats['total_earnings']) }}</div>
    </div>
    <div class="stat-card">
        <div class="label">{{ __('marketplace::app.vendor.dashboard.pending-earnings') }}</div>
        <div class="value">{{ core()->formatBasePrice($stats['pending_earnings']) }}</div>
    </div>
    <div class="stat-card">
        <div class="label">{{ __('marketplace::app.vendor.dashboard.paid-earnings') }}</div>
        <div class="value">{{ core()->formatBasePrice($stats['paid_earnings']) }}</div>
    </div>
    <div class="stat-card">
        <div class="label">{{ __('marketplace::app.vendor.dashboard.total-orders') }}</div>
        <div class="value">{{ $stats['total_orders'] }}</div>
    </div>
    <div class="stat-card">
        <div class="label">{{ __('marketplace::app.vendor.dashboard.total-products') }}</div>
        <div class="value">{{ $stats['total_products'] }}</div>
    </div>
</div>

{{-- Recent earnings table --}}
<div class="section-header">
    <h2>{{ __('marketplace::app.vendor.dashboard.recent-earnings') }}</h2>
    <a href="{{ route('vendor.earnings.index') }}">{{ __('marketplace::app.vendor.dashboard.view-all') }} →</a>
</div>

@if ($recentEarnings->isEmpty())
    <p style="color:#94a3b8; font-size:.875rem;">{{ __('marketplace::app.vendor.dashboard.no-earnings') }}</p>
@else
    <table>
        <thead>
            <tr>
                <th>#{{ __('marketplace::app.vendor.earnings.order') }}</th>
                <th>{{ __('marketplace::app.vendor.earnings.total') }}</th>
                <th>{{ __('marketplace::app.vendor.earnings.commission') }}</th>
                <th>{{ __('marketplace::app.vendor.earnings.vendor-amount') }}</th>
                <th>{{ __('marketplace::app.vendor.earnings.status') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($recentEarnings as $earning)
            <tr>
                <td>#{{ $earning->order_id }}</td>
                <td>{{ core()->formatBasePrice($earning->order_total) }}</td>
                <td>{{ core()->formatBasePrice($earning->commission_amount) }} ({{ $earning->commission_percentage }}%)</td>
                <td>{{ core()->formatBasePrice($earning->vendor_amount) }}</td>
                <td><span class="badge badge-{{ $earning->status }}">{{ $earning->status }}</span></td>
            </tr>
            @endforeach
        </tbody>
    </table>
@endif

@endsection
