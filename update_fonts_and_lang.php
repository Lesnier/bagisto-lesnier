<?php

require __DIR__.'/vendor/autoload.php';
$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

// Update fonts in database Theme Customizations
DB::table('theme_customization_translations')
    ->update(['options' => DB::raw("REPLACE(options, 'DM Serif Display', 'Poppins')")]);
DB::table('theme_customization_translations')
    ->update(['options' => DB::raw("REPLACE(options, 'dmserif', 'poppins')")]);

echo "Fonts updated in database.\n";

// Update ES translations manually to avoid indentation parsing issues
$esLangPath = __DIR__ . '/packages/Webkul/Shop/src/Resources/lang/es/app.php';
if (file_exists($esLangPath)) {
    $content = file_get_contents($esLangPath);
    $content = str_replace("'ID de IVA'", "'RUC/CÉDULA/PASAPORTE'", $content);
    $content = str_replace("'Número de IVA'", "'RUC/CÉDULA/PASAPORTE'", $content);
    file_put_contents($esLangPath, $content);
    echo "ES translations updated.\n";
} else {
    echo "ES translation file not found.\n";
}

