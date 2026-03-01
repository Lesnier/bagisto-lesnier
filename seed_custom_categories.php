<?php

use Illuminate\Support\Facades\Storage;
use Webkul\Category\Repositories\CategoryRepository;
use Illuminate\Http\File;

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$categoryRepository = app(CategoryRepository::class);

$categories = [
    [
        'en' => ['name' => 'Pets', 'slug' => 'pets', 'description' => 'Everything for your pets', 'meta_title' => 'Pets'],
        'es' => ['name' => 'Mascotas', 'slug' => 'mascotas', 'description' => 'Todo para tus mascotas', 'meta_title' => 'Mascotas'],
        'image' => 'C:\\Users\\LES INNOVATIONS\\.gemini\\antigravity\\brain\\a603257c-b3f1-4f36-a073-7839ec7a9dc0\\category_pets_1772157358345.png'
    ],
    [
        'en' => ['name' => 'Electronics', 'slug' => 'electronics', 'description' => 'Latest electronics and gadgets', 'meta_title' => 'Electronics'],
        'es' => ['name' => 'Electrónica', 'slug' => 'electronica', 'description' => 'Última tecnología y gadgets', 'meta_title' => 'Electrónica'],
        'image' => 'C:\\Users\\LES INNOVATIONS\\.gemini\\antigravity\\brain\\a603257c-b3f1-4f36-a073-7839ec7a9dc0\\category_electronics_1772157371697.png'
    ],
    [
        'en' => ['name' => 'Clothes & Shoes', 'slug' => 'clothes-shoes', 'description' => 'Fashion and apparel', 'meta_title' => 'Clothes & Shoes'],
        'es' => ['name' => 'Ropa y Calzado', 'slug' => 'ropa-calzado', 'description' => 'Moda y vestimenta para todos', 'meta_title' => 'Ropa y Calzado'],
        'image' => 'C:\\Users\\LES INNOVATIONS\\.gemini\\antigravity\\brain\\a603257c-b3f1-4f36-a073-7839ec7a9dc0\\category_clothes_1772157383949.png'
    ],
    [
        'en' => ['name' => 'Games', 'slug' => 'games', 'description' => 'Video games and consoles', 'meta_title' => 'Games'],
        'es' => ['name' => 'Videojuegos', 'slug' => 'videojuegos', 'description' => 'Juegos de video y consolas', 'meta_title' => 'Videojuegos'],
        'image' => 'C:\\Users\\LES INNOVATIONS\\.gemini\\antigravity\\brain\\a603257c-b3f1-4f36-a073-7839ec7a9dc0\\category_games_1772157396419.png'
    ],
    [
        'en' => ['name' => 'Sports', 'slug' => 'sports', 'description' => 'Sports and fitness equipment', 'meta_title' => 'Sports'],
        'es' => ['name' => 'Deportes', 'slug' => 'deportes', 'description' => 'Equipos de deporte y fitness', 'meta_title' => 'Deportes'],
        'image' => 'C:\\Users\\LES INNOVATIONS\\.gemini\\antigravity\\brain\\a603257c-b3f1-4f36-a073-7839ec7a9dc0\\category_sports_1772157409373.png'
    ],
    [
        'en' => ['name' => 'Beauty', 'slug' => 'beauty', 'description' => 'Beauty and personal care', 'meta_title' => 'Beauty'],
        'es' => ['name' => 'Belleza', 'slug' => 'belleza', 'description' => 'Cuidado personal y belleza', 'meta_title' => 'Belleza'],
        'image' => 'C:\\Users\\LES INNOVATIONS\\.gemini\\antigravity\\brain\\a603257c-b3f1-4f36-a073-7839ec7a9dc0\\category_beauty_1772157421835.png'
    ],
    [
        'en' => ['name' => 'Vehicles', 'slug' => 'vehicles', 'description' => 'Cars, motorcycles and accessories', 'meta_title' => 'Vehicles'],
        'es' => ['name' => 'Vehículos', 'slug' => 'vehiculos', 'description' => 'Carros, motos y repuestos', 'meta_title' => 'Vehículos'],
        'image' => 'C:\\Users\\LES INNOVATIONS\\.gemini\\antigravity\\brain\\a603257c-b3f1-4f36-a073-7839ec7a9dc0\\category_vehicles_1772157433046.png'
    ],
    [
        'en' => ['name' => 'Home', 'slug' => 'home', 'description' => 'Home decor and furniture', 'meta_title' => 'Home'],
        'es' => ['name' => 'Hogar', 'slug' => 'hogar', 'description' => 'Decoración y muebles para el hogar', 'meta_title' => 'Hogar'],
        'image' => 'C:\\Users\\LES INNOVATIONS\\.gemini\\antigravity\\brain\\a603257c-b3f1-4f36-a073-7839ec7a9dc0\\category_home_1772157447398.png'
    ]
];

foreach ($categories as $index => $data) {
    echo "Creating category: {$data['es']['name']}\n";

    $imageName = basename($data['image']);
    $newPath = 'category/' . uniqid() . '_' . $imageName;
    Storage::disk('public')->put($newPath, file_get_contents($data['image']));

    $payload = [
        'position' => $index + 2,
        'status' => 1,
        'display_mode' => 'products_and_description',
        'parent_id' => 1,
        'en' => $data['en'],
        'es' => $data['es'],
    ];

    $category = $categoryRepository->create($payload);
    
    // Disable timestamps so we don't trigger observers again if not needed, 
    // or just save the model. Wait, Bagisto needs logo_path in the model.
    $category->logo_path = $newPath;
    $category->save();
    
    echo "Done.\n";
}

echo "All complete.\n";
