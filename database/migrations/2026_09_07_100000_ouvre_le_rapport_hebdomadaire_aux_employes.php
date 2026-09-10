<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Ouvre le rapport des taches aux employes, restreint a leurs taches.
 *
 * La permission view_task_report ne declarait que « all » et « none » :
 * un employe ne pouvait donc que tout voir ou rien voir. Le niveau
 * « owned » existe deja dans permission_types (id 2) et est utilise
 * ailleurs dans l'application ; on se contente de l'autoriser ici.
 *
 * Aucune table n'est creee. Seules deux lignes de configuration et les
 * attributions individuelles changent.
 *
 * Rappel utile a la relecture : User::permission() lit user_permissions,
 * pas permission_role. Mettre a jour le role seul n'aurait aucun effet
 * pour les comptes existants — les deux tables sont donc traitees.
 */
return new class extends Migration
{

    /** Roles du cabinet qui doivent voir leur propre rapport. */
    private array $roles = [
        'employee', 'employee-stagiaires', 'stagiaires-it', 'empyohou',
    ];

    public function up(): void
    {
        $permission = DB::table('permissions')->where('name', 'view_task_report')->first();

        if (!$permission) {
            return;
        }

        /* 1. Autoriser le niveau « owned » sur cette permission. */
        DB::table('permissions')
            ->where('id', $permission->id)
            ->update(['allowed_permissions' => '{"all":4, "owned":2, "none":5}']);

        $owned = DB::table('permission_types')->where('name', 'owned')->value('id');

        if (!$owned) {
            return;
        }

        foreach ($this->roles as $nom) {
            $roles = DB::table('roles')->where('name', $nom)->get();

            foreach ($roles as $role) {
                /* 2. Le role. */
                DB::table('permission_role')
                    ->updateOrInsert(
                        ['role_id' => $role->id, 'permission_id' => $permission->id],
                        ['permission_type_id' => $owned]
                    );

                /* 3. Les comptes qui portent ce role. Seuls ceux qui
                      n'ont aucun droit sont eleves : un compte deja en
                      « all » garde sa vue complete. */
                $comptes = DB::table('role_user')
                    ->where('role_id', $role->id)
                    ->pluck('user_id');

                foreach ($comptes as $userId) {
                    $actuel = DB::table('user_permissions')
                        ->where('user_id', $userId)
                        ->where('permission_id', $permission->id)
                        ->value('permission_type_id');

                    if ($actuel === 4) {   // deja « all »
                        continue;
                    }

                    DB::table('user_permissions')->updateOrInsert(
                        ['user_id' => $userId, 'permission_id' => $permission->id],
                        ['permission_type_id' => $owned]
                    );
                }
            }
        }
    }

    public function down(): void
    {
        $permission = DB::table('permissions')->where('name', 'view_task_report')->first();

        if (!$permission) {
            return;
        }

        $none = DB::table('permission_types')->where('name', 'none')->value('id');
        $owned = DB::table('permission_types')->where('name', 'owned')->value('id');

        /* On ne remet a « none » que ce que cette migration avait
           eleve : les comptes actuellement en « owned ». */
        if ($none && $owned) {
            $roleIds = DB::table('roles')->whereIn('name', $this->roles)->pluck('id');

            $userIds = DB::table('role_user')->whereIn('role_id', $roleIds)->pluck('user_id');

            DB::table('user_permissions')
                ->whereIn('user_id', $userIds)
                ->where('permission_id', $permission->id)
                ->where('permission_type_id', $owned)
                ->update(['permission_type_id' => $none]);

            DB::table('permission_role')
                ->whereIn('role_id', $roleIds)
                ->where('permission_id', $permission->id)
                ->where('permission_type_id', $owned)
                ->update(['permission_type_id' => $none]);
        }

        DB::table('permissions')
            ->where('id', $permission->id)
            ->update(['allowed_permissions' => '{"all":4, "none":5}']);
    }

};
