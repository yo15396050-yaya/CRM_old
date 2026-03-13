<?php
use App\Models\User;

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$clients = User::allClients(null, false, 'all');
echo "Count: " . $clients->count() . "\n";
foreach ($clients as $client) {
    echo "ID: " . $client->id . " - Name: " . $client->name . "\n";
}
