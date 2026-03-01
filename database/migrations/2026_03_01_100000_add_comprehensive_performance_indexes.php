<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Migración maestra de índices de performance para Bagisto.
 *
 * Organizada por flujo de usuario:
 * 1. Exploración y búsqueda de productos
 * 2. Detalle de producto
 * 3. Carrito y Checkout
 * 4. Órdenes y post-venta
 * 5. Clientes y sesiones
 * 6. Configuración y Core (todas las páginas)
 * 7. Marketplace / Vendors
 */
return new class extends Migration
{
    public function up(): void
    {
        // =====================================================================
        // FLUJO 1 — Exploración y búsqueda de productos
        // =====================================================================

        // product_flat: tabla central de listados de productos
        $this->addIndex('product_flat', ['channel', 'locale', 'status'], 'idx_pf_channel_locale_status');
        $this->addIndex('product_flat', ['status', 'visible_individually'], 'idx_pf_status_visible');
        $this->addIndex('product_flat', ['url_key'], 'idx_pf_url_key');
        $this->addIndex('product_flat', ['new'], 'idx_pf_new');
        $this->addIndex('product_flat', ['featured'], 'idx_pf_featured');
        $this->addIndex('product_flat', ['price'], 'idx_pf_price');
        $this->addIndex('product_flat', ['created_at'], 'idx_pf_created_at');
        $this->addIndex('product_flat', ['parent_id'], 'idx_pf_parent_id');

        // category_products: JOIN en cada listado de categoría
        $this->addIndex('category_products', ['category_id'], 'idx_cp_category_id');
        $this->addIndex('category_products', ['product_id'], 'idx_cp_product_id');

        // categories: árbol de navegación
        $this->addIndex('categories', ['parent_id'], 'idx_cat_parent_id');
        $this->addIndex('categories', ['status'], 'idx_cat_status');
        $this->addIndex('categories', ['position'], 'idx_cat_position');

        // product_price_indices: precios por canal/grupo
        if (Schema::hasTable('product_price_indices')) {
            $this->addIndex('product_price_indices', ['product_id'], 'idx_ppi_product_id');
            $this->addIndex('product_price_indices', ['channel_id'], 'idx_ppi_channel_id');
            $this->addIndex('product_price_indices', ['customer_group_id'], 'idx_ppi_customer_group_id');
        }

        // product_inventory_indices: stock en listados
        if (Schema::hasTable('product_inventory_indices')) {
            $this->addIndex('product_inventory_indices', ['product_id'], 'idx_pii_product_id');
        }

        // =====================================================================
        // FLUJO 2 — Detalle de producto
        // =====================================================================

        // product_attribute_values: EAV lookup — el más consultado de toda la app
        $this->addIndex('product_attribute_values', ['product_id'], 'idx_pav_product_id');
        $this->addIndex('product_attribute_values', ['attribute_id'], 'idx_pav_attribute_id');

        // product_images: galería
        $this->addIndex('product_images', ['product_id'], 'idx_pi_product_id');

        // product_reviews: reviews visibles de un producto
        $this->addIndex('product_reviews', ['product_id', 'status'], 'idx_pr_product_status');
        $this->addIndex('product_reviews', ['customer_id'], 'idx_pr_customer_id');

        // product_inventories: stock por fuente
        $this->addIndex('product_inventories', ['product_id'], 'idx_pinv_product_id');
        $this->addIndex('product_inventories', ['vendor_id'], 'idx_pinv_vendor_id');

        // =====================================================================
        // FLUJO 3 — Carrito y Checkout
        // =====================================================================

        // cart: carrito activo — consultado en cada request de sesión activa
        $this->addIndex('cart', ['customer_id', 'is_active'], 'idx_cart_customer_active');
        $this->addIndex('cart', ['is_guest', 'is_active'], 'idx_cart_guest_active');
        $this->addIndex('cart', ['channel_id'], 'idx_cart_channel');

        // cart_items: items del carrito
        $this->addIndex('cart_items', ['cart_id'], 'idx_ci_cart_id');
        $this->addIndex('cart_items', ['product_id'], 'idx_ci_product_id');
        $this->addIndex('cart_items', ['parent_id'], 'idx_ci_parent_id');

        // addresses: ⚠️ 222ms detectado en Telescope
        $this->addIndex('addresses', ['cart_id'], 'idx_addr_cart_id');
        $this->addIndex('addresses', ['order_id'], 'idx_addr_order_id');
        $this->addIndex('addresses', ['customer_id'], 'idx_addr_customer_id');
        $this->addIndex('addresses', ['address_type'], 'idx_addr_type');

        // =====================================================================
        // FLUJO 4 — Órdenes y Post-venta
        // =====================================================================

        // orders: el admin lista y filtra por estos campos constantemente
        $this->addIndex('orders', ['customer_id'], 'idx_ord_customer_id');
        $this->addIndex('orders', ['status'], 'idx_ord_status');
        $this->addIndex('orders', ['channel_id'], 'idx_ord_channel_id');
        $this->addIndex('orders', ['customer_email'], 'idx_ord_customer_email');
        $this->addIndex('orders', ['cart_id'], 'idx_ord_cart_id');
        $this->addIndex('orders', ['created_at'], 'idx_ord_created_at');

        // order_items: items de órdenes
        $this->addIndex('order_items', ['order_id'], 'idx_oi_order_id');
        $this->addIndex('order_items', ['product_id', 'product_type'], 'idx_oi_product_morph');
        $this->addIndex('order_items', ['parent_id'], 'idx_oi_parent_id');

        // invoices
        $this->addIndex('invoices', ['order_id'], 'idx_inv_order_id');

        // shipments
        $this->addIndex('shipments', ['order_id'], 'idx_ship_order_id');
        $this->addIndex('shipments', ['customer_id'], 'idx_ship_customer_id');

        // shipment_items
        $this->addIndex('shipment_items', ['shipment_id'], 'idx_shi_shipment_id');
        $this->addIndex('shipment_items', ['order_item_id'], 'idx_shi_order_item_id');

        // refunds
        $this->addIndex('refunds', ['order_id'], 'idx_ref_order_id');

        // refund_items
        $this->addIndex('refund_items', ['refund_id'], 'idx_ri_refund_id');
        $this->addIndex('refund_items', ['order_item_id'], 'idx_ri_order_item_id');

        // invoice_items
        $this->addIndex('invoice_items', ['invoice_id'], 'idx_ii_invoice_id');
        $this->addIndex('invoice_items', ['order_item_id'], 'idx_ii_order_item_id');

        // order_payment
        $this->addIndex('order_payment', ['order_id'], 'idx_op_order_id');

        // =====================================================================
        // FLUJO 5 — Clientes y Cuentas
        // =====================================================================

        // customers: filtros frecuentes en admin
        $this->addIndex('customers', ['customer_group_id'], 'idx_cust_group_id');
        $this->addIndex('customers', ['status'], 'idx_cust_status');
        $this->addIndex('customers', ['is_verified'], 'idx_cust_verified');
        $this->addIndex('customers', ['created_at'], 'idx_cust_created_at');

        // wishlist_items: ⚠️ 27ms detectado en Telescope
        $this->addIndex('wishlist_items', ['customer_id', 'channel_id'], 'idx_wi_customer_channel');
        $this->addIndex('wishlist_items', ['product_id'], 'idx_wi_product_id');

        // compare_items
        $this->addIndex('compare_items', ['customer_id'], 'idx_cmp_customer_id');

        // customer_notes
        if (Schema::hasTable('customer_notes')) {
            $this->addIndex('customer_notes', ['customer_id'], 'idx_cn_customer_id');
        }

        // =====================================================================
        // FLUJO 6 — Configuración y Core (impacto en TODAS las páginas)
        // =====================================================================

        // core_config: ⚠️ consultado >50 veces por request con CACHE=array
        $this->addIndex('core_config', ['code'], 'idx_cc_code');
        $this->addIndex('core_config', ['channel_code'], 'idx_cc_channel_code');

        // channels: ⚠️ consultado 2 veces por request sin caché
        $this->addIndex('channels', ['hostname'], 'idx_ch_hostname');

        // locales
        $this->addIndex('locales', ['code'], 'idx_loc_code');

        // currencies
        $this->addIndex('currencies', ['code'], 'idx_cur_code');

        // =====================================================================
        // FLUJO 7 — Marketplace / Vendors
        // =====================================================================

        // vendors: tabla custom del marketplace
        if (Schema::hasTable('vendors')) {
            $this->addIndex('vendors', ['customer_id'], 'idx_v_customer_id');
            $this->addIndex('vendors', ['shop_slug'], 'idx_v_shop_slug');
            $this->addIndex('vendors', ['status'], 'idx_v_status');
        }

        // vendor_earnings
        if (Schema::hasTable('vendor_earnings')) {
            $this->addIndex('vendor_earnings', ['vendor_id'], 'idx_ve_vendor_id');
            $this->addIndex('vendor_earnings', ['order_id'], 'idx_ve_order_id');
        }

        // products: vendor_id agregado por migración custom
        if (Schema::hasColumn('products', 'vendor_id')) {
            $this->addIndex('products', ['vendor_id'], 'idx_prod_vendor_id');
        }
    }

    public function down(): void
    {
        // Los índices se eliminan automáticamente con las tablas en migrate:fresh
        // Si se necesita rollback parcial, los índices nombrados pueden eliminarse con dropIndex
    }

    /**
     * Agrega un índice solo si no existe ya en la tabla.
     */
    private function addIndex(string $table, array|string $columns, string $indexName): void
    {
        if (! Schema::hasTable($table)) {
            return;
        }

        if ($this->indexExists($table, $indexName)) {
            return;
        }

        Schema::table($table, function (Blueprint $t) use ($columns, $indexName) {
            $t->index($columns, $indexName);
        });
    }

    /**
     * Verifica si un índice ya existe en MySQL.
     */
    private function indexExists(string $table, string $indexName): bool
    {
        $results = DB::select(
            'SHOW INDEX FROM `' . $table . '` WHERE Key_name = ?',
            [$indexName]
        );

        return ! empty($results);
    }
};
