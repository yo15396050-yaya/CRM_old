<?php
/**
 * fix_login_diag.php - Diagnostic complet du problème de connexion
 * Placer dans public/ et accéder via :
 * https://tacheflux.dc-knowing.com/fix_login_diag.php?token=dck2026login
 * SUPPRIMER APRÈS UTILISATION
 */
define('SECRET_TOKEN', 'dck2026login');
if (!isset($_GET['token']) || $_GET['token'] !== SECRET_TOKEN) {
    die('Accès refusé.');
}

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Cache;

$email = $_GET['email'] ?? 'williamskouassi525@gmail.com';

echo '<pre style="font-family:monospace;padding:20px;background:#1e1e1e;color:#d4d4d4;font-size:13px;">';
echo "=== DIAGNOSTIC CONNEXION - FlowTask CRM ===\n\n";

// -----------------------------------------------
// 1. VÉRIFIER L'UTILISATEUR EN BASE
// -----------------------------------------------
echo "👤 UTILISATEUR : $email\n";
$user = DB::table('users')->where('email', $email)->first();

if (!$user) {
    echo "  ❌ UTILISATEUR INTROUVABLE EN BASE DE DONNÉES !\n";
    echo "     Vérifiez l'adresse email.\n\n";
} else {
    echo "  ✅ Trouvé → ID: {$user->id}, Nom: {$user->name}\n";
    echo "  Status    : " . ($user->status ?? 'N/A') . " (doit être 'active')\n";
    echo "  Login     : " . ($user->login ?? 'N/A') . " (doit être 'enable')\n";
    echo "  Company ID: " . ($user->company_id ?? 'N/A') . "\n";

    // Tester le mot de passe
    $testPassword = $_GET['pwd'] ?? '12345678';
    $passwordOk = Hash::check($testPassword, $user->password);
    echo "  Mot de passe '$testPassword' : " . ($passwordOk ? "✅ CORRECT" : "❌ INCORRECT") . "\n\n";

    // Vérifier la company
    if ($user->company_id) {
        $company = DB::table('companies')->where('id', $user->company_id)->first();
        echo "🏢 COMPANY :\n";
        echo "  " . ($company ? "✅ Trouvée → " . $company->company_name : "❌ INTROUVABLE !") . "\n";

        if ($company) {
            $attendance = DB::table('attendance_settings')->where('company_id', $user->company_id)->first();
            echo "  AttendanceSetting : " . ($attendance ? "✅ Trouvée → auto_clock_in=" . $attendance->auto_clock_in : "⚠️  INTROUVABLE (peut causer des erreurs de login)") . "\n";
        }
    }
    echo "\n";
}

// -----------------------------------------------
// 2. VÉRIFIER LA SESSION
// -----------------------------------------------
echo "🔐 CONFIGURATION SESSION :\n";
$globalSetting = DB::table('global_settings')->first();
$sessionDriver = $globalSetting->session_driver ?? 'file';
echo "  Driver en DB   : $sessionDriver\n";
echo "  Driver effectif: " . config('session.driver') . "\n";

if ($sessionDriver === 'file' || config('session.driver') === 'file') {
    $sessionPath = storage_path('framework/sessions');
    $writable = is_writable($sessionPath);
    echo "  Dossier sessions : $sessionPath\n";
    echo "  Accessible en écriture : " . ($writable ? "✅ OUI" : "❌ NON - PROBLÈME !") . "\n";

    if (!$writable) {
        echo "\n  🔧 CORRECTION PERMISSIONS...\n";
        @chmod($sessionPath, 0775);
        echo "  " . (is_writable($sessionPath) ? "✅ Permissions corrigées" : "❌ Impossible de corriger (contactez l'hébergeur)") . "\n";
    }
}

if ($sessionDriver === 'database' || config('session.driver') === 'database') {
    $tables = DB::select("SHOW TABLES LIKE 'sessions'");
    echo "  Table sessions en DB : " . (count($tables) > 0 ? "✅ Existe" : "❌ MANQUANTE !") . "\n";
}
echo "\n";

// -----------------------------------------------
// 3. VÉRIFIER LE RATE LIMITER
// -----------------------------------------------
echo "⏱️  RATE LIMITER (anti-brute force) :\n";
$rateLimitKey = 'login:' . sha1($email . '|127.0.0.1');
$rateLimitKeyAlt = 'login_' . sha1($email);
echo "  Clé cache : $rateLimitKey\n";

// Vider le rate limiter pour cet email
if (isset($_GET['clear_limit'])) {
    Cache::forget($rateLimitKey);
    Cache::forget('login|' . $email);
    // Vider toutes les clés de rate limit
    $keysToForget = [
        'login:' . sha1($email . '|' . request()->ip()),
        sha1($email . '|' . request()->ip()),
    ];
    foreach ($keysToForget as $key) {
        Cache::forget($key);
    }
    echo "  ✅ Rate limiter vidé pour $email\n";
}

echo "  Vider le rate limiter → ajoutez &clear_limit=1 à l'URL\n\n";

// -----------------------------------------------
// 4. VÉRIFIER LE CACHE DE L'APPLICATION
// -----------------------------------------------
echo "💾 CACHE APPLICATION :\n";
$cacheDriver = config('cache.default');
echo "  Driver cache : $cacheDriver\n";

if ($cacheDriver === 'file') {
    $cachePath = storage_path('framework/cache');
    echo "  Dossier cache accessible : " . (is_writable($cachePath) ? "✅ OUI" : "❌ NON") . "\n";
}
echo "\n";

// -----------------------------------------------
// 5. VÉRIFIER APP_KEY ET ENV
// -----------------------------------------------
echo "🔑 CONFIGURATION APP :\n";
echo "  APP_KEY défini   : " . (config('app.key') ? "✅ OUI" : "❌ NON - CRITIQUE !") . "\n";
echo "  APP_ENV          : " . config('app.env') . "\n";
echo "  APP_URL          : " . config('app.url') . "\n";
echo "  SESSION_DOMAIN   : " . (config('session.domain') ?? 'null (auto)') . "\n";
echo "  SESSION_SECURE   : " . (config('session.secure') ? 'true' : 'false/null') . "\n\n";

// -----------------------------------------------
// 6. ACTIONS CORRECTIVES
// -----------------------------------------------
if (isset($_GET['fix_login'])) {
    echo "🔧 APPLICATION DES CORRECTIONS...\n\n";

    // Vider le rate limiter
    try {
        \Illuminate\Support\Facades\Artisan::call('cache:clear');
        echo "  ✅ Cache vidé\n";
    } catch (\Exception $e) {
        echo "  ⚠️  Cache: " . $e->getMessage() . "\n";
    }

    // Corriger les permissions du dossier sessions
    $sessionPath = storage_path('framework/sessions');
    @chmod($sessionPath, 0775);
    $files = glob($sessionPath . '/*');
    if ($files) {
        foreach ($files as $file) {
            @chmod($file, 0664);
        }
    }
    echo "  ✅ Permissions sessions corrigées\n";

    // S'assurer que l'utilisateur peut se connecter
    if ($user) {
        DB::table('users')->where('email', $email)->update([
            'status' => 'active',
            'login'  => 'enable',
        ]);
        echo "  ✅ Utilisateur $email → status=active, login=enable\n";
    }

    echo "\n  🎉 Essayez de vous connecter maintenant !\n";
}

echo "\n";
echo "👉 Pour tout corriger : ajoutez &fix_login=1&clear_limit=1 à l'URL\n";
echo "👉 URL complète : https://tacheflux.dc-knowing.com/fix_login_diag.php?token=dck2026login&fix_login=1&clear_limit=1\n\n";
echo "⚠️  SUPPRIMEZ CE FICHIER APRÈS UTILISATION !\n";
echo '</pre>';
?>
