<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

try {
    // 1. Voir la structure de la table projects
    echo "=== Colonnes de projects ===\n";
    $columns = DB::select('DESCRIBE projects');
    foreach ($columns as $column) {
        if (in_array($column->Field, ['client_id', 'project_name', 'id'])) {
            echo "- " . $column->Field . " (" . $column->Type . ")\n";
        }
    }

    // 2. Voir le projet ID 1
    echo "\n=== Projet ID 1 ===\n";
    $project = DB::table('projects')->where('id', 1)->first();
    if ($project) {
        echo "project_name: " . $project->project_name . "\n";
        echo "client_id: " . ($project->client_id ?? 'NULL') . "\n";
    }

    // 3. Voir tous les projets et leurs clients
    echo "\n=== Tous les projets avec client_id ===\n";
    $projects = DB::table('projects')->select('id', 'project_name', 'client_id')->get();
    foreach ($projects as $p) {
        echo "ID:{$p->id} | {$p->project_name} | client_id: " . ($p->client_id ?? 'NULL') . "\n";
    }

    // 4. Voir les membres du projet 1
    echo "\n=== Membres du projet 1 (project_members) ===\n";
    $members = DB::table('project_members')->where('project_id', 1)->get();
    foreach ($members as $m) {
        $user = DB::table('users')->where('id', $m->user_id)->first();
        echo "user_id: {$m->user_id} | nom: " . ($user ? $user->name : 'N/A') . "\n";
    }

    // 5. Voir les clients dans la table users
    echo "\n=== Clients (role_id client) ===\n";
    $clientRoleId = DB::table('roles')->where('name', 'client')->first();
    if ($clientRoleId) {
        echo "Role client ID: " . $clientRoleId->id . "\n";
        $clients = DB::table('role_user')->where('role_id', $clientRoleId->id)->take(5)->get();
        foreach ($clients as $c) {
            $user = DB::table('users')->where('id', $c->user_id)->first();
            echo "user_id: {$c->user_id} | nom: " . ($user ? $user->name : 'N/A') . "\n";
        }
    }

    // 6. Voir la dernière tâche créée
    echo "\n=== Dernière tâche créée ===\n";
    $task = DB::table('tasks')->orderBy('id', 'desc')->first();
    if ($task) {
        echo "Task ID: {$task->id} | heading: {$task->heading} | project_id: " . ($task->project_id ?? 'NULL') . "\n";
    }


}
catch (\Exception $e) {
    echo "Erreur : " . $e->getMessage() . "\n";
}
