<?php
require_once 'vendor/autoload.php';
use Twilio\Rest\Client;

$sid = 'AC04621a5694e575888eca16ff1b854ccb';
$token = '4db08c0a0d5e242e7d729008765d4e68';

echo "Testing Twilio SID: $sid\n";
echo "Testing Twilio Token: $token\n";

try {
    $curlClient = new \Twilio\Http\CurlClient([
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_SSL_VERIFYHOST => false,
    ]);
    $client = new Client($sid, $token, null, null, $curlClient);
    // Fetch account info
    $account = $client->api->v2010->accounts($sid)->fetch();
    echo "Account Status: " . $account->status . "\n";
    echo "Friendly Name: " . $account->friendlyName . "\n";
} catch (\Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
