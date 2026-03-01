<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Customer\Repositories\CustomerRepository;
use Webkul\Marketplace\Models\Vendor;
use Webkul\Product\Repositories\ProductRepository;
use Webkul\Sales\Models\Order;
use Webkul\Sales\Models\OrderItem;
use Illuminate\Support\Facades\Storage;
use Webkul\Product\Models\ProductImage;

class VendorMarketplaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoryRepository = app(CategoryRepository::class);
        $customerRepository = app(CustomerRepository::class);
        $productRepository = app(ProductRepository::class);
        
        $locale = core()->getCurrentLocale()->code ?? 'en';
        
        // --- 0. PRE-CLEANUP to remove duplicated dummy categories and products ---
        $dummySlugs = ['ropa', 'deporte', 'electronica', 'belleza', 'hogar'];
        $dummyTranslations = \Webkul\Category\Models\CategoryTranslation::whereIn('slug', $dummySlugs)->get();
        foreach ($dummyTranslations as $translation) {
            if ($translation->category_id) {
                try {
                    // This will also cascade and delete associated products via Bagisto's events or relationships
                    $categoryRepository->delete($translation->category_id);
                } catch (\Exception $e) {}
            }
        }

        // --- 1. Create Categories via CategoryRepository ---
        $artifactsPath = 'C:/Users/LES INNOVATIONS/.gemini/antigravity/brain/a603257c-b3f1-4f36-a073-7839ec7a9dc0/';
        $categoriesInfo = [
            ['name' => 'Ropa', 'slug' => 'ropa', 'icon' => 'category_clothes_1772157383949.png'],
            ['name' => 'Deporte', 'slug' => 'deporte', 'icon' => 'category_sports_1772157409373.png'],
            ['name' => 'Electrónica', 'slug' => 'electronica', 'icon' => 'category_electronics_1772157371697.png'],
            ['name' => 'Belleza', 'slug' => 'belleza', 'icon' => 'category_beauty_1772157421835.png'],
            ['name' => 'Hogar', 'slug' => 'hogar', 'icon' => 'category_home_1772157447398.png'],
        ];

        $categoryIds = [];
        $rootCategory = $categoryRepository->findOneByField('parent_id', null);
        
        if (!$rootCategory) {
            $rootCategory = $categoryRepository->create([
                'position' => 1,
                'status' => 1,
                'parent_id' => null,
                $locale => [
                    'name' => 'Root',
                    'slug' => 'root',
                    'description' => 'Root category'
                ]
            ]);
        }

        foreach ($categoriesInfo as $index => $catInfo) {
            $cat = $categoryRepository->create([
                'position' => $index + 1,
                'status' => 1,
                'parent_id' => $rootCategory->id,
                $locale => [
                    'name' => $catInfo['name'],
                    'slug' => $catInfo['slug'],
                    'description' => 'Categoría de ' . $catInfo['name'],
                ]
            ]);

            // Assign logo/icon to the category
            $iconSource = $artifactsPath . $catInfo['icon'];
            if (file_exists($iconSource)) {
                $imagePath = 'category/' . $cat->id . '/' . $catInfo['icon'];
                Storage::put($imagePath, file_get_contents($iconSource));
                $cat->logo_path = $imagePath;
                $cat->save();
            }

            $categoryIds[$catInfo['name']] = $cat->id;
        }

        // --- 2. Create Customers via CustomerRepository ---
        $customerRegular = $customerRepository->findOneByField('email', 'cliente@ejemplo.com') ?? $customerRepository->create([
            'first_name' => 'Cliente',
            'last_name' => 'Normal',
            'email' => 'cliente@ejemplo.com',
            'password' => bcrypt('password123'),
            'is_verified' => 1,
            'customer_group_id' => 1,
        ]);

        $customerVendor1 = $customerRepository->findOneByField('email', 'vendor1@ejemplo.com') ?? $customerRepository->create([
            'first_name' => 'Vendedor',
            'last_name' => 'Uno',
            'email' => 'vendor1@ejemplo.com',
            'password' => bcrypt('password123'),
            'is_verified' => 1,
            'customer_group_id' => 1,
        ]);

        $customerVendor2 = $customerRepository->findOneByField('email', 'vendor2@ejemplo.com') ?? $customerRepository->create([
            'first_name' => 'Vendedor',
            'last_name' => 'Dos',
            'email' => 'vendor2@ejemplo.com',
            'password' => bcrypt('password123'),
            'is_verified' => 1,
            'customer_group_id' => 1,
        ]);

        // --- 3. Create Vendors ---
        $vendor1 = Vendor::where('shop_slug', 'tienda-alpha')->first() ?? Vendor::create([
            'customer_id' => $customerVendor1->id,
            'shop_name' => 'Tienda Alpha',
            'shop_slug' => 'tienda-alpha',
            'status' => 'approved',
        ]);

        $vendor2 = Vendor::where('shop_slug', 'tienda-omega')->first() ?? Vendor::create([
            'customer_id' => $customerVendor2->id,
            'shop_name' => 'Tienda Omega',
            'shop_slug' => 'tienda-omega',
            'status' => 'approved',
        ]);

        $vendors = [$vendor1, $vendor2];

        // --- 4. Create 25 Products (5 per Category) ---
        $attributeFamilyId = \Webkul\Attribute\Models\AttributeFamily::first()->id ?? 1;
        $createdProducts = [];

        foreach ($categoriesInfo as $catInfo) {
            for ($i = 1; $i <= 5; $i++) {
                $currentVendor = $vendors[($i % 2 === 0) ? 1 : 0];
                $productName = "Producto $i de {$catInfo['name']}";
                $sku = "SKU-{$catInfo['slug']}-{$i}-" . rand(100, 999);

                $product = $productRepository->create([
                    'type' => 'simple',
                    'attribute_family_id' => $attributeFamilyId,
                    'sku' => $sku,
                ]);

                // Set Vendor ID explicitly
                $product->vendor_id = $currentVendor->id;
                $product->save();
                
                \Illuminate\Database\Eloquent\Model::reguard();
                
                // Build EAV Update Payload
                $updateData = [
                    'sku' => $sku,
                    'name' => $productName,
                    'url_key' => strtolower(str_replace(' ', '-', $productName)) . '-' . rand(10, 99),
                    'tax_category_id' => '',
                    'new' => 1,
                    'featured' => 0,
                    'visible_individually' => 1,
                    'status' => 1,
                    'color' => '',
                    'size' => '',
                    'brand' => '',
                    'guest_checkout' => 1,
                    'short_description' => "Excelente $productName para uso diario.",
                    'description' => "Descripción detallada del $productName garantizando la mejor calidad del mercado.",
                    'price' => rand(10, 500) . '.00',
                    'weight' => '1',
                    'categories' => [$categoryIds[$catInfo['name']]],
                    'channel' => core()->getCurrentChannelCode() ?? 'default',
                    'locale' => $locale,
                ];

                $productRepository->update($updateData, $product->id);
                \Illuminate\Database\Eloquent\Model::unguard();

                // Assign image to product (reusing the category icon as a placeholder)
                $iconSource = $artifactsPath . $catInfo['icon'];
                if (file_exists($iconSource)) {
                    $prodImageName = 'product_img_' . $product->id . '_' . $catInfo['icon'];
                    $prodImagePath = 'product/' . $product->id . '/' . $prodImageName;
                    Storage::put($prodImagePath, file_get_contents($iconSource));
                    
                    ProductImage::create([
                        'path' => $prodImagePath,
                        'product_id' => $product->id,
                    ]);
                }
                
                $createdProducts[$catInfo['name']][] = $product;
            }
        }

        // --- 5. Generate dummy Orders (1 Belleza, 1 Electrónica, 1 Ropa) ---
        $orderCategories = ['Belleza', 'Electrónica', 'Ropa'];
        foreach ($orderCategories as $index => $catName) {
            $product = $createdProducts[$catName][0] ?? null;
            if (!$product) continue;

            $vendor = Vendor::find($product->vendor_id);
            $order = Order::create([
                'increment_id' => 'ORD-' . date('Y') . '-' . rand(1000, 9999),
                'status' => 'completed',
                'channel_name' => 'Default',
                'is_guest' => 0,
                'customer_email' => $customerRegular->email,
                'customer_first_name' => $customerRegular->first_name,
                'customer_last_name' => $customerRegular->last_name,
                'shipping_method' => 'flatrate_flatrate',
                'shipping_title' => 'Flat Rate',
                'shipping_description' => 'Flat Rate Shipping',
                'grand_total' => 150.00,
                'base_grand_total' => 150.00,
                'sub_total' => 100.00,
                'base_sub_total' => 100.00,
                'discount_amount' => 0,
                'base_discount_amount' => 0,
                'cart_id' => null,
                'customer_id' => $customerRegular->id,
            ]);

            OrderItem::create([
                'sku' => $product->sku,
                'type' => 'simple',
                'name' => $product->name ?? 'Dummy Product',
                'weight' => 1,
                'total_weight' => 1,
                'qty_ordered' => 1,
                'price' => 100.00,
                'base_price' => 100.00,
                'total' => 100.00,
                'base_total' => 100.00,
                'product_id' => $product->id,
                'order_id' => $order->id,
            ]);
        }
    }
}
