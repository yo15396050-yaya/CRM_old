<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    \Illuminate\Support\Facades\Artisan::call('migrate', [
        '--force' => true,
    ]);
    echo \Illuminate\Support\Facades\Artisan::output();
}
catch (\Exception $e) {
    echo "Erreur lors de la migration : " . $e->getMessage();
}
