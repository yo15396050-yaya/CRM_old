<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Http;
use Twilio\Rest\Client;

$twilioSid = 'AC04621a5694e575888eca16ff1b854ccb';
$twilioToken = '4db08c0a0d5e242e7d729008765d4e68';
$infobipKey = '2bb5ffccab9857d745e9000147738020-76f6d521-f500-45af-ba71-59d81d9bbe01';
$infobipBase = '1egjqn.api.infobip.com';

echo "--- Testing Twilio ---\n";
try {
    $curlClient = new \Twilio\Http\CurlClient([
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    $twilio = new Client($twilioSid, $twilioToken, null, null, $curlClient);
    $account = $twilio->api->v2010->accounts($twilioSid)->fetch();
    echo "Twilio OK: Account Status is " . $account->status . "\n";
} catch (\Exception $e) {
    echo "Twilio FAILED: " . $e->getMessage() . "\n";
}

echo "\n--- Testing Infobip SMS ---\n";
try {
    $response = Http::withOptions(['verify' => false])->withHeaders([
        'Authorization' => 'App ' . $infobipKey,
        'Content-Type' => 'application/json',
        'Accept' => 'application/json',
    ])->post('https://' . $infobipBase . '/sms/2/text/advanced', [
        'messages' => [
            [
                'from' => 'TestCRM',
                'destinations' => [['to' => '2250584388979']],
                'text' => 'Verification des nouveaux identifiants Infobip.',
            ],
        ],
    ]);
    
    if ($response->successful()) {
        echo "Infobip OK: " . $response->body() . "\n";
    } else {
        echo "Infobip FAILED: Status " . $response->status() . " - " . $response->body() . "\n";
    }
} catch (\Exception $e) {
    echo "Infobip ERROR: " . $e->getMessage() . "\n";
}
