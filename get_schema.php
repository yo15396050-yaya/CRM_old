<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$tables = ['users', 'client_details', 'employee_details', 'employee_activity'];

foreach ($tables as $table) {
    echo "--- Table: $table ---\n";
    try {
        $res = DB::select("SHOW CREATE TABLE `$table`")[0];
        $prop = "Create Table";
        echo $res->$prop . "\n\n";
    }
    catch (\Exception $e) {
        echo "Error: " . $e->getMessage() . "\n\n";
    }
}
