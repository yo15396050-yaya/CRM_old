<?php
/**
 * fix_smtp_server.php
 * ⚠️  Placer dans le dossier PUBLIC/ du serveur  ⚠️
 * Accès : https://tacheflux.dc-knowing.com/fix_smtp_server.php
 * ⚠️  SUPPRIMER APRÈS UTILISATION ⚠️
 */

define('SECRET_TOKEN', 'dck2026fix');

if (!isset($_GET['token']) || $_GET['token'] !== SECRET_TOKEN) {
    die('Accès refusé. Ajoutez ?token=dck2026fix à l\'URL.');
}

// Depuis public/, remonter d'un niveau pour trouver la racine Laravel
require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo '<pre style="font-family:monospace; padding:20px; background:#1e1e1e; color:#d4d4d4; font-size:14px;">';
echo "=== DIAGNOSTIC SMTP - FlowTask CRM ===\n\n";

// 1. Lire les paramètres actuels en base
$smtp = DB::table('smtp_settings')->first();
$emailNotif = DB::table('email_notification_settings')->where('slug', 'user-assign-to-task')->first();

echo "📋 SMTP EN BASE DE DONNÉES:\n";
echo "  mail_driver      : " . ($smtp->mail_driver ?? 'N/A') . "\n";
echo "  mail_host        : " . ($smtp->mail_host ?? 'N/A') . "\n";
echo "  mail_port        : " . ($smtp->mail_port ?? 'N/A') . "\n";
echo "  mail_username    : " . ($smtp->mail_username ?? 'N/A') . "\n";
echo "  mail_encryption  : " . ($smtp->mail_encryption ?? 'N/A') . "\n";
echo "  mail_from_email  : " . ($smtp->mail_from_email ?? 'N/A') . "\n";
echo "  mail_from_name   : " . ($smtp->mail_from_name ?? 'N/A') . "\n";
echo "  mail_connection  : " . ($smtp->mail_connection ?? 'N/A') . "  ← (sync = envoi direct, database = bloqué)\n";
echo "  verified         : " . ($smtp->verified ?? 'N/A') . "\n";
echo "  email_verified   : " . ($smtp->email_verified ?? 'N/A') . "\n\n";

echo "📋 NOTIFICATION 'user-assign-to-task':\n";
if ($emailNotif) {
    echo "  send_email : " . $emailNotif->send_email . "\n\n";
} else {
    echo "  ⚠️  INTROUVABLE EN BASE !\n\n";
}

// 2. Appliquer corrections si ?fix=1
if (isset($_GET['fix']) && $_GET['fix'] == '1') {

    echo "🔧 APPLICATION DES CORRECTIONS...\n\n";

    $passwordCrypt = Crypt::encryptString('Task@1234567');

    DB::table('smtp_settings')->update([
        'mail_driver'      => 'smtp',
        'mail_host'        => 'mail.dc-knowing.com',
        'mail_port'        => '465',
        'mail_username'    => 'info.taskflow@dc-knowing.com',
        'mail_password'    => $passwordCrypt,
        'mail_encryption'  => 'ssl',
        'mail_from_email'  => 'info.taskflow@dc-knowing.com',
        'mail_from_name'   => 'FlowTask CRM',
        'mail_connection'  => 'sync',
        'verified'         => 1,
        'email_verified'   => 1,
    ]);
    echo "  ✅ Paramètres SMTP mis à jour (mail_connection=sync)\n";

    if ($emailNotif) {
        DB::table('email_notification_settings')
            ->where('slug', 'user-assign-to-task')
            ->update(['send_email' => 'yes']);
    } else {
        DB::table('email_notification_settings')->insert([
            'setting_name' => 'User Assign to Task',
            'slug'         => 'user-assign-to-task',
            'send_email'   => 'yes',
            'send_push'    => 'no',
            'send_slack'   => 'no',
            'created_at'   => now(),
            'updated_at'   => now(),
        ]);
    }
    echo "  ✅ Notification 'user-assign-to-task' → send_email=yes\n";

    DB::table('email_notification_settings')
        ->whereIn('slug', ['task-completed', 'task-status-updated'])
        ->update(['send_email' => 'yes']);
    echo "  ✅ Notifications tâche complétée et statut activées\n\n";

    $smtp = DB::table('smtp_settings')->first();
    echo "📋 VÉRIFICATION APRÈS CORRECTION:\n";
    echo "  mail_connection : " . $smtp->mail_connection . "\n";
    echo "  email_verified  : " . $smtp->email_verified . "\n\n";
}

// 3. Test envoi email si ?test_email=xxx
if (!empty($_GET['test_email'])) {
    $testEmail = $_GET['test_email'];
    echo "📧 TEST ENVOI EMAIL vers : $testEmail\n";
    try {
        $smtpFresh = DB::table('smtp_settings')->first();
        $pwd = Crypt::decryptString($smtpFresh->mail_password);

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', $smtpFresh->mail_host);
        Config::set('mail.mailers.smtp.port', $smtpFresh->mail_port);
        Config::set('mail.mailers.smtp.username', $smtpFresh->mail_username);
        Config::set('mail.mailers.smtp.password', $pwd);
        Config::set('mail.mailers.smtp.encryption', $smtpFresh->mail_encryption);
        Config::set('mail.from.address', $smtpFresh->mail_from_email);
        Config::set('mail.from.name', $smtpFresh->mail_from_name);

        \Illuminate\Support\Facades\Mail::raw(
            'Test SMTP FlowTask CRM - ' . now(),
            function($msg) use ($testEmail, $smtpFresh) {
                $msg->to($testEmail)
                    ->subject('✅ Test SMTP FlowTask - ' . now())
                    ->from($smtpFresh->mail_from_email, $smtpFresh->mail_from_name);
            }
        );
        echo "  ✅ Email envoyé ! Vérifiez votre boîte (et les SPAMS)\n";
    } catch (\Exception $e) {
        echo "  ❌ ERREUR : " . $e->getMessage() . "\n";
    }
}

if (!isset($_GET['fix'])) {
    echo "👉 Pour corriger : ajoutez &fix=1 à l'URL\n";
    echo "👉 Pour tester   : ajoutez &test_email=votre@email.com\n";
}

echo "\n⚠️  SUPPRIMEZ CE FICHIER APRÈS UTILISATION !\n";
echo '</pre>';
?>
