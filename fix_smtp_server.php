<?php
/**
 * =====================================================
 * fix_smtp_server.php
 * Script de diagnostic et correction SMTP pour le serveur
 * À placer dans le dossier racine du projet et accéder via :
 * https://tacheflux.dc-knowing.com/fix_smtp_server.php
 *
 * ⚠️  SUPPRIMER CE FICHIER APRÈS UTILISATION ⚠️
 * =====================================================
 */

// Sécurité : token requis dans l'URL pour éviter l'accès non autorisé
define('SECRET_TOKEN', 'dck2026fix');

if (!isset($_GET['token']) || $_GET['token'] !== SECRET_TOKEN) {
    die('Accès refusé. Ajoutez ?token=dck2026fix à l\'URL.');
}

// Charger Laravel
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

// Connexion directe à la base de données
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Config;

$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

echo '<pre style="font-family:monospace; padding:20px; background:#1e1e1e; color:#d4d4d4;">';
echo "=== DIAGNOSTIC SMTP - FlowTask CRM ===\n\n";

// -----------------------------------------------
// 1. Lire les paramètres actuels en base
// -----------------------------------------------
$smtp = DB::table('smtp_settings')->first();
$emailNotif = DB::table('email_notification_settings')->where('slug', 'user-assign-to-task')->first();

echo "📋 PARAMÈTRES SMTP EN BASE DE DONNÉES:\n";
echo "  mail_driver      : " . ($smtp->mail_driver ?? 'N/A') . "\n";
echo "  mail_host        : " . ($smtp->mail_host ?? 'N/A') . "\n";
echo "  mail_port        : " . ($smtp->mail_port ?? 'N/A') . "\n";
echo "  mail_username    : " . ($smtp->mail_username ?? 'N/A') . "\n";
echo "  mail_encryption  : " . ($smtp->mail_encryption ?? 'N/A') . "\n";
echo "  mail_from_email  : " . ($smtp->mail_from_email ?? 'N/A') . "\n";
echo "  mail_from_name   : " . ($smtp->mail_from_name ?? 'N/A') . "\n";
echo "  mail_connection  : " . ($smtp->mail_connection ?? 'N/A') . " ← QUEUE TYPE\n";
echo "  verified         : " . ($smtp->verified ?? 'N/A') . "\n";
echo "  email_verified   : " . ($smtp->email_verified ?? 'N/A') . "\n\n";

echo "📋 NOTIFICATION 'user-assign-to-task':\n";
if ($emailNotif) {
    echo "  send_email : " . $emailNotif->send_email . "\n";
    echo "  send_push  : " . $emailNotif->send_push . "\n";
    echo "  send_slack : " . $emailNotif->send_slack . "\n\n";
} else {
    echo "  ⚠️  ENREGISTREMENT INTROUVABLE !\n\n";
}

// -----------------------------------------------
// 2. Appliquer les corrections si ?fix=1
// -----------------------------------------------
if (isset($_GET['fix']) && $_GET['fix'] == '1') {

    echo "🔧 APPLICATION DES CORRECTIONS...\n\n";

    // Corriger les paramètres SMTP
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
        'mail_connection'  => 'sync',  // ← CRITIQUE : pas de worker nécessaire
        'verified'         => 1,
        'email_verified'   => 1,
    ]);
    echo "  ✅ Paramètres SMTP mis à jour\n";

    // S'assurer que la notification "user-assign-to-task" est activée
    if ($emailNotif) {
        DB::table('email_notification_settings')
            ->where('slug', 'user-assign-to-task')
            ->update(['send_email' => 'yes']);
        echo "  ✅ Notification 'user-assign-to-task' activée (send_email=yes)\n";
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
        echo "  ✅ Notification 'user-assign-to-task' créée\n";
    }

    // Activer aussi les autres notifications de tâches
    $taskSlugs = [
        'task-completed',
        'task-status-updated',
    ];
    DB::table('email_notification_settings')
        ->whereIn('slug', $taskSlugs)
        ->update(['send_email' => 'yes']);
    echo "  ✅ Notifications task-completed et task-status-updated activées\n\n";

    // Relire après correction
    $smtp = DB::table('smtp_settings')->first();
    echo "📋 PARAMÈTRES SMTP APRÈS CORRECTION:\n";
    echo "  mail_connection : " . ($smtp->mail_connection ?? 'N/A') . "\n";
    echo "  verified        : " . ($smtp->verified ?? 'N/A') . "\n";
    echo "  email_verified  : " . ($smtp->email_verified ?? 'N/A') . "\n\n";

} else {
    echo "\n⚡ Pour appliquer les corrections, accédez à :\n";
    echo "   https://tacheflux.dc-knowing.com/fix_smtp_server.php?token=dck2026fix&fix=1\n\n";
}

// -----------------------------------------------
// 3. Test d'envoi d'email
// -----------------------------------------------
if (isset($_GET['test_email'])) {
    $testEmail = $_GET['test_email'];
    echo "📧 TEST D'ENVOI EMAIL vers : $testEmail\n";
    try {
        // Reconfigurer mail avec les nouvelles valeurs DB
        $smtpFresh = DB::table('smtp_settings')->first();
        $pwdDecrypt = Crypt::decryptString($smtpFresh->mail_password);

        Config::set('mail.default', 'smtp');
        Config::set('mail.mailers.smtp.host', $smtpFresh->mail_host);
        Config::set('mail.mailers.smtp.port', $smtpFresh->mail_port);
        Config::set('mail.mailers.smtp.username', $smtpFresh->mail_username);
        Config::set('mail.mailers.smtp.password', $pwdDecrypt);
        Config::set('mail.mailers.smtp.encryption', $smtpFresh->mail_encryption);
        Config::set('mail.from.address', $smtpFresh->mail_from_email);
        Config::set('mail.from.name', $smtpFresh->mail_from_name);

        \Illuminate\Support\Facades\Mail::raw(
            'Test de configuration SMTP depuis FlowTask CRM - ' . now(),
            function($msg) use ($testEmail, $smtpFresh) {
                $msg->to($testEmail)
                    ->subject('Test SMTP FlowTask - ' . now())
                    ->from($smtpFresh->mail_from_email, $smtpFresh->mail_from_name);
            }
        );
        echo "  ✅ Email envoyé avec succès !\n";
    } catch (\Exception $e) {
        echo "  ❌ ERREUR : " . $e->getMessage() . "\n";
    }
}

echo "\n";
echo "⚠️  IMPORTANT : Supprimez ce fichier après utilisation !\n";
echo "   rm fix_smtp_server.php\n";
echo '</pre>';
?>
