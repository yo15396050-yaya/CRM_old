<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = DB::select('SHOW TABLES');
$dbName = DB::getDatabaseName();
$prop = "Tables_in_" . $dbName;

foreach ($tables as $table) {
    $tableName = $table->$prop;
    $columns = DB::select("SHOW COLUMNS FROM `$tableName` WHERE Extra LIKE '%auto_increment%'");

    if (empty($columns)) {
        // Check if it has an 'id' column
        $hasId = DB::select("SHOW COLUMNS FROM `$tableName` WHERE Field = 'id'");
        if (!empty($hasId)) {
            echo "Table $tableName lacks auto_increment on 'id'\n";
        }
    }

    // Check for ID 0
    $hasZero = DB::select("SELECT COUNT(*) as count FROM `$tableName` WHERE id = 0");
    if (!empty($hasZero) && $hasZero[0]->count > 0) {
        echo "Table $tableName has record with ID 0\n";
    }
}
