<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Webkul\Customer\Models\Customer;
use Webkul\Marketplace\Models\Vendor;
use Webkul\Product\Models\Product;

class VendorMarketplaceTest extends TestCase
{
    use RefreshDatabase;

    protected $customer;
    protected $vendor;

    protected function setUp(): void
    {
        parent::setUp();

        // Create a customer manually to bypass broken factory
        $this->customer = Customer::create([
            'first_name' => 'John',
            'last_name' => 'Doe',
            'email' => 'vendor' . rand(1, 10000) . '@example.com',
            'password' => bcrypt('password123'),
            'is_verified' => 1,
            'customer_group_id' => 1,
        ]);

        // Create an approved vendor profile for the customer
        $this->vendor = Vendor::create([
            'customer_id' => $this->customer->id,
            'shop_name' => 'Test Vendor Shop',
            'status' => 'approved',
        ]);
    }

    public function test_approved_vendor_can_access_dashboard()
    {
        $this->actingAs($this->customer, 'customer')
            ->get(route('vendor.dashboard'))
            ->assertStatus(200)
            ->assertSee('Test Vendor Shop');
    }

    public function test_unapproved_vendor_cannot_access_dashboard()
    {
        $this->vendor->update(['status' => 'pending']);

        $this->actingAs($this->customer, 'customer')
            ->get(route('vendor.dashboard'))
            ->assertRedirect(); // Should hit EnsureIsVendor middleware and redirect
    }

    public function test_vendor_can_view_product_creation_page()
    {
        $this->actingAs($this->customer, 'customer')
            ->get(route('vendor.products.create'))
            ->assertStatus(200);
    }

    public function test_vendor_can_create_a_product_and_vendor_id_is_assigned()
    {
        $payload = [
            'type' => 'simple',
            'attribute_family_id' => 1,
            'sku' => 'TEST-SKU-' . rand(1000, 9999),
        ];

        $response = $this->actingAs($this->customer, 'customer')
            ->post(route('vendor.products.store'), $payload);

        $product = Product::where('sku', $payload['sku'])->first();

        $this->assertNotNull($product);
        $this->assertEquals((int)$this->vendor->id, (int)$product->vendor_id);

        $response->assertRedirect(route('vendor.products.edit', $product->id));
    }

    public function test_vendor_can_edit_own_product()
    {
        $product = Product::create([
            'sku' => 'OWN-PROD-' . rand(1000, 9999),
            'type' => 'simple',
            'attribute_family_id' => 1,
            'vendor_id' => $this->vendor->id,
        ]);

        $this->actingAs($this->customer, 'customer')
            ->get(route('vendor.products.edit', $product->id))
            ->assertStatus(200);
    }

    public function test_vendor_cannot_edit_another_vendors_product()
    {
        $otherCustomer = Customer::create([
            'first_name' => 'Jane',
            'last_name' => 'Smith',
            'email' => 'other' . rand(1, 10000) . '@example.com',
            'password' => bcrypt('password123'),
            'is_verified' => 1,
            'customer_group_id' => 1,
        ]);

        $otherVendor = Vendor::create([
            'customer_id' => $otherCustomer->id,
            'shop_name' => 'Other Shop',
            'status' => 'approved',
        ]);

        $product = Product::create([
            'sku' => 'OTHER-PROD-' . rand(1000, 9999),
            'type' => 'simple',
            'attribute_family_id' => 1,
            'vendor_id' => $otherVendor->id,
        ]);

        $this->actingAs($this->customer, 'customer')
            ->get(route('vendor.products.edit', $product->id))
            ->assertStatus(403);
    }

    public function test_vendor_can_view_earnings()
    {
        $this->actingAs($this->customer, 'customer')
            ->get(route('vendor.earnings.index'))
            ->assertStatus(200);
    }

    public function test_vendor_can_view_orders()
    {
        $this->actingAs($this->customer, 'customer')
            ->get(route('vendor.orders.index'))
            ->assertStatus(200);
    }

    public function test_vendor_can_update_settings()
    {
        $payload = [
            'shop_name' => 'Updated Shop Name',
            'shop_description' => 'New Description',
            'bank_account_holder' => 'Jane Doe',
        ];

        $this->actingAs($this->customer, 'customer')
            ->post(route('vendor.settings.update'), $payload)
            ->assertRedirect();

        $this->vendor->refresh();
        $this->assertEquals('Updated Shop Name', $this->vendor->shop_name);
        $this->assertEquals('Jane Doe', $this->vendor->bank_account_holder);
    }
}
