<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

$emails = ['f54631893@gmail.com', 'yaya.ouattara@dc-knowing.com', 'guehirita2@gmail.com'];
foreach ($emails as $email) {
    $user = User::where('email', $email)->first();
    if ($user) {
        echo "User: {$user->email} | Name: {$user->name} | Mobile: " . ($user->mobile ?: 'N/A') . " | WhatsApp: " . ($user->whatsapp ?: 'N/A') . "\n";
    }
    else {
        echo "User: {$email} NOT FOUND\n";
    }
}
