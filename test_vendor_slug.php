<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$vendor = app(\Webkul\Marketplace\Repositories\VendorRepository::class)->findOneByField('customer_id', 1);

if ($vendor) {
    echo "Vendor exists!\n";
    var_dump($vendor->status);
    var_dump($vendor->shop_slug);
    var_dump($vendor->url);
} else {
    echo "No vendor found.\n";
}
