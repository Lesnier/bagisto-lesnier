<?php

use Illuminate\Support\Facades\Storage;
use Webkul\Category\Repositories\CategoryRepository;
use Webkul\Product\Repositories\ProductRepository;
use Illuminate\Support\Facades\DB;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$categoryRepository = app(CategoryRepository::class);
$productRepository = app(ProductRepository::class);

echo "Task 1: Add Image to MEN category...\n";
$imagePath = 'C:\\Users\\LES INNOVATIONS\\.gemini\\antigravity\\brain\\a603257c-b3f1-4f36-a073-7839ec7a9dc0\\category_men_1772159643712.png';
if (file_exists($imagePath)) {
    $imageName = basename($imagePath);
    $newPath = 'category/' . uniqid() . '_' . $imageName;
    Storage::disk('public')->put($newPath, file_get_contents($imagePath));

    $menCategory = $categoryRepository->find(2);
    if ($menCategory) {
        $menCategory->logo_path = $newPath;
        $menCategory->save();
        echo "Men category updated.\n";
    }
}

echo "Task 2: Duplicate 4 products per new category...\n";
$targetCategories = [2, 3, 4, 5, 6, 7, 8, 9, 10, 11];
// Fetch at least 5 existing products to copy from
$baseProductsQuery = DB::table('products')->where('type', 'simple')->pluck('id')->toArray();
if (empty($baseProductsQuery)) {
    echo "No base products found to duplicate. Run product seeder first.\n";
    exit;
}

foreach ($targetCategories as $catId) {
    echo "Seeding 4 products for category ID $catId...\n";
    for ($i = 0; $i < 4; $i++) {
        $randomBaseId = reset($baseProductsQuery);
        shuffle($baseProductsQuery);
        
        try {
            // Check if product exists before copying
            if (!DB::table('products')->where('id', $randomBaseId)->exists()) {
                continue; // Skip if invalid
            }
            
            $copied = $productRepository->copy($randomBaseId);
            
            // Delete old category associations for the copied product
            DB::table('product_categories')->where('product_id', $copied->id)->delete();
            
            // Assign to the targeted category
            DB::table('product_categories')->insert([
                'product_id'  => $copied->id,
                'category_id' => $catId
            ]);
        } catch (\Exception $e) {
            echo "Error copying product: " . $e->getMessage() . "\n";
        }
    }
}
echo "All done.\n";
