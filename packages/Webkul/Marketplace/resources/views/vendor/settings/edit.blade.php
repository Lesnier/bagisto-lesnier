@extends('marketplace::vendor.layouts.app')

@section('title', __('marketplace::app.vendor.settings.title'))
@section('page-title', __('marketplace::app.vendor.settings.title'))

@section('content')

<style>
    .settings-form { background:#fff; border:1px solid #e5e7eb; border-radius:.5rem; padding:1.75rem; max-width:700px; }
    .form-section-title { font-size:.9rem; font-weight:700; color:#1e293b; margin:1.25rem 0 .75rem; padding-bottom:.4rem; border-bottom:1px solid #f1f5f9; }
    .form-group { margin-bottom:1rem; }
    .form-group label { display:block; font-size:.8rem; font-weight:600; color:#374151; margin-bottom:.35rem; }
    .form-group input, .form-group textarea {
        width:100%; padding:.55rem .75rem; border:1px solid #d1d5db; border-radius:.375rem;
        font-size:.875rem; color:#1e293b; box-sizing:border-box;
    }
    .form-group input:focus, .form-group textarea:focus { outline:none; border-color:#6366f1; }
    .form-group textarea { height: 100px; resize:vertical; }
    .form-error { color:#dc2626; font-size:.78rem; margin-top:.25rem; }
    .btn-save { padding:.6rem 1.5rem; background:#6366f1; color:#fff; border:none; border-radius:.375rem; font-size:.875rem; font-weight:600; cursor:pointer; }
    .btn-save:hover { background:#4f46e5; }
</style>

<div class="settings-form">
    <form method="POST" action="{{ route('vendor.settings.update') }}">
        @csrf @method('POST')

        {{-- Shop information --}}
        <div class="form-section-title">🏪 {{ __('marketplace::app.vendor.settings.shop-info') }}</div>

        <div class="form-group">
            <label>{{ __('marketplace::app.vendor.settings.shop-name') }} *</label>
            <input type="text" name="shop_name" value="{{ old('shop_name', $vendor->shop_name) }}" required>
            @error('shop_name')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        <div class="form-group">
            <label>{{ __('marketplace::app.vendor.settings.shop-description') }}</label>
            <textarea name="shop_description">{{ old('shop_description', $vendor->shop_description) }}</textarea>
            @error('shop_description')<div class="form-error">{{ $message }}</div>@enderror
        </div>

        {{-- Banking information --}}
        <div class="form-section-title">🏦 {{ __('marketplace::app.vendor.settings.banking') }}</div>

        <div class="form-group">
            <label>{{ __('marketplace::app.vendor.settings.bank-account-holder') }}</label>
            <input type="text" name="bank_account_holder" value="{{ old('bank_account_holder', $vendor->bank_account_holder) }}">
        </div>

        <div class="form-group">
            <label>{{ __('marketplace::app.vendor.settings.bank-name') }}</label>
            <input type="text" name="bank_name" value="{{ old('bank_name', $vendor->bank_name) }}">
        </div>

        <div class="form-group">
            <label>{{ __('marketplace::app.vendor.settings.bank-account-number') }}</label>
            <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $vendor->bank_account_number) }}">
        </div>

        <div class="form-group">
            <label>{{ __('marketplace::app.vendor.settings.bank-routing-number') }}</label>
            <input type="text" name="bank_routing_number" value="{{ old('bank_routing_number', $vendor->bank_routing_number) }}">
        </div>

        <button type="submit" class="btn-save">{{ __('marketplace::app.vendor.settings.save') }}</button>
    </form>
</div>

@endsection
