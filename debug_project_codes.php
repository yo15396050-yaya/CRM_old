<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

$codeToCheck = 'DLG#075';
$exists = DB::table('projects')->where('project_short_code', $codeToCheck)->exists();
echo "Code $codeToCheck exists: " . ($exists ? 'YES' : 'NO') . "\n";

$lastProjects = DB::table('projects')
    ->select('id', 'project_short_code')
    ->orderBy('id', 'DESC')
    ->limit(10)
    ->get();

echo "\nLast 10 projects:\n";
foreach ($lastProjects as $project) {
    echo "ID: {$project->id}, Code: {$project->project_short_code}\n";
}

$highestCode = DB::table('projects')
    ->where('project_short_code', 'like', 'DLG#%')
    ->orderBy('project_short_code', 'DESC')
    ->first();

if ($highestCode) {
    echo "\nHighest DLG# code: {$highestCode->project_short_code}\n";
}
