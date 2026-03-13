<?php
use App\Models\User;
use Illuminate\Support\Facades\DB;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$roles = DB::table('roles')->get();
echo "Roles:\n";
foreach ($roles as $role) {
    echo "- " . $role->name . " (ID: " . $role->id . ")\n";
}

$clientCount = DB::table('role_user')
    ->join('roles', 'roles.id', '=', 'role_user.role_id')
    ->where('roles.name', 'client')
    ->count();
echo "\nUsers with 'client' role: " . $clientCount . "\n";

$clientWithDetailsCount = DB::table('role_user')
    ->join('roles', 'roles.id', '=', 'role_user.role_id')
    ->join('client_details', 'client_details.user_id', '=', 'role_user.user_id')
    ->where('roles.name', 'client')
    ->count();
echo "Clients with client_details: " . $clientWithDetailsCount . "\n";
