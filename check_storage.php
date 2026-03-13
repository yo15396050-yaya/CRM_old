<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\StorageSetting;

$settings = StorageSetting::all();
foreach ($settings as $s) {
    echo "Filesystem: {$s->filesystem}, Status: {$s->status}\n";
}

if ($settings->where('status', 'enabled')->count() == 0) {
    echo "ATTENTION: Aucun réglage de stockage n'est activé !\n";
}
