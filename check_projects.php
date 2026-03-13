<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);
$kernel->handle(Illuminate\Http\Request::capture());

use App\Models\Project;

$lastProjects = Project::orderBy('id', 'desc')->take(5)->get();

foreach ($lastProjects as $project) {
    echo "ID: " . $project->id . "\n";
    echo "Name: " . $project->project_name . "\n";
    echo "Status: " . $project->status . "\n";
    echo "Added By: " . $project->added_by . "\n";
    echo "Company ID: " . $project->company_id . "\n";
    echo "Public: " . $project->public . "\n";
    echo "Created At: " . $project->created_at . "\n";
    echo "---------------------------\n";
}
