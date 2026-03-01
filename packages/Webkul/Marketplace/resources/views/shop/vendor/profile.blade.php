@extends('shop::layouts.master')

@section('page_title')
    {{ $vendor->shop_name }} - {{ config('app.name') }}
@endsection

@section('content-wrapper')
    <div class="container" style="max-width: 1200px; margin: 0 auto; padding: 20px;">
        
        {{-- Vendor Header --}}
        <div class="vendor-profile-header" style="background:#fff; border-radius:8px; padding:30px; box-shadow:0 1px 3px rgba(0,0,0,0.1); margin-bottom: 30px; display: flex; align-items: flex-start; gap: 20px;">
            <div class="vendor-logo" style="width: 120px; height: 120px; background:#f1f5f9; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:3rem; flex-shrink:0;">
                🏪
            </div>
            <div class="vendor-info">
                <h1 style="margin-top:0; font-size: 2rem; color: #1e293b;">{{ $vendor->shop_name }}</h1>
                @if($vendor->shop_description)
                    <p style="color: #64748b; line-height: 1.6; margin-top: 10px; font-size: 1.05rem;">
                        {{ $vendor->shop_description }}
                    </p>
                @endif
            </div>
        </div>

        {{-- Vendor Products --}}
        <div class="vendor-products-section">
            <h2 style="font-size: 1.5rem; color: #1e293b; margin-bottom: 20px; border-bottom: 2px solid #e2e8f0; padding-bottom: 10px;">
                {{ __('marketplace::app.shop.vendor.products') }} ({{ $products->total() }})
            </h2>

            @if ($products->isEmpty())
                <p style="color: #64748b; font-size: 1.1rem; padding: 20px 0;">
                    {{ __('marketplace::app.shop.vendor.no-products') }}
                </p>
            @else
                <div class="product-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px;">
                    @foreach ($products as $product)
                        @include('shop::products.list.card', ['product' => $product])
                    @endforeach
                </div>

                <div class="pagination-wrapper" style="margin-top: 30px;">
                    {{ $products->links() }}
                </div>
            @endif
        </div>

    </div>
@endsection
