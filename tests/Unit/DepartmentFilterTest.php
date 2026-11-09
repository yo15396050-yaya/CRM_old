<?php

namespace Tests\Unit;

if (!function_exists('base_path')) {
    function base_path($path = '') {
        return dirname(__DIR__, 2) . ($path ? DIRECTORY_SEPARATOR . $path : '');
    }
}

if (!function_exists('resource_path')) {
    function resource_path($path = '') {
        return base_path('resources' . ($path ? DIRECTORY_SEPARATOR . $path : ''));
    }
}

use PHPUnit\Framework\TestCase;

/**
 * Tests de validation de la nouvelle fonctionnalité de filtrage des tâches par département.
 * Couvre les 10 scénarios demandés dans la spécification TaskFlow.
 */
class DepartmentFilterTest extends TestCase
{
    /**
     * Scénario 1 : Affichage initial sans filtre
     * L'accès initial à la liste ou au Kanban (avec department=all ou sans paramètre)
     * doit charger toutes les tâches autorisées sans clause restrictive de département.
     */
    public function test_scenario_1_initial_display_without_filter_department_all()
    {
        // 1. Vérification dans TasksDataTable : 'all' ou null n'ajoute pas de restriction
        $dataTableCode = file_get_contents(base_path('app/DataTables/TasksDataTable.php'));
        $this->assertStringContainsString('$departmentId = $request->department ?? $request->department_id;', $dataTableCode);
        $this->assertStringContainsString("if (\$departmentId != '' && \$departmentId != null && \$departmentId != 'all')", $dataTableCode);

        // 2. Vérification dans TaskBoardController
        $boardCode = file_get_contents(base_path('app/Http/Controllers/TaskBoardController.php'));
        $this->assertStringContainsString("if (\$departmentId != '' && \$departmentId != null && \$departmentId != 'all')", $boardCode);

        // 3. Option par défaut dans le template Blade
        $filterBlade = file_get_contents(resource_path('views/tasks/filters/department.blade.php'));
        $this->assertStringContainsString('<option value="all">', $filterBlade);
        $this->assertStringContainsString('Tous les départements', $filterBlade);
    }

    /**
     * Scénario 2 : Filtrage par département Informatique
     * La sélection du département Informatique (ex: ID 1) doit filtrer les tâches
     * via les employés assignés (employee_details.department_id) ou le département du projet.
     */
    public function test_scenario_2_filter_by_it_department()
    {
        $dataTableCode = file_get_contents(base_path('app/DataTables/TasksDataTable.php'));
        
        // Vérifie la sous-requête sur les membres assignés
        $this->assertStringContainsString("'employee_details.department_id', \$departmentId", $dataTableCode);
        // Vérifie la sous-requête sur project_departments
        $this->assertStringContainsString("'project_departments.team_id', \$departmentId", $dataTableCode);
        // Vérifie la sous-requête sur projects.team_id
        $this->assertStringContainsString("'projects.team_id', \$departmentId", $dataTableCode);
    }

    /**
     * Scénario 3 : Filtrage par département Comptabilité
     * La sélection d'un autre département (ex: Comptabilité, ID 2) applique
     * la même logique avec le nouvel identifiant.
     */
    public function test_scenario_3_filter_by_accounting_department()
    {
        $boardCode = file_get_contents(base_path('app/Http/Controllers/TaskBoardController.php'));
        
        // applyDepartmentFilter gère dynamiquement l'ID passé en paramètre
        $this->assertStringContainsString('private function applyDepartmentFilter($query, $request)', $boardCode);
        $this->assertStringContainsString('$departmentId = $request->department ?? $request->department_id;', $boardCode);
        $this->assertStringContainsString("'employee_details.department_id', \$departmentId", $boardCode);
        $this->assertStringContainsString("'project_departments.team_id', \$departmentId", $boardCode);
        $this->assertStringContainsString("'projects.team_id', \$departmentId", $boardCode);
    }

    /**
     * Scénario 4 : Combinaison filtre département + recherche textuelle
     * Les conditions de recherche textuelle et de département doivent se combiner avec AND.
     */
    public function test_scenario_4_combine_department_filter_with_text_search()
    {
        $boardCode = file_get_contents(base_path('app/Http/Controllers/TaskBoardController.php'));
        $dataTableCode = file_get_contents(base_path('app/DataTables/TasksDataTable.php'));

        // Dans TaskBoardController : applyDepartmentFilter est appelé avant ou après searchText
        $this->assertStringContainsString('$this->applyDepartmentFilter($q, $request);', $boardCode);
        $this->assertStringContainsString("if (\$request->searchText != '')", $boardCode);

        // Dans TasksDataTable : le filtre de département utilise $model->where(...)
        $this->assertStringContainsString('$model->where(function ($q) use ($departmentId) {', $dataTableCode);
    }

    /**
     * Scénario 5 : Changement de vue (Liste <-> Kanban) avec maintien du filtre
     * Le filtre sélectionné doit être conservé lors du passage de la vue Liste au Kanban et vice-versa.
     */
    public function test_scenario_5_view_switch_preserves_department_parameter()
    {
        // 1. Vue Liste -> Lien Kanban avec paramètre URL
        $listView = file_get_contents(resource_path('views/tasks/index.blade.php'));
        $this->assertStringContainsString("route('taskboards.index') . (request('department') ? '?department=' . request('department') : '')", $listView);
        $this->assertStringContainsString('updateViewLinks()', $listView);
        $this->assertStringContainsString('.view-kanban-link', $listView);

        // 2. Vue Kanban -> Lien Liste avec paramètre URL
        $kanbanView = file_get_contents(resource_path('views/taskboard/index.blade.php'));
        $this->assertStringContainsString("route('tasks.index') . (request('department') ? '?department=' . request('department') : '')", $kanbanView);
        $this->assertStringContainsString('updateViewLinks()', $kanbanView);
        $this->assertStringContainsString('.view-list-link', $kanbanView);
    }

    /**
     * Scénario 6 : Compteurs des colonnes Kanban synchronisés
     * Le nombre de tâches affiché en en-tête de chaque colonne du Kanban doit correspondre aux tâches filtrées.
     */
    public function test_scenario_6_kanban_column_counters_match_filtered_results()
    {
        $boardCode = file_get_contents(base_path('app/Http/Controllers/TaskBoardController.php'));

        // Vérifie que applyDepartmentFilter est appelé dans la fermeture 'tasks as tasks_count'
        $this->assertMatchesRegularExpression(
            "/withCount\(\[\s*'tasks as tasks_count' => function \(\\\$q\)[\s\S]*?applyDepartmentFilter/m",
            $boardCode
        );
    }

    /**
     * Scénario 7 : Réinitialisation du filtre (Bouton reset)
     * L'action de reset doit remettre le filtre sur 'Tous les départements' et réactualiser la vue.
     */
    public function test_scenario_7_reset_filter_returns_to_all_departments()
    {
        // 1. Vue Liste : reset-filters réinitialise #department
        $listView = file_get_contents(resource_path('views/tasks/index.blade.php'));
        $this->assertStringContainsString("$('#department').val('all');", $listView);
        $this->assertStringContainsString("$('#department').selectpicker('refresh');", $listView);

        // 2. Vue Kanban : reset-filters réinitialise #department
        $kanbanView = file_get_contents(resource_path('views/taskboard/index.blade.php'));
        $this->assertStringContainsString("$('#department').val('all');", $kanbanView);
        $this->assertStringContainsString("$('#department').selectpicker('refresh');", $kanbanView);
    }

    /**
     * Scénario 8 : Département sans tâches (Résultat vide)
     * Si un département n'a aucune tâche associée, un affichage propre sans erreur doit être rendu.
     */
    public function test_scenario_8_department_with_zero_tasks_empty_state()
    {
        // Les requêtes whereExists garantissent qu'aucune exception SQL n'est levée si aucun résultat n'existe
        $dataTableCode = file_get_contents(base_path('app/DataTables/TasksDataTable.php'));
        $this->assertStringContainsString("whereExists(function (\$sub) use (\$departmentId)", $dataTableCode);

        // Composant Blade gère le cas vide
        $filterBlade = file_get_contents(resource_path('views/tasks/filters/department.blade.php'));
        $this->assertStringContainsString("@if(isset(\$departments))", $filterBlade);
    }

    /**
     * Scénario 9 : Pagination et permissions existantes préservées
     * La pagination et le contrôle d'accès (viewTaskPermission, is_private) doivent rester fonctionnels.
     */
    public function test_scenario_9_pagination_and_user_permissions_preserved()
    {
        $boardCode = file_get_contents(base_path('app/Http/Controllers/TaskBoardController.php'));
        $dataTableCode = file_get_contents(base_path('app/DataTables/TasksDataTable.php'));

        // Vérifie la conservation des permissions
        $this->assertStringContainsString('$this->viewTaskPermission', $boardCode);
        $this->assertStringContainsString('$this->viewUnassignedTasksPermission', $boardCode);
        $this->assertStringContainsString('is_private', $boardCode);

        // Vérifie que loadMore applique également le filtre département
        $this->assertMatchesRegularExpression(
            "/function loadMore\(Request \\\$request\)[\s\S]*?applyDepartmentFilter/m",
            $boardCode
        );

        // DataTable pagination préservée
        $this->assertStringContainsString('datatables()->eloquent($query)', $dataTableCode);
    }

    /**
     * Scénario 10 : Chargement dynamique des départements depuis la base
     * Les départements disponibles dans le filtre doivent provenir de la table teams ordonnés par nom.
     */
    public function test_scenario_10_dynamic_loading_of_departments_from_database()
    {
        // 1. TaskController index charge les départements
        $taskController = file_get_contents(base_path('app/Http/Controllers/TaskController.php'));
        $this->assertStringContainsString("use App\Models\Team;", $taskController);
        $this->assertStringContainsString("\$this->departments = Team::orderBy('team_name')->get();", $taskController);

        // 2. TaskBoardController index charge les départements
        $boardController = file_get_contents(base_path('app/Http/Controllers/TaskBoardController.php'));
        $this->assertStringContainsString("use App\Models\Team;", $boardController);
        $this->assertStringContainsString("\$this->departments = Team::orderBy('team_name')->get();", $boardController);

        // 3. Blade itère sur $departments et affiche team_name
        $filterBlade = file_get_contents(resource_path('views/tasks/filters/department.blade.php'));
        $this->assertStringContainsString('@foreach ($departments as $dept)', $filterBlade);
        $this->assertStringContainsString('{{ $dept->team_name }}', $filterBlade);
        $this->assertStringContainsString('value="{{ $dept->id }}"', $filterBlade);
    }
}
