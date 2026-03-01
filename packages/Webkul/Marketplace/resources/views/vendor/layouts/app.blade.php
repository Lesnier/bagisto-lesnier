<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', __('marketplace::app.vendor.dashboard.title')) — {{ config('app.name') }}</title>

    {{-- Reuse the shop's compiled assets --}}
    @bagistoVite(['src/Resources/assets/css/app.css', 'src/Resources/assets/js/app.js'])

    <style>
        /* ── Vendor dashboard sidebar layout ─────────────────────────── */
        .vendor-layout { display: flex; min-height: 100vh; background: #f9fafb; }

        .vendor-sidebar {
            width: 240px; flex-shrink: 0;
            background: #1e293b; color: #e2e8f0;
            display: flex; flex-direction: column;
        }
        .vendor-sidebar .brand {
            padding: 1.5rem 1.25rem;
            font-size: 1.1rem; font-weight: 700; color: #fff;
            border-bottom: 1px solid #334155;
        }
        .vendor-sidebar nav { flex: 1; padding: 1rem 0; }
        .vendor-sidebar nav a {
            display: flex; align-items: center; gap: .6rem;
            padding: .65rem 1.25rem;
            color: #94a3b8; text-decoration: none;
            font-size: .875rem; transition: background .15s, color .15s;
        }
        .vendor-sidebar nav a:hover,
        .vendor-sidebar nav a.active { background: #334155; color: #fff; }
        .vendor-sidebar nav a .icon { width: 18px; text-align: center; }

        .vendor-content { flex: 1; display: flex; flex-direction: column; overflow: hidden; }

        .vendor-topbar {
            background: #fff; border-bottom: 1px solid #e5e7eb;
            padding: .875rem 1.5rem; display: flex; justify-content: space-between; align-items: center;
        }
        .vendor-topbar .page-title { font-size: 1.15rem; font-weight: 600; color: #1e293b; }

        .vendor-main { flex: 1; padding: 1.75rem 1.5rem; overflow-y: auto; }

        /* Flash messages */
        .alert { padding: .85rem 1rem; border-radius: .375rem; margin-bottom: 1rem; font-size: .875rem; }
        .alert-success { background: #d1fae5; color: #065f46; }
        .alert-error   { background: #fee2e2; color: #991b1b; }
    </style>
</head>
<body>

<div class="vendor-layout">

    {{-- ── Sidebar ──────────────────────────────────────────────────── --}}
    <aside class="vendor-sidebar">
        <div class="brand">🏪 {{ $currentVendor->shop_name }}</div>
        <nav>
            <a href="{{ route('vendor.dashboard') }}"
               class="{{ request()->routeIs('vendor.dashboard') ? 'active' : '' }}">
                <span class="icon">📊</span> {{ __('marketplace::app.vendor.sidebar.dashboard') }}
            </a>
            <a href="{{ route('vendor.products.index') }}"
               class="{{ request()->routeIs('vendor.products*') ? 'active' : '' }}">
                <span class="icon">📦</span> {{ __('marketplace::app.vendor.sidebar.products') }}
            </a>
            <a href="{{ route('vendor.orders.index') }}"
               class="{{ request()->routeIs('vendor.orders*') ? 'active' : '' }}">
                <span class="icon">🛒</span> {{ __('marketplace::app.vendor.sidebar.orders') }}
            </a>
            <a href="{{ route('vendor.earnings.index') }}"
               class="{{ request()->routeIs('vendor.earnings*') ? 'active' : '' }}">
                <span class="icon">💰</span> {{ __('marketplace::app.vendor.sidebar.earnings') }}
            </a>
            <a href="{{ route('vendor.settings.edit') }}"
               class="{{ request()->routeIs('vendor.settings*') ? 'active' : '' }}">
                <span class="icon">⚙️</span> {{ __('marketplace::app.vendor.sidebar.settings') }}
            </a>
        </nav>
        <div style="padding: 1rem 1.25rem; border-top: 1px solid #334155; font-size:.8rem; color:#64748b;">
            <a href="{{ route('shop.home.index') }}" style="color:#64748b;text-decoration:none;">
                ← {{ __('marketplace::app.vendor.sidebar.back-to-store') }}
            </a>
        </div>
    </aside>

    {{-- ── Main content ─────────────────────────────────────────────── --}}
    <div class="vendor-content">
        <div class="vendor-topbar">
            <div class="page-title">@yield('page-title', 'Dashboard')</div>
            <div style="font-size:.85rem; color:#64748b;">
                {{ Auth::guard('customer')->user()->first_name }}
            </div>
        </div>

        <main class="vendor-main">

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if (session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            @yield('content')

        </main>
    </div>

</div>

</body>
</html>
