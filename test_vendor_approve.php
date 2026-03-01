<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $vendorRepository = app(\Webkul\Marketplace\Repositories\VendorRepository::class);
    $vendor = $vendorRepository->findOrFail(1);
    
    // Simulate approval
    echo "Approving vendor ID 1...\n";
    $vendorRepository->update(['status' => 'approved', 'rejection_reason' => null], 1);
    
    echo "Vendor successfully approved!\n";
} catch (\Exception $e) {
    echo "EXCEPTION CAUGHT: " . $e->getMessage() . "\n";
    echo $e->getTraceAsString();
}
