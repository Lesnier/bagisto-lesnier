<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Boot app
$request = Illuminate\Http\Request::create('/admin/marketplace/vendors/1/approve', 'POST');

// Assuming admin is user ID 1 for testing purposes. We must authenticate to hit admin routes.
auth()->guard('admin')->loginUsingId(1);

$response = $kernel->handle($request);

echo "HTTP STATUS: " . $response->getStatusCode() . "\n";
echo "RESPONSE BODY:\n" . substr($response->getContent(), 0, 2000) . "\n";

if ($response->exception) {
    echo "EXCEPTION MESSAGE: " . $response->exception->getMessage() . "\n";
    echo "EXCEPTION TRACE: " . $response->exception->getTraceAsString() . "\n";
}
