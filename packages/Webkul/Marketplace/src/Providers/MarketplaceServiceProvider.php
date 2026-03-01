<?php

namespace Webkul\Marketplace\Providers;

use Illuminate\Routing\Router;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Webkul\Customer\Models\Customer;
use Webkul\Marketplace\Http\Middleware\EnsureIsVendor;
use Webkul\Marketplace\Models\VendorEarning;
use Webkul\Marketplace\Policies\VendorPolicy;
use Webkul\Marketplace\Repositories\VendorEarningRepository;
use Webkul\Marketplace\Repositories\VendorRepository;
use Webkul\Product\Models\Product;

class MarketplaceServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__, 2).'/config/marketplace.php',
            'marketplace'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/menu.php',
            'menu.admin'
        );

        // Bind repositories into the Laravel IoC container
        $this->app->bind(VendorRepository::class, fn ($app) => new VendorRepository($app));
        $this->app->bind(VendorEarningRepository::class, fn ($app) => new VendorEarningRepository($app));
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(dirname(__DIR__).'/Database/Migrations');

        $this->loadRoutesFrom(dirname(__DIR__, 2) . '/routes/vendor.php');
        $this->loadRoutesFrom(dirname(__DIR__, 2) . '/routes/shop.php');
        $this->loadRoutesFrom(dirname(__DIR__, 2) . '/routes/admin.php');

        $this->loadTranslationsFrom(dirname(__DIR__, 2) . '/resources/lang', 'marketplace');

        $this->loadViewsFrom(dirname(__DIR__, 2) . '/resources/views', 'marketplace');

        // Register the EventServiceProvider for order-based earnings events
        $this->app->register(EventServiceProvider::class);

        // Register the "vendor" middleware alias so routes can use ->middleware('vendor')
        $router = $this->app->make(Router::class);
        $router->aliasMiddleware('vendor', EnsureIsVendor::class);

        // Register the VendorPolicy gates
        $this->registerPolicies();
    }

    /**
     * Register authorization policies for the Marketplace package.
     */
    protected function registerPolicies(): void
    {
        $policy = new VendorPolicy();

        Gate::define('marketplace.vendor.manage', function (Customer $customer) use ($policy) {
            return $policy->manage($customer);
        });

        Gate::define('marketplace.vendor.manage-product', function (Customer $customer, Product $product) use ($policy) {
            return $policy->manageProduct($customer, $product);
        });

        Gate::define('marketplace.vendor.view-earning', function (Customer $customer, VendorEarning $earning) use ($policy) {
            return $policy->viewEarning($customer, $earning);
        });
    }
}
