<?php
/**
 * fix_session.php - Correction driver de session et nettoyage
 * Placer dans public/ → https://tacheflux.dc-knowing.com/fix_session.php?token=dck2026login
 * SUPPRIMER APRÈS UTILISATION
 */
define('SECRET_TOKEN', 'dck2026login');
if (!isset($_GET['token']) || $_GET['token'] !== SECRET_TOKEN) die('Accès refusé.');

require __DIR__ . '/../vendor/autoload.php';
$app = require_once __DIR__ . '/../bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

use Illuminate\Support\Facades\DB;

echo '<pre style="font-family:monospace;padding:20px;background:#1e1e1e;color:#d4d4d4;font-size:13px;">';
echo "=== CORRECTION SESSION - FlowTask CRM ===\n\n";

// 1. Changer le driver de session de 'database' à 'file'
$current = DB::table('global_settings')->value('session_driver');
echo "Session driver actuel : $current\n";

DB::table('global_settings')->update(['session_driver' => 'file']);
echo "✅ Session driver changé → 'file' (plus stable)\n\n";

// 2. Nettoyer les anciennes sessions en base
$count = DB::table('sessions')->count();
echo "Sessions en base : $count enregistrements\n";

$deleted = DB::table('sessions')->where('last_activity', '<', time() - 86400)->delete();
echo "✅ $deleted anciennes sessions supprimées (> 24h)\n\n";

// 3. Vider le cache
try {
    \Illuminate\Support\Facades\Artisan::call('cache:clear');
    echo "✅ Cache vidé\n";
    \Illuminate\Support\Facades\Artisan::call('config:clear');
    echo "✅ Config cache vidée\n";
} catch (\Exception $e) {
    echo "⚠️  " . $e->getMessage() . "\n";
}

echo "\n🎉 CORRECTIONS APPLIQUÉES !\n";
echo "   Essayez de vous connecter sur : https://tacheflux.dc-knowing.com/login\n\n";
echo "⚠️  Supprimez ce fichier après utilisation !\n";
echo '</pre>';
?>
