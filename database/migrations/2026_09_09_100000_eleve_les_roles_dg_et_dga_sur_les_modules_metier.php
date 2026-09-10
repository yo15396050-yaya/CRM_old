<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Ouvre l'ensemble des modules metier aux roles dg-dck et dga-dck.
 *
 * Le DG et le DGA pilotent le cabinet mais ne voyaient qu'une partie des
 * dossiers : 199 et 192 droits complets sur 356. Les manques portaient sur
 * des ecrans qu'ils utilisent au quotidien (modeles et types de contrat,
 * modeles de projet, quotas de conges, tableau de bord des tickets, rapport
 * des depenses), et sur les tickets et les conges ils etaient limites a
 * « added » ou « both », donc a leurs propres enregistrements.
 *
 * Les modules d'administration technique restent fermes. En particulier
 * « settings » contient manage_role_permission_setting : l'ouvrir aurait
 * permis a ces deux comptes de s'attribuer ensuite n'importe quel droit, y
 * compris de modifier ceux de l'administrateur. La separation entre
 * direction et administration technique est donc conservee.
 *
 * Rappel utile a la relecture : User::permission() lit user_permissions,
 * pas permission_role. Mettre a jour le role seul n'aurait aucun effet pour
 * les comptes existants — les deux tables sont donc traitees.
 *
 * La migration est reversible. Comme les niveaux d'origine variaient d'une
 * permission a l'autre, down() ne peut pas les deduire : up() les recopie
 * dans une table de sauvegarde, que down() rejoue puis supprime.
 */
return new class extends Migration
{

    /** Roles de direction a elever. */
    private array $roles = ['dg-dck', 'dga-dck'];

    /**
     * Modules laisses en l'etat.
     *
     * « settings » par prudence (voir l'entete). Les cinq autres ne sont pas
     * utilises au cabinet : les ouvrir n'aurait ajoute que du bruit dans les
     * menus.
     */
    private array $modulesExclus = ['settings', 'recruit', 'leads', 'estimates', 'orders', 'sms'];

    /** Table de sauvegarde, creee puis supprimee par cette seule migration. */
    private string $sauvegarde = 'sauvegarde_droits_direction';

    public function up(): void
    {
        $roleIds = DB::table('roles')->whereIn('name', $this->roles)->pluck('id');

        if ($roleIds->isEmpty()) {
            return;
        }

        /* Le niveau « all » porte l'id 4 dans permission_types. On le lit
           plutot que de l'ecrire en dur : l'id est stable sur cette base,
           mais la lecture coute une requete et evite une surprise. */
        $all = DB::table('permission_types')->where('name', 'all')->value('id');

        if (!$all) {
            return;
        }

        $moduleIds = DB::table('modules')->whereNotIn('module_name', $this->modulesExclus)->pluck('id');
        $permissionIds = DB::table('permissions')->whereIn('module_id', $moduleIds)->pluck('id');

        if ($permissionIds->isEmpty()) {
            return;
        }

        $userIds = DB::table('role_user')->whereIn('role_id', $roleIds)->pluck('user_id')->unique();

        $this->creerLaSauvegarde();
        $this->sauvegarder($roleIds, $userIds, $permissionIds);

        /* 1. Le gabarit de role, dont heriteront les futurs comptes. */
        foreach ($roleIds as $roleId) {
            foreach ($permissionIds as $permissionId) {
                DB::table('permission_role')->updateOrInsert(
                    ['role_id' => $roleId, 'permission_id' => $permissionId],
                    ['permission_type_id' => $all]
                );
            }
        }

        /* 2. Les comptes existants qui portent ces roles. */
        foreach ($userIds as $userId) {
            foreach ($permissionIds as $permissionId) {
                DB::table('user_permissions')->updateOrInsert(
                    ['user_id' => $userId, 'permission_id' => $permissionId],
                    ['permission_type_id' => $all]
                );
            }
        }

        $this->viderLesCaches($userIds);
    }

    public function down(): void
    {
        if (!Schema::hasTable($this->sauvegarde)) {
            return;
        }

        /* Les lignes qui n'existaient pas avant sont supprimees ; les autres
           retrouvent leur niveau d'origine. */
        foreach (DB::table($this->sauvegarde)->where('table_source', 'permission_role')->get() as $ligne) {
            $cle = ['role_id' => $ligne->proprietaire_id, 'permission_id' => $ligne->permission_id];

            is_null($ligne->permission_type_id)
                ? DB::table('permission_role')->where($cle)->delete()
                : DB::table('permission_role')->where($cle)->update(['permission_type_id' => $ligne->permission_type_id]);
        }

        foreach (DB::table($this->sauvegarde)->where('table_source', 'user_permissions')->get() as $ligne) {
            $cle = ['user_id' => $ligne->proprietaire_id, 'permission_id' => $ligne->permission_id];

            is_null($ligne->permission_type_id)
                ? DB::table('user_permissions')->where($cle)->delete()
                : DB::table('user_permissions')->where($cle)->update(['permission_type_id' => $ligne->permission_type_id]);
        }

        $this->viderLesCaches(
            DB::table($this->sauvegarde)->where('table_source', 'user_permissions')->pluck('proprietaire_id')->unique()
        );

        Schema::dropIfExists($this->sauvegarde);
    }

    /**
     * Etat anterieur, une ligne par droit touche.
     *
     * permission_type_id nul signifie « la ligne n'existait pas » : down()
     * la supprimera au lieu de la remettre a un niveau invente.
     */
    private function creerLaSauvegarde(): void
    {
        Schema::dropIfExists($this->sauvegarde);

        Schema::create($this->sauvegarde, function (Blueprint $table) {
            $table->id();
            $table->string('table_source', 32);
            $table->unsignedBigInteger('proprietaire_id');
            $table->unsignedBigInteger('permission_id');
            $table->unsignedBigInteger('permission_type_id')->nullable();
            $table->index(['table_source', 'proprietaire_id']);
        });
    }

    private function sauvegarder($roleIds, $userIds, $permissionIds): void
    {
        $lignes = [];

        $cibles = [
            ['permission_role', 'role_id', $roleIds],
            ['user_permissions', 'user_id', $userIds],
        ];

        foreach ($cibles as [$table, $colonne, $ids]) {
            foreach ($ids as $id) {
                $actuels = DB::table($table)
                    ->where($colonne, $id)
                    ->whereIn('permission_id', $permissionIds)
                    ->pluck('permission_type_id', 'permission_id');

                foreach ($permissionIds as $permissionId) {
                    $lignes[] = [
                        'table_source' => $table,
                        'proprietaire_id' => $id,
                        'permission_id' => $permissionId,
                        'permission_type_id' => $actuels[$permissionId] ?? null,
                    ];
                }
            }
        }

        foreach (array_chunk($lignes, 500) as $paquet) {
            DB::table($this->sauvegarde)->insert($paquet);
        }
    }

    /**
     * Les droits sont lus depuis le cache applicatif a deux endroits :
     * la liste des modules du menu, et permissionTypeId(). Sans purge, les
     * comptes concernes garderaient leurs anciens droits jusqu'a expiration.
     */
    private function viderLesCaches($userIds): void
    {
        $noms = DB::table('permissions')->pluck('name');

        foreach ($userIds as $userId) {
            cache()->forget('user_modules_' . $userId);

            foreach ($noms as $nom) {
                cache()->forget('permission-id-' . $nom . '-' . $userId);
            }
        }
    }

};
