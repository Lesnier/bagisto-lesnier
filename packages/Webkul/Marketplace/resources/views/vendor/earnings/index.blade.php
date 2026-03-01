@extends('marketplace::vendor.layouts.app')

@section('title', __('marketplace::app.vendor.earnings.title'))
@section('page-title', __('marketplace::app.vendor.earnings.title'))

@section('content')

<style>
    .totals-bar { display:flex; gap:1rem; margin-bottom:1.5rem; flex-wrap:wrap; }
    .total-chip {
        padding:.5rem 1rem; border-radius:.375rem; font-size:.85rem; font-weight:600;
        background:#fff; border:1px solid #e5e7eb;
    }
    .filter-row { display:flex; gap:.5rem; margin-bottom:1rem; flex-wrap:wrap; }
    .filter-row a {
        padding:.35rem .85rem; border-radius:9999px; font-size:.8rem; border:1px solid #e5e7eb;
        background:#fff; text-decoration:none; color:#374151;
    }
    .filter-row a.active { background:#1e293b; color:#fff; border-color:#1e293b; }
    table { width:100%; border-collapse:collapse; background:#fff; border:1px solid #e5e7eb; border-radius:.5rem; }
    th { background:#f1f5f9; text-align:left; padding:.65rem 1rem; font-size:.78rem; font-weight:600; color:#64748b; text-transform:uppercase; }
    td { padding:.75rem 1rem; font-size:.875rem; color:#374151; border-top:1px solid #f1f5f9; }
    .badge { display:inline-block; padding:.2rem .6rem; border-radius:9999px; font-size:.7rem; font-weight:600; text-transform:uppercase; }
    .badge-pending  { background:#fef9c3; color:#854d0e; }
    .badge-paid     { background:#d1fae5; color:#065f46; }
    .badge-refunded { background:#fce7f3; color:#9d174d; }
</style>

{{-- Totals summary --}}
<div class="totals-bar">
    <div class="total-chip">💰 {{ __('marketplace::app.vendor.earnings.total') }}: <strong>{{ core()->formatBasePrice($totals['total']) }}</strong></div>
    <div class="total-chip">⏳ {{ __('marketplace::app.vendor.earnings.pending') }}: <strong>{{ core()->formatBasePrice($totals['pending']) }}</strong></div>
    <div class="total-chip">✅ {{ __('marketplace::app.vendor.earnings.paid') }}: <strong>{{ core()->formatBasePrice($totals['paid']) }}</strong></div>
    <div class="total-chip">↩ {{ __('marketplace::app.vendor.earnings.refunded') }}: <strong>{{ core()->formatBasePrice($totals['refunded']) }}</strong></div>
</div>

{{-- Status filter --}}
<div class="filter-row">
    <a href="{{ route('vendor.earnings.index') }}" class="{{ !$status ? 'active' : '' }}">{{ __('marketplace::app.vendor.earnings.all') }}</a>
    <a href="{{ route('vendor.earnings.index', ['status' => 'pending']) }}" class="{{ $status === 'pending' ? 'active' : '' }}">{{ __('marketplace::app.vendor.earnings.pending') }}</a>
    <a href="{{ route('vendor.earnings.index', ['status' => 'paid']) }}" class="{{ $status === 'paid' ? 'active' : '' }}">{{ __('marketplace::app.vendor.earnings.paid') }}</a>
    <a href="{{ route('vendor.earnings.index', ['status' => 'refunded']) }}" class="{{ $status === 'refunded' ? 'active' : '' }}">{{ __('marketplace::app.vendor.earnings.refunded') }}</a>
</div>

@if ($earnings->isEmpty())
    <p style="color:#94a3b8; font-size:.875rem;">{{ __('marketplace::app.vendor.earnings.empty') }}</p>
@else
    <table>
        <thead>
            <tr>
                <th>#{{ __('marketplace::app.vendor.earnings.order') }}</th>
                <th>{{ __('marketplace::app.vendor.earnings.order-total') }}</th>
                <th>{{ __('marketplace::app.vendor.earnings.commission') }}</th>
                <th>{{ __('marketplace::app.vendor.earnings.vendor-amount') }}</th>
                <th>{{ __('marketplace::app.vendor.earnings.status') }}</th>
                <th>{{ __('marketplace::app.vendor.earnings.paid-at') }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($earnings as $earning)
            <tr>
                <td>#{{ $earning->order_id }}</td>
                <td>{{ core()->formatBasePrice($earning->order_total) }}</td>
                <td>{{ core()->formatBasePrice($earning->commission_amount) }} ({{ $earning->commission_percentage }}%)</td>
                <td>{{ core()->formatBasePrice($earning->vendor_amount) }}</td>
                <td><span class="badge badge-{{ $earning->status }}">{{ $earning->status }}</span></td>
                <td>{{ $earning->paid_at ? $earning->paid_at->format('d/m/Y') : '—' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div style="margin-top:1rem;">
        {{ $earnings->withQueryString()->links() }}
    </div>
@endif

@endsection
