<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Schema;

/**
 * Desactive les modules dont les routes ont ete commentees.
 *
 * Le recentrage sur les modules du cabinet a commente les routes de ces
 * modules (routes/web.php, « [MODULE RETIRE] ») sans toucher a
 * module_settings : ils restaient « active ». Le code qui teste
 * in_array('<module>', user_modules()) affichait donc encore leurs widgets,
 * onglets et notifications, qui appellent des routes inexistantes
 * (RouteNotFoundException, erreur 500).
 *
 * Garde-fou : un module n'est desactive que si aucune de ses routes temoins
 * n'existe. Si ses routes sont un jour retablies, la migration le laisse
 * actif au lieu de le couper a tort.
 *
 * Reversible : up() sauvegarde le statut de chaque ligne modifiee dans
 * sauvegarde_statut_modules ; down() le restaure puis supprime la table.
 *
 * Le cache user_modules_<id> est vide pour que l'effet soit immediat.
 */
return new class extends Migration
{

    private string $sauvegarde = 'sauvegarde_statut_modules';

    /** module_name => routes temoins (le module est garde si l'une existe). */
    private array $modulesRetires = [
        'invoices' => ['invoices.index'],
        'estimates' => ['estimates.index'],
        'events' => ['events.index'],
        'messages' => ['messages.index'],
        'notices' => ['notices.index'],
        'payments' => ['payments.index'],
        'knowledgebase' => ['knowledgebase.index'],
        'attendance' => ['attendances.index'],
        'expenses' => ['expenses.index'],
        'leaves' => ['leaves.index'],
        'holidays' => ['holidays.index'],
        'products' => ['products.index'],
        'bankaccount' => ['bankaccounts.index'],
        'sms' => ['sms-setting.index'],
        // Deja desactives sur l'entreprise d'origine, mais CompanyObserver les
        // active pour toute nouvelle entreprise.
        'tickets' => ['tickets.index'],
        'orders' => ['orders.index'],
        'leads' => ['deals.index', 'lead-contact.index'],
    ];

    public function up(): void
    {
        $modules = array_keys(array_filter(
            $this->modulesRetires,
            fn ($routes) => !collect($routes)->contains(fn ($nom) => Route::has($nom))
        ));

        if (empty($modules)) {
            return;
        }

        if (!Schema::hasTable($this->sauvegarde)) {
            Schema::create($this->sauvegarde, function (Blueprint $table) {
                $table->unsignedBigInteger('module_setting_id')->primary();
                $table->string('status');
            });
        }

        DB::transaction(function () use ($modules) {
            $lignes = DB::table('module_settings')
                ->whereIn('module_name', $modules)
                ->where('status', '!=', 'deactive')
                ->get(['id', 'status']);

            foreach ($lignes->chunk(200) as $paquet) {
                DB::table($this->sauvegarde)->insertOrIgnore(
                    $paquet->map(fn ($l) => ['module_setting_id' => $l->id, 'status' => $l->status])->all()
                );
            }

            DB::table('module_settings')
                ->whereIn('id', $lignes->pluck('id'))
                ->update(['status' => 'deactive', 'updated_at' => now()]);
        });

        $this->viderLeCacheDesModules();
    }

    public function down(): void
    {
        if (!Schema::hasTable($this->sauvegarde)) {
            return;
        }

        DB::transaction(function () {
            foreach (DB::table($this->sauvegarde)->get() as $ligne) {
                DB::table('module_settings')
                    ->where('id', $ligne->module_setting_id)
                    ->update(['status' => $ligne->status, 'updated_at' => now()]);
            }
        });

        Schema::drop($this->sauvegarde);

        $this->viderLeCacheDesModules();
    }

    private function viderLeCacheDesModules(): void
    {
        DB::table('users')->orderBy('id')->select('id')->chunk(500, function ($users) {
            foreach ($users as $user) {
                cache()->forget('user_modules_' . $user->id);
            }
        });
    }

};
