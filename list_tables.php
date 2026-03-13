<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

try {
    $tables = \Illuminate\Support\Facades\DB::select('SHOW TABLES');
    $dbName = \Illuminate\Support\Facades\DB::getDatabaseName();
    $key = 'Tables_in_' . $dbName;

    echo "Base de données : $dbName\n";
    echo "Liste des tables :\n";
    foreach ($tables as $table) {
        echo "- " . $table->$key . "\n";
    }
}
catch (\Exception $e) {
    echo "Erreur : " . $e->getMessage();
}
