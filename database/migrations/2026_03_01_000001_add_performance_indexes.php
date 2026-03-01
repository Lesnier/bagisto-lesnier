<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Índices de performance — basados en queries lentas detectadas por Telescope.
     * Afecta: addresses (cart_id), cart (customer_id, is_active),
     *         product_flat (status, visible_individually, channel, locale),
     *         category_products (category_id, product_id),
     *         core_config (code).
     */
    public function up(): void
    {
        // addresses.cart_id — query lenta 222ms detectada en Telescope
        if (Schema::hasTable('addresses') && !$this->hasIndex('addresses', 'addresses_cart_id_index')) {
            Schema::table('addresses', function (Blueprint $table) {
                $table->index('cart_id', 'addresses_cart_id_index');
            });
        }

        // addresses.order_id — mismo patrón de lookup
        if (Schema::hasTable('addresses') && !$this->hasIndex('addresses', 'addresses_order_id_index')) {
            Schema::table('addresses', function (Blueprint $table) {
                $table->index('order_id', 'addresses_order_id_index');
            });
        }

        // cart.customer_id + is_active — usados en cada request de cliente logueado
        if (Schema::hasTable('cart') && !$this->hasIndex('cart', 'cart_customer_id_is_active_index')) {
            Schema::table('cart', function (Blueprint $table) {
                $table->index(['customer_id', 'is_active'], 'cart_customer_id_is_active_index');
            });
        }

        // core_config.code — llamada repetidamente sin índice
        if (Schema::hasTable('core_config') && !$this->hasIndex('core_config', 'core_config_code_index')) {
            Schema::table('core_config', function (Blueprint $table) {
                $table->index('code', 'core_config_code_index');
            });
        }

        // product_flat — la query de listado de productos por categoría
        if (Schema::hasTable('product_flat') && !$this->hasIndex('product_flat', 'product_flat_status_visible_index')) {
            Schema::table('product_flat', function (Blueprint $table) {
                $table->index(['status', 'visible_individually', 'channel', 'locale'], 'product_flat_status_visible_index');
            });
        }

        // category_products — join en cada listado de categoría
        if (Schema::hasTable('category_products') && !$this->hasIndex('category_products', 'category_products_category_id_index')) {
            Schema::table('category_products', function (Blueprint $table) {
                $table->index('category_id', 'category_products_category_id_index');
            });
        }

        // wishlist_items.customer_id — query lenta detectada
        if (Schema::hasTable('wishlist_items') && !$this->hasIndex('wishlist_items', 'wishlist_items_customer_channel_index')) {
            Schema::table('wishlist_items', function (Blueprint $table) {
                $table->index(['customer_id', 'channel_id'], 'wishlist_items_customer_channel_index');
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasTable('addresses')) {
            Schema::table('addresses', function (Blueprint $table) {
                $table->dropIndexIfExists('addresses_cart_id_index');
                $table->dropIndexIfExists('addresses_order_id_index');
            });
        }

        if (Schema::hasTable('cart')) {
            Schema::table('cart', function (Blueprint $table) {
                $table->dropIndexIfExists('cart_customer_id_is_active_index');
            });
        }

        if (Schema::hasTable('core_config')) {
            Schema::table('core_config', function (Blueprint $table) {
                $table->dropIndexIfExists('core_config_code_index');
            });
        }

        if (Schema::hasTable('product_flat')) {
            Schema::table('product_flat', function (Blueprint $table) {
                $table->dropIndexIfExists('product_flat_status_visible_index');
            });
        }

        if (Schema::hasTable('category_products')) {
            Schema::table('category_products', function (Blueprint $table) {
                $table->dropIndexIfExists('category_products_category_id_index');
            });
        }

        if (Schema::hasTable('wishlist_items')) {
            Schema::table('wishlist_items', function (Blueprint $table) {
                $table->dropIndexIfExists('wishlist_items_customer_channel_index');
            });
        }
    }

    /**
     * Verifica si un índice ya existe para evitar errores al re-ejecutar.
     */
    private function hasIndex(string $table, string $indexName): bool
    {
        $indexes = \Illuminate\Support\Facades\DB::select(
            "SHOW INDEX FROM `{$table}` WHERE Key_name = ?",
            [$indexName]
        );

        return !empty($indexes);
    }
};
