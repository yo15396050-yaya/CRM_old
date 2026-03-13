<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $columns = \Illuminate\Support\Facades\DB::select('DESCRIBE tasks');
    echo "Structure de la table tasks :\n";
    foreach ($columns as $column) {
        echo "- " . $column->Field . " (" . $column->Type . ")\n";
    }
}
catch (\Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
