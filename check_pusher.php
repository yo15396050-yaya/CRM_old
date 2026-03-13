<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $settings = \App\Models\PusherSetting::first();
    echo "ID: " . ($settings->id ?? 'N/A') . "\n";
    echo "Status: " . ($settings->status ?? 'N/A') . "\n";
    echo "App ID: " . ($settings->pusher_app_id ?? 'N/A') . "\n";
    echo "App Key: " . ($settings->pusher_app_key ?? 'N/A') . "\n";
    echo "App Secret: " . ($settings->pusher_app_secret ?? 'N/A') . "\n";
    echo "Cluster: " . ($settings->pusher_cluster ?? 'N/A') . "\n";
    echo "Force TLS: " . ($settings->force_tls ?? 'N/A') . "\n";
}
catch (\Exception $e) {
    echo "Error: " . $e->getMessage();
}
