<?php

namespace Database\Seeders;

use App\Models\ClientDetails;
use App\Models\Company;
use App\Models\EmployeeDetails;
use App\Models\Project;
use App\Models\Role;
use App\Models\Task;
use App\Models\TaskboardColumn;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use RuntimeException;

/**
 * Jeu de donnees de recette : une seconde entreprise et des comptes de test.
 *
 * Usage (jamais en production) :
 *   php artisan db:seed --class=RecetteSeeder
 *
 * Cree, si absents (idempotent, cle = e-mail) :
 *   - l'entreprise B « Entreprise Recette B » (pour les tests d'isolation) ;
 *   - un compte par role metier dans l'entreprise A (la premiere entreprise) ;
 *   - un admin, un employe et un client dans l'entreprise B ;
 *   - dans chaque entreprise : un projet, une tache publique et une tache
 *     privee assignees a l'employe de test, et une tache non assignee.
 *
 * Tous les comptes utilisent le domaine @recette.test (non routable) et le
 * mot de passe RECETTE_PASSWORD (.env), ou « Recette#2026! » par defaut.
 * Les envois (mails, notifications, HTTP sortant) sont neutralises pendant
 * l'execution : la base de recette est une copie de la production et contient
 * les vraies adresses des collaborateurs et des clients.
 */
class RecetteSeeder extends Seeder
{

    public const DOMAINE = 'recette.test';

    private string $motDePasse;

    public function run(): void
    {
        if (app()->environment('production')) {
            throw new RuntimeException('RecetteSeeder ne doit jamais etre execute en production.');
        }

        Mail::fake();
        Notification::fake();
        Http::fake();

        $this->motDePasse = bcrypt(env('RECETTE_PASSWORD', 'Recette#2026!'));

        $entrepriseA = Company::orderBy('id')->firstOrFail();
        $entrepriseB = $this->entrepriseB();

        $comptesA = [
            'admin' => $this->compte($entrepriseA, 'admin', 'Recette Admin A', 'admin'),
            'dg' => $this->compte($entrepriseA, 'dg', 'Recette DG A', 'dg-dck'),
            'employe' => $this->compte($entrepriseA, 'employe', 'Recette Employe A', 'employee'),
            'stagiaire' => $this->compte($entrepriseA, 'stagiaire', 'Recette Stagiaire A', 'employee-stagiaires'),
            'apporteur' => $this->compte($entrepriseA, 'apporteur', 'Recette Apporteur A', 'apporteurs'),
            'client' => $this->compte($entrepriseA, 'client', 'Recette Client A', 'client'),
        ];

        $comptesB = [
            'admin' => $this->compte($entrepriseB, 'b.admin', 'Recette Admin B', 'admin'),
            'employe' => $this->compte($entrepriseB, 'b.employe', 'Recette Employe B', 'employee'),
            'client' => $this->compte($entrepriseB, 'b.client', 'Recette Client B', 'client'),
        ];

        $this->donnees($entrepriseA, $comptesA, 'A');
        $this->donnees($entrepriseB, $comptesB, 'B');

        $this->command?->info('Recette prete. Comptes : recette.<role>@' . self::DOMAINE . ' (A) et recette.b.<role>@' . self::DOMAINE . ' (B).');
    }

    private function entrepriseB(): Company
    {
        $existante = Company::where('company_email', 'entreprise-b@' . self::DOMAINE)->first();

        if ($existante) {
            return $existante;
        }

        $modele = Company::orderBy('id')->firstOrFail();

        $entreprise = new Company();
        $entreprise->forceFill([
            'company_name' => 'Entreprise Recette B',
            'app_name' => 'Entreprise Recette B',
            'company_email' => 'entreprise-b@' . self::DOMAINE,
            'company_phone' => '0000000000',
            'address' => 'Adresse de recette',
            'timezone' => $modele->timezone,
            'locale' => $modele->locale,
            'currency_id' => null,
            'allow_client_signup' => 0,
            'admin_client_signup_approval' => 0,
            'before_days' => 0,
            'after_days' => 0,
        ]);
        $entreprise->save();

        return $entreprise->fresh();
    }

    /** Cree (ou retrouve) un compte et lui donne les roles et permissions de $nomRole. */
    private function compte(Company $entreprise, string $cle, string $nom, string $nomRole): User
    {
        $email = 'recette.' . $cle . '@' . self::DOMAINE;

        $user = User::withoutGlobalScopes()->where('email', $email)->first();

        if ($user) {
            return $user;
        }

        $role = Role::withoutGlobalScopes()->where('company_id', $entreprise->id)->where('name', $nomRole)->first();

        if (!$role) {
            throw new RuntimeException("Role {$nomRole} absent de l'entreprise {$entreprise->id}.");
        }

        return DB::transaction(function () use ($entreprise, $email, $nom, $role) {
            $user = new User();
            $user->forceFill([
                'company_id' => $entreprise->id,
                'name' => $nom,
                'email' => $email,
                'password' => $this->motDePasse,
                'status' => 'active',
                'login' => 'enable',
                'dark_theme' => 0,
                'rtl' => 0,
                'permission_sync' => 1,
            ]);
            $user->saveQuietly();

            if ($role->name === 'client') {
                ClientDetails::withoutGlobalScopes()->create([
                    'company_id' => $entreprise->id,
                    'user_id' => $user->id,
                    'company_name' => $nom,
                ]);
            }
            else {
                $employeeDetails = new EmployeeDetails();
                $employeeDetails->forceFill([
                    'company_id' => $entreprise->id,
                    'user_id' => $user->id,
                    'employee_id' => 'RECETTE-' . $user->id,
                    'joining_date' => now(),
                ]);
                $employeeDetails->saveQuietly();

                // Comme EmployeeController : tout collaborateur porte le role employee.
                $roleEmployee = Role::withoutGlobalScopes()->where('company_id', $entreprise->id)->where('name', 'employee')->first();
                $user->roles()->attach($roleEmployee->id);
            }

            if ($role->name !== 'employee') {
                $user->roles()->attach($role->id);
            }

            $user->assignUserRolePermission($role->id);

            return $user;
        });
    }

    /** Un projet, une tache publique, une tache privee et une tache non assignee. */
    private function donnees(Company $entreprise, array $comptes, string $lettre): void
    {
        $nomProjet = "Projet recette {$lettre}";

        if (Project::withoutGlobalScopes()->where('company_id', $entreprise->id)->where('project_name', $nomProjet)->exists()) {
            return;
        }

        $colonne = TaskboardColumn::withoutGlobalScopes()->where('company_id', $entreprise->id)->where('slug', 'incomplete')->value('id');

        DB::transaction(function () use ($entreprise, $comptes, $lettre, $nomProjet, $colonne) {
            $projet = new Project();
            $projet->forceFill([
                'company_id' => $entreprise->id,
                'project_name' => $nomProjet,
                'start_date' => now()->subDays(5)->toDateString(),
                'deadline' => now()->addDays(30)->toDateString(),
                'client_id' => $comptes['client']->id,
                'completion_percent' => 0,
                'public' => 0,
                'added_by' => $comptes['admin']->id,
            ]);
            $projet->saveQuietly();

            DB::table('project_members')->insert([
                'project_id' => $projet->id,
                'user_id' => $comptes['employe']->id,
                'hourly_rate' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $taches = [
                ["Tache publique recette {$lettre}", 0, $comptes['employe']->id],
                ["Tache privee recette {$lettre}", 1, $comptes['employe']->id],
                ["Tache non assignee recette {$lettre}", 0, null],
            ];

            foreach ($taches as [$titre, $privee, $assigne]) {
                $tache = new Task();
                $tache->forceFill([
                    'company_id' => $entreprise->id,
                    // saveQuietly() court-circuite TaskObserver, qui genere ce hash
                    // (lien public signe de la page de detail).
                    'hash' => md5(uniqid('recette', true)),
                    'heading' => $titre,
                    'project_id' => $projet->id,
                    'board_column_id' => $colonne,
                    'start_date' => now()->toDateString(),
                    'due_date' => now()->addDays(7)->toDateString(),
                    'is_private' => $privee,
                    'column_priority' => 0,
                    'estimate_hours' => 0,
                    'estimate_minutes' => 0,
                    'added_by' => $comptes['admin']->id,
                    'created_by' => $comptes['admin']->id,
                ]);
                $tache->saveQuietly();

                if ($assigne) {
                    DB::table('task_users')->insert([
                        'task_id' => $tache->id,
                        'user_id' => $assigne,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }
        });
    }

}
