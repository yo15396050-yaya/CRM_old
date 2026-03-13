<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\PusherSetting;

try {
    $settings = PusherSetting::first() ?: new PusherSetting();
    $settings->status = 1; // Activé
    $settings->messages = 1; // Messagerie activée
    $settings->pusher_app_id = 'app-id';
    $settings->pusher_app_key = 'app-key';
    $settings->pusher_app_secret = 'app-secret';
    $settings->pusher_cluster = 'mt1';
    $settings->force_tls = 0; // Pas de HTTPS pour le local
    $settings->save();

    echo "✅ Les paramètres Pusher ont été mis à jour pour Soketi (local).\n";
}
catch (\Exception $e) {
    echo "❌ Erreur : " . $e->getMessage() . "\n";
}
