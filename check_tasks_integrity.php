<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Task;

$tasks = Task::withoutGlobalScopes()->orderBy('id', 'desc')->take(10)->get();

echo "ID | Heading | Project ID | Created At\n";
echo "-------------------------------------------\n";
foreach ($tasks as $task) {
    echo "{$task->id} | {$task->heading} | {$task->project_id} | {$task->created_at}\n";
}
