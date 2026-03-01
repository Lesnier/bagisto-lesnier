<?php

$dir = __DIR__ . '/public/themes/shop/default/build/assets/';
$files = glob($dir . '*.css');

foreach ($files as $file) {
    if (is_file($file)) {
        $content = file_get_contents($file);
        $content = str_ireplace('dm serif display', 'Poppins', $content);
        $content = str_ireplace('DM Serif Display', 'Poppins', $content);
        file_put_contents($file, $content);
        echo "Updated font in: " . basename($file) . "\n";
    }
}
echo "All CSS compiled files updated.\n";
