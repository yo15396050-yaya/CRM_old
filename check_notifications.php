<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\ProNotificationLog;
use Illuminate\Support\Facades\Log;

echo "--- TWILIO CONFIG ---\n";
echo "SID: " . (env('TWILIO_SID') ? 'Configuré' : 'Manquant') . "\n";
echo "Token: " . (env('TWILIO_AUTH_TOKEN') && env('TWILIO_AUTH_TOKEN') !== '[AuthToken]' ? 'Configuré' : 'Manquant') . "\n";
echo "WhatsApp Number: " . env('TWILIO_WHATSAPP_NUMBER') . "\n";
echo "SMS Number: " . env('TWILIO_SMS_NUMBER') . "\n";

echo "\n--- RECENT LOGS ---\n";
$logs = ProNotificationLog::latest()->take(20)->get();
foreach ($logs as $log) {
    echo "[{$log->created_at}] Tâche:{$log->task_id} Canal:{$log->channel} Statut:{$log->status} Erreur:{$log->error_message}\n";
}
