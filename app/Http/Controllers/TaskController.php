<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Task;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Helper\Files;
use App\Helper\Reply;
use App\Models\Pinned;
use App\Models\Project;
use App\Models\SubTask;
use App\Models\TaskFile;
use App\Models\TaskUser;
use App\Http\Requests\Tasks\ActionTask;
use App\Models\TaskComment;

use App\Models\BaseModel;

use App\Models\TaskLabel;
use App\Models\SubTaskFile;
use App\Models\TaskSetting;
use App\Models\TaskCategory;
use Illuminate\Http\Request;
use App\Models\TaskLabelList;
use App\Models\ProjectTimeLog;
use App\Models\TaskboardColumn;
use App\Traits\ProjectProgress;
use App\Traits\ApprobationTache;
use App\Models\ProjectMilestone;
use App\Events\TaskReminderEvent;
use App\DataTables\TasksDataTable;
use App\Imports\TaskImport;
use Maatwebsite\Excel\Facades\Excel;
use App\Traits\ImportationTaches;
use App\DataTables\WaitingForApprovalDataTable;
use Illuminate\Support\Facades\DB;
use App\Models\ProjectTimeLogBreak;
use App\Http\Requests\Tasks\StoreTask;
use Illuminate\Support\Facades\Config;
use App\Http\Requests\Tasks\UpdateTask;
use App\Events\TaskEvent;

class TaskController extends AccountBaseController
{


    use ImportationTaches;
    use ProjectProgress;
    use ApprobationTache;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.tasks';
        $this->middleware(
            function ($request, $next) {
            abort_403(!in_array('tasks', $this->user->modules));

            return $next($request);
        }
        );
    }

    public function index(TasksDataTable $dataTable)
    {
        if ((request()->wantsJson() || request()->is('api/*')) && !request()->ajax()) {
            $user = auth()->user();
            $tasks = Task::with(['project', 'users'])
                ->whereHas('users', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->orWhere('created_by', $user->id)
                ->orderBy('due_date', 'asc')
                ->get()
                ->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'name' => $task->heading,
                        'status' => $task->status,
                        'project_name' => $task->project ? $task->project->project_name : 'Sans projet',
                        'description' => $task->description,
                        'due_date' => $task->due_date ? $task->due_date->format('d/m/Y') : 'Pas de date',
                    ];
                });
            return response()->json($tasks);
        }

        $viewPermission = user()->permission('view_tasks');

        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        if (!request()->ajax()) {
            $this->assignedTo = request()->assignedTo;

            if (request()->has('assignee') && request()->assignee == 'me') {
                $this->assignedTo = user()->id;
            }

            $this->projects = Project::allProjects();

            if (in_array('client', user_roles())) {
                $this->clients = User::client();
            }
            else {
                $this->clients = User::allClients();
            }

            $this->employees = User::allEmployees(null, true, ($viewPermission == 'all' ? 'all' : null));
            $this->taskBoardStatus = TaskboardColumn::all();
            $this->taskCategories = TaskCategory::all();
            $this->taskLabels = TaskLabelList::all();
            $this->milestones = ProjectMilestone::all();

            $taskBoardColumn = TaskboardColumn::waitingForApprovalColumn();

            $projectIds = Project::where('project_admin', user()->id)->pluck('id');

            if (!in_array('admin', user_roles()) && (in_array('employee', user_roles()) && $projectIds->isEmpty())) {
                $user = User::findOrFail(user()->id);
                $this->waitingApprovalCount = $user->tasks()->where('board_column_id', $taskBoardColumn->id)->where('company_id', company()->id)->count();
            }
            elseif (!in_array('admin', user_roles()) && (in_array('employee', user_roles()) && !$projectIds->isEmpty())) {
                $this->waitingApprovalCount = Task::whereIn('project_id', $projectIds)->where('board_column_id', $taskBoardColumn->id)->where('company_id', company()->id)->count();
            }
            else {
                $this->waitingApprovalCount = Task::where('board_column_id', $taskBoardColumn->id)->where('company_id', company()->id)->count();
            }
        }

        return $dataTable->render('tasks.index', $this->data);
    }

    /**
     * XXXXXXXXXXX
     *
     * @return array
     */
    public function applyQuickAction(Request $request)
    {
        switch ($request->action_type) {
            case 'delete':
                return $this->bilanActionGroupee(
                    $this->deleteRecords($request), __('messages.deleteSuccess'));

            case 'change-status':
                return $this->bilanActionGroupee(
                    $this->changeBulkStatus($request), __('messages.updateSuccess'));

            case 'milestone':
                return $this->bilanActionGroupee(
                    $this->changeMilestones($request), __('messages.updateSuccess'));

            default:
                return Reply::error(__('messages.selectAction'));
        }
    }

    /**
     * Reponse d'une action groupee, selon ce qui a pu etre traite.
     *
     * Le silence serait le pire des retours : une selection de dix
     * taches dont trois echappent a l'utilisateur doit le lui dire,
     * sinon il croit avoir tout supprime.
     *
     * @param array $bilan ['traitees' => int, 'ignorees' => int]
     */
    private function bilanActionGroupee(array $bilan, string $succes)
    {
        if ($bilan['traitees'] === 0) {
            return Reply::error(__('Aucune des tâches sélectionnées ne relève de vos droits.'));
        }

        if ($bilan['ignorees'] > 0) {
            return Reply::success(sprintf(
                __('%d tâche(s) traitée(s). %d ignorée(s), faute de droits sur celles-ci.'),
                $bilan['traitees'], $bilan['ignorees']
            ));
        }

        return Reply::success($succes);
    }

    /**
     * Parmi les taches designees, celles que l'utilisateur peut traiter.
     *
     * La regle est celle qui commande deja l'affichage des boutons et
     * la suppression a l'unite. Les actions groupees, elles, exigeaient
     * « all » : un employe ne pouvait pas supprimer d'un seul geste des
     * taches qu'il avait pourtant creees lui-meme.
     *
     * @param string $droit 'delete_tasks' ou 'edit_tasks'
     */
    private function tachesAutorisees(string $droit, array $ids)
    {
        $niveau = user()->permission($droit);

        if ($niveau === 'none') {
            return collect();
        }

        return Task::with('users', 'project')->whereIn('id', $ids)->get()
            ->filter(function ($tache) use ($niveau) {
                $gens = $tache->users->pluck('id')->toArray();

                return $niveau == 'all'
                    || ($niveau == 'added' && $tache->added_by == user()->id)
                    || ($niveau == 'owned' && in_array(user()->id, $gens))
                    || ($niveau == 'both'
                        && (in_array(user()->id, $gens) || $tache->added_by == user()->id))
                    || ($tache->project && $tache->project->project_admin == user()->id);
            });
    }

    protected function deleteRecords($request)
    {
        $ids = explode(',', $request->row_ids);
        $autorisees = $this->tachesAutorisees('delete_tasks', $ids);

        if ($autorisees->isNotEmpty()) {
            Task::whereIn('id', $autorisees->pluck('id'))->delete();
        }

        return [
            'traitees' => $autorisees->count(),
            'ignorees' => count($ids) - $autorisees->count(),
        ];
    }

    protected function changeBulkStatus($request)
    {
        $taskBoardColumn = TaskboardColumn::findOrFail(request()->status);

        $ids = explode(',', $request->row_ids);

        // Le statut ne change que pour les taches dont l'utilisateur est
        // assigne (ou qu'il arbitre) : les autres sont ignorees.
        $cibles = $this->tachesAutorisees('edit_tasks', $ids)
            ->load('boardColumn')
            ->filter(fn ($tache) => $this->peutChangerStatut($tache, $taskBoardColumn))
            ->pluck('id');

        if ($cibles->isNotEmpty()) {
            if ($taskBoardColumn && $taskBoardColumn->slug == 'completed') {
                Task::whereIn('id', $cibles)->update([
                    'status' => 'completed',
                    'board_column_id' => $request->status,
                    'completed_on' => now()->format('Y-m-d')
                ]);
            }
            else {
                Task::whereIn('id', $cibles)->update(['board_column_id' => $request->status]);
            }
        }

        return [
            'traitees' => $cibles->count(),
            'ignorees' => count($ids) - $cibles->count(),
        ];
    }

    public function changeMilestones($request)
    {
        $ids = explode(',', $request->row_ids);
        $cibles = $this->tachesAutorisees('edit_tasks', $ids)->pluck('id');

        if ($cibles->isNotEmpty()) {
            Task::whereIn('id', $cibles)->update([
                'milestone_id' => $request->milestone
            ]);
        }

        return [
            'traitees' => $cibles->count(),
            'ignorees' => count($ids) - $cibles->count(),
        ];
    }

    public function changeStatus(Request $request)
    {
        $taskId = $request->taskId;
        $status = $request->status;

        $task = Task::withTrashed()->with('project', 'users', 'boardColumn')->findOrFail($taskId);

        $taskBoardColumn = TaskboardColumn::where('slug', $status)->first();

        // Seul l'assigne change le statut ; entrer dans « En attente
        // d'approbation » ou en sortir reste reserve a l'arbitre.
        $this->verifierChangementStatut($task, $taskBoardColumn);

        $task->board_column_id = $taskBoardColumn->id;

        if ($task->status === 'completed' && $status !== 'completed') {
            $task->approval_send = 0; // Reset approval_send to 0
        }

        if ($taskBoardColumn->slug == 'completed') {
            $task->status = 'completed';
            $task->completed_on = now()->format('Y-m-d');
        }
        else {
            $task->completed_on = null;
        }

        if ($task->trashed()) {
            $task->saveQuietly();
        }
        else {
            $task->save();
        }


        if ($task->project_id != null) {

            if ($task->project->calculate_task_progress == 'true') {
                // Calculate project progress if enabled
                $this->calculateProjectProgress($task->project_id, 'true');
            }
        }

        $this->selfActiveTimer = ProjectTimeLog::selfActiveTimer();

        $clockHtml = view('sections.timer_clock', $this->data)->render();

        return Reply::successWithData(__('messages.updateSuccess'), ['clockHtml' => $clockHtml]);

    }

    public function milestoneChange(Request $request)
    {
        $editTaskPermission = user()->permission('edit_tasks');
        $editMilestonePermission = user()->permission('edit_project_milestones');

        $taskId = $request->taskId;
        $milestoneId = $request->milestone_id;

        $task = Task::withTrashed()->with('project', 'users')->findOrFail($taskId);
        $taskUsers = $task->users->pluck('id')->toArray();

        abort_403(
            !(
            ($editTaskPermission == 'all'
            || ($editTaskPermission == 'owned' && in_array(user()->id, $taskUsers))
            || ($editTaskPermission == 'added' && $task->added_by == user()->id)
            || ($task->project && ($task->project->project_admin == user()->id))
            || ($editTaskPermission == 'both' && (in_array(user()->id, $taskUsers) || $task->added_by == user()->id))
            || ($editTaskPermission == 'owned' && (in_array('client', user_roles()) && $task->project && ($task->project->client_id == user()->id)))
            || ($editTaskPermission == 'both' && (in_array('client', user_roles()) && ($task->project && ($task->project->client_id == user()->id)) || $task->added_by == user()->id))
            ) && (
            $editMilestonePermission == 'all'
            || ($editMilestonePermission == 'added' && $task->added_by == user()->id)
            || ($editMilestonePermission == 'owned' && in_array(user()->id, $taskUsers))
            || ($editMilestonePermission == 'owned' && (in_array('client', user_roles()) && $task->project && ($task->project->client_id == user()->id)))
            )
            )
        );

        $task->milestone_id = $milestoneId;
        $task->save();

        return Reply::success(__('messages.updateSuccess'));
    }

    public function destroy(Request $request, $id)
    {
        $task = Task::with('project')->findOrFail($id);

        $this->deletePermission = user()->permission('delete_tasks');

        $taskUsers = $task->users->pluck('id')->toArray();

        /*
         * AUTORISATION DE SUPPRESSION (backend)
         *
         * Un collaborateur (non-admin) ne peut supprimer une tâche que s'il
         * en est le créateur ou l'assigné. Protège contre la suppression
         * par manipulation de l'ID dans l'URL.
         */
        abort_403(
            !(in_array('admin', user_roles())
            || est_arbitre()
            || ($this->deletePermission == 'all' && ($task->added_by == user()->id || in_array(user()->id, $taskUsers)))
            || ($this->deletePermission == 'owned' && in_array(user()->id, $taskUsers))
            || ($task->project && ($task->project->project_admin == user()->id))
            || ($this->deletePermission == 'added' && $task->added_by == user()->id)
            || ($this->deletePermission == 'both' && (in_array(user()->id, $taskUsers) || $task->added_by == user()->id))
            || ($this->deletePermission == 'owned' && (in_array('client', user_roles()) && $task->project && ($task->project->client_id == user()->id)))
            || ($this->deletePermission == 'both' && (in_array('client', user_roles()) && ($task->project && ($task->project->client_id == user()->id)) || $task->added_by == user()->id))
            )
        );

        // If it is recurring and allowed by user to delete all its recurring tasks
        if ($request->has('recurring') && $request->recurring == 'yes') {
            Task::where('recurring_task_id', $id)->delete();
        }

        // Delete current task
        $task->delete();

        return Reply::success(__('messages.deleteSuccess'));
    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $this->pageTitle = __('app.addTask');

        $this->addPermission = user()->permission('add_tasks');
        $this->projectShortCode = '';
        $this->project = request('task_project_id') ?Project::with('projectMembers')->findOrFail(request('task_project_id')) : null;

        if (is_null($this->project) || ($this->project->project_admin != user()->id)) {
            abort_403(!in_array($this->addPermission, ['all', 'added']));
        }

        $this->task = (request()['duplicate_task']) ?Task::with('users', 'label', 'project')->findOrFail(request()['duplicate_task'])->withCustomFields() : null;
        $this->selectedLabel = TaskLabel::where('task_id', request()['duplicate_task'])->get()->pluck('label_id')->toArray();
        $this->projectMember = TaskUser::where('task_id', request()['duplicate_task'])->get()->pluck('user_id')->toArray();

        $this->projects = Project::allProjects(true);

        $this->taskLabels = TaskLabelList::whereNull('project_id')->get();
        $this->projectID = request()->task_project_id;

        if (request('task_project_id')) {
            $project = Project::findOrFail(request('task_project_id'));
            $this->projectShortCode = $project->project_short_code;
            $this->taskLabels = TaskLabelList::where('project_id', request('task_project_id'))->orWhere('project_id', null)->get();
            $this->milestones = ProjectMilestone::where('project_id', request('task_project_id'))->whereNot('status', 'complete')->get();
        }
        else {
            if ($this->task && $this->task->project) {
                $this->milestones = $this->task->project->incompleteMilestones;
            }
            else {
                $this->milestones = collect([]);
            }
        }

        $this->columnId = request('column_id');
        $this->categories = TaskCategory::all();

        $this->taskboardColumns = TaskboardColumn::orderBy('priority', 'asc')->get();
        $completedTaskColumn = TaskboardColumn::where('slug', '=', 'completed')->first();

        if (request()->has('default_assign') && request('default_assign') != '') {
            $this->defaultAssignee = request('default_assign');
        }

        $this->dependantTasks = $completedTaskColumn ?Task::where('board_column_id', '<>', $completedTaskColumn->id)
            ->where('project_id', $this->projectID)
            ->whereNotNull('due_date')->get() : [];

        $this->allTasks = $completedTaskColumn ?Task::where('board_column_id', '<>', $completedTaskColumn->id)->whereNotNull('due_date')->get() : [];

        if (!is_null($this->project)) {
            if ($this->project->public) {
                $this->employees = User::allEmployees(null, true, ($this->addPermission == 'all' ? 'all' : null));

            }
            else {

                $this->employees = $this->project->projectMembers;
            }
        }
        else if (!is_null($this->task) && !is_null($this->task->project_id)) {
            if ($this->task->project->public) {
                $this->employees = User::allEmployees(null, true, ($this->addPermission == 'all' ? 'all' : null));
            }
            else {

                $this->employees = $this->task->project->projectMembers;
            }
        }
        else {
            if (in_array('client', user_roles())) {
                $this->employees = collect([]); // Do not show all employees to client

            }
            else {
                $this->employees = User::allEmployees(null, true, ($this->addPermission == 'all' ? 'all' : null));
            }

        }

        $task = new Task();

        $getCustomFieldGroupsWithFields = $task->getCustomFieldGroupsWithFields();

        if ($getCustomFieldGroupsWithFields) {
            $this->fields = $getCustomFieldGroupsWithFields->fields;
        }

        $userData = [];

        $usersData = $this->employees;

        foreach ($usersData as $user) {

            $url = route('employees.show', [$user->id]);

            $userData[] = ['id' => $user->id, 'value' => $user->name, 'image' => $user->image_url, 'link' => $url];

        }

        $this->userData = $userData;

        $this->allEmployees = User::allEmployees(null, true, 'all');
        $this->allClients = User::allClients();

        $this->backUrl = $this->determinerUrlRetour();
        $this->view = 'tasks.ajax.create';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('tasks.create', $this->data);
    }

    public function selectChannels()
    {
        return view('tasks.ajax.select_channels');
    }

    // The function is called for duplicate code also
    /**
     * Ecran d'importation.
     *
     * Ouvert a qui peut deja creer une tache, au meme titre que le
     * formulaire. Un compte limite a « added » ne contourne rien en
     * important : les taches qu'il cree portent son nom, et restent
     * donc soumises aux memes restrictions de lecture et de
     * modification que celles qu'il saisit a la main.
     */
    public function importTask()
    {
        abort_403(!in_array(user()->permission('add_tasks'), ['all', 'added']));

        $this->colonnes = TaskImport::colonnes();

        /* Les projets du selecteur « projet par defaut ». Le plus
           souvent, un lot de taches concerne un seul projet : le
           choisir ici evite de repeter son nom a chaque ligne. */
        $this->projets = Project::where('company_id', company()->id)
            ->orderBy('project_name')->get(['id', 'project_name']);

        $this->pageTitle = __('Importer des tâches');
        $this->view = 'tasks.ajax.import';

        return view('tasks.create', $this->data);
    }

    /**
     * Modele Excel officiel, avec une ligne d'exemple.
     *
     * Genere a la volee plutot que stocke : les statuts et les colonnes
     * suivent ainsi la configuration reelle, sans fichier a maintenir.
     */
    public function importTaskTemplate()
    {
        abort_403(!in_array(user()->permission('add_tasks'), ['all', 'added']));

        return Excel::download(
            new \App\Exports\ModeleImportTaches(),
            'modele-import-taches.xlsx'
        );
    }

    /**
     * Analyse le fichier et renvoie l'apercu éditable, sans rien enregistrer.
     */
    public function importTaskAnalyse(Request $request)
    {
        abort_403(!in_array(user()->permission('add_tasks'), ['all', 'added']));

        $request->validate(
            ['import_file' => 'required|file|mimes:xlsx,xls,csv,ods'],
            ['import_file.mimes' => __('Ce format de fichier ne peut pas être lu. Déposez un tableur : xlsx, xls, ods ou csv. Un PDF ou une image ne peuvent pas être importés.')],
            ['import_file' => __('Fichier')]
        );

        try {
            $jeton = 'import-taches-' . user()->id . '-' . time() . '.'
                . $request->file('import_file')->getClientOriginalExtension();

            $chemin = $request->file('import_file')
                ->move(storage_path('app/imports-taches'), $jeton)
                ->getPathname();

            $resultat = $this->analyserFichierTaches($chemin, $this->projetDefautChoisi($request));

            if ($resultat->total === 0) {
                @unlink($chemin);
                $jeton = null;
            }

            // Référentiels pour le DataTable éditable
            $societe = company()->id;
            $this->projets = Project::where('company_id', $societe)
                ->orderBy('project_name')->get(['id', 'project_name']);
            $this->categories = TaskCategory::where('company_id', $societe)
                ->orderBy('category_name')->get(['id', 'category_name']);
            $this->statuts = TaskboardColumn::orderBy('priority')->get(['id', 'column_name', 'slug', 'label_color']);
            $this->nomFichier = $request->file('import_file')->getClientOriginalName();

                    $this->resultat = $resultat;
                    $this->jeton = $jeton;

            return Reply::dataOnly([
                'status' => 'success',
                'html' => view('tasks.ajax.import_apercu', $this->data)->render(),
                'peutImporter' => $resultat->total > 0,
            ]);
        }
        catch (\Throwable $e) {
            \Illuminate\Support\Facades\Log::error('Import de taches, analyse : ' . $e->getMessage(), [
                'fichier' => $e->getFile() . ':' . $e->getLine(),
            ]);

            return Reply::error(__('Le fichier n’a pas pu être analysé.') . ' ' . $e->getMessage());
        }
    }

    /**
     * Cree les taches en base à partir des données éditées dans le DataTable.
     */
    public function importTaskProcess(Request $request)
    {
        abort_403(!in_array(user()->permission('add_tasks'), ['all', 'added']));

        try {
            $jeton = basename((string) $request->jeton);
            $chemin = $jeton ? storage_path('app/imports-taches/' . $jeton) : null;

            // Si des tâches modifiées sont transmises directement via le DataTable
            $tachesModifiees = $request->input('tasks');

            if (is_array($tachesModifiees)) {
                if (empty($tachesModifiees)) {
                    return response()->json(
                        Reply::error(__('Aucune tâche à importer.')),
                        422
                    );
                }

                // Validation de base des tâches éditées et manuelles
                $tachesValides = [];
                $erreurs = [];

                foreach ($tachesModifiees as $idx => $t) {
                    $ligneNum = $t['numero'] ?? ($idx + 1);
                    $titre = trim((string)($t['heading'] ?? ''));
                    $dateDebut = trim((string)($t['start_date'] ?? ''));

                    if ($titre === '') {
                        $erreurs[] = sprintf(__('Ligne %d : le titre de la tâche est obligatoire.'), $ligneNum);
                    }

                    if ($dateDebut === '') {
                        $erreurs[] = sprintf(__('Ligne %d : la date de début est obligatoire.'), $ligneNum);
                    }

                    if ($titre !== '' && $dateDebut !== '') {
                        $tachesValides[] = $t;
                    }
                }

                if (!empty($erreurs)) {
                    return response()->json(
                        Reply::error(implode('<br>', $erreurs)),
                        422
                    );
                }

                if (empty($tachesValides)) {
                    return response()->json(
                        Reply::error(__('Aucune tâche valide à importer. Veuillez renseigner au moins un titre et une date de début.')),
                        422
                    );
                }

                $resultatCreation = $this->creerTachesDepuisDonneesEditables($tachesValides);
                $creees   = $resultatCreation->creees;
                $ignorees = $resultatCreation->ignorees;

                if ($chemin && is_file($chemin)) {
                    @unlink($chemin);
                }

                if ($creees === 0 && $ignorees > 0) {
                    return response()->json(
                        Reply::error(
                            $ignorees === 1
                                ? __('La tâche n\'a pas été importée car elle existe déjà en base.')
                                : sprintf(__('Aucune tâche importée : les %d tâches existent déjà en base.'), $ignorees)
                        ),
                        422
                    );
                }

                $message = sprintf(__('%d tâches ont été importées avec succès.'), $creees);
                if ($ignorees > 0) {
                    $message .= ' ' . sprintf(
                        $ignorees === 1
                            ? __('(%d tâche ignorée car déjà existante.)')
                            : __('(%d tâches ignorées car déjà existantes.)'),
                        $ignorees
                    );
                }

                return response()->json(
                    Reply::successWithData(
                        $message,
                        ['creees' => $creees, 'redirectUrl' => route('taskboards.index')]
                    )
                );
            }

            // Fallback : import direct depuis le fichier conservé si aucune donnée JSON
            if (!$jeton || !$chemin || !is_file($chemin)) {
                return response()->json(
                    Reply::error(__('Le fichier importé est introuvable. Recommencez.')), 422
                );
            }

            $resultat = $this->analyserFichierTaches($chemin, $this->projetDefautChoisi($request));

            if ($resultat->total === 0) {
                @unlink($chemin);

                return response()->json(
                    Reply::error($resultat->message
                        ?: __('Le fichier contient des erreurs. Corrigez-le avant de recommencer.')),
                    422
                );
            }

            $valides = array_values(array_filter(
                $resultat->lignes, fn($l) => empty($l->erreurs)
            ));

            if (!$valides) {
                @unlink($chemin);

                return response()->json(
                    Reply::error(__('Aucune ligne de ce fichier n\'est exploitable. Corrigez-le avant de recommencer.')),
                    422
                );
            }

            $creees = $this->creerTachesImportees($valides);

            @unlink($chemin);

            return response()->json(
                Reply::successWithData(
                    sprintf(__('%d tâches ont été importées avec succès.'), $creees),
                    ['creees' => $creees, 'redirectUrl' => route('taskboards.index')]
                )
            );
        } catch (\Throwable $e) {
            Log::error('Import de taches, confirmation : ' . $e->getMessage(), [
                'fichier' => $e->getFile() . ':' . $e->getLine(),
            ]);

            return response()->json(
                Reply::error(__('Une erreur est survenue lors de l\'importation : ') . $e->getMessage()),
                500
            );
        }
    }

    /**
     * Projet applique aux lignes dont la colonne Projet est vide.
     *
     * Verifie ici plutot que dans le trait : l'identifiant vient du
     * navigateur, et rien ne garantit qu'il designe un projet du
     * cabinet. Un identifiant etranger est simplement ignore.
     */
    private function projetDefautChoisi(Request $request): ?int
    {
        $id = (int) $request->projet_defaut;

        if (!$id) {
            return null;
        }

        return Project::where('company_id', company()->id)
            ->where('id', $id)->value('id');
    }

    /**
     * Previent les collaborateurs a qui une tache vient d'etre confiee.
     *
     * L'auteur n'est jamais notifie : il vient d'ecrire la tache.
     *
     * @param array|null $ids identifiants des assignes
     */
    private function notifierNouveauxAssignes(Task $task, $ids): void
    {
        $ids = array_filter((array) $ids);

        if (!$ids) {
            return;
        }

        /* On ne se previent pas soi-meme. */
        $ids = array_diff($ids, [user()?->id]);

        if (!$ids) {
            return;
        }

        $gens = User::whereIn('id', $ids)->get();

        if ($gens->isEmpty()) {
            return;
        }

        /* Un envoi qui echoue ne doit pas defaire l'enregistrement.

           La tache est deja en base quand on arrive ici. Laisser
           remonter l'exception afficherait une erreur pour une
           creation pourtant reussie, et l'utilisateur recommencerait —
           c'est ainsi qu'on fabrique des doublons.

           Le serveur refuse par exemple les adresses desactivees
           (« 525 Disabled recipient address »), ce qui est le cas de
           plusieurs boites du domaine. On le consigne et l'on
           poursuit ; la notification dans l'application, elle, est
           ecrite quoi qu'il arrive. */
        try {
            event(new TaskEvent($task, $gens, 'NewTask'));
        }
        catch (\Throwable $e) {
            Log::warning('Tache #' . $task->id . ' : notification d\'affectation non delivree — '
                . $e->getMessage());
        }
    }

    public function store(StoreTask $request)
    {
        $project = request('project_id') ?Project::findOrFail(request('project_id')) : null;

        if (is_null($project) || ($project->project_admin != user()->id)) {
            $this->addPermission = user()->permission('add_tasks');
            abort_403(!in_array($this->addPermission, ['all', 'added']));
        }

        DB::beginTransaction();
        $ganttTaskArray = [];
        $gantTaskLinkArray = [];

        $taskBoardColumn = TaskboardColumn::where('slug', 'incomplete')->first();
        $task = new Task();
        $task->heading = $request->heading;
        $task->description = trim_editor($request->description);
        $dueDate = ($request->has('without_duedate') || empty($request->due_date)) ? null : companyToCarbon($request->due_date);
        $task->start_date = companyToCarbon($request->start_date) ?? now();
        $task->due_date = $dueDate;
        $task->project_id = $request->project_id;
        $task->task_category_id = $request->category_id;
        $task->priority = $request->priority;
        $task->board_column_id = $taskBoardColumn->id;

        if ($request->has('dependent') && $request->has('dependent_task_id') && $request->dependent_task_id != '') {
            $dependentTask = Task::findOrFail($request->dependent_task_id);

            if (!is_null($dependentTask->due_date) && !is_null($dueDate) && $dependentTask->due_date->greaterThan($dueDate)) {
                /* @phpstan-ignore-line */
                return Reply::error(__('messages.taskDependentDate'));
            }

            $task->dependent_task_id = $request->dependent_task_id;
        }

        $task->is_private = $request->has('is_private') ? 1 : 0;
        $task->billable = $request->has('billable') && $request->billable ? 1 : 0;
        $task->estimate_hours = (int) $request->estimate_hours;
        $task->estimate_minutes = (int) $request->estimate_minutes;

        if ($request->board_column_id) {
            $task->board_column_id = $request->board_column_id;
        }

        $waitingApprovalTaskBoardColumn = TaskboardColumn::waitingForApprovalColumn();
        if ($request->board_column_id == $waitingApprovalTaskBoardColumn->id) {
            $task->approval_send = 1;
        }
        else {
            $task->approval_send = 0;
        }

        if ($request->milestone_id != '') {
            $task->milestone_id = $request->milestone_id;
        }

        // Add repeated task
        $task->repeat = $request->repeat ? 1 : 0;

        if ($request->has('repeat')) {
            $task->repeat_count = $request->repeat_count;
            $task->repeat_type = $request->repeat_type;
            $task->repeat_cycles = $request->repeat_cycles;
        }

        $task->responsible_id = $request->responsible_id;

        if ($project) {
            $projectLastTaskCount = Task::projectTaskCount($project->id);

            if (isset($project->project_short_code)) {
                $task->task_short_code = $project->project_short_code . '-' . $this->getTaskShortCode($project->project_short_code, $projectLastTaskCount);
            }
            else {
                $task->task_short_code = $projectLastTaskCount + 1;
            }
        }

        if (user()) {
            $task->added_by = user()->id;
            $task->last_updated_by = user()->id;
        }

        $task->save();

        // Save labels
        if ($request->task_labels) {
            $task->labels()->sync($request->task_labels);
        }

        // Save assignees
        if ($request->user_id) {
            $task->users()->sync($request->user_id);

            /* Prevenir les collaborateurs a qui la tache est confiee.

               Cet envoi avait ete coupe dans TaskObserver pour cause de
               doublons : la notification partait a la fois de
               l'observateur de la tache et de celui du pivot
               task_users, que sync() declenche lui aussi. Creer une
               tache ne prevenait donc plus personne, alors que la
               modification, elle, prevenait toujours les nouveaux
               assignes — d'ou l'incoherence constatee.

               On la retablit ici, au meme endroit et de la meme facon
               que dans update() : un seul point d'emission, sur le
               chemin explicite du controleur. Les observateurs restent
               muets, ce qui evite le doublon d'origine.

               TaskListener ecarte de lui-meme les taches privees, et
               NewTask respecte les preferences de chacun. */
            $this->notifierNouveauxAssignes($task, $request->user_id);
        }


        if (!is_null($request->taskId)) {

            $taskExists = TaskFile::where('task_id', $request->taskId)->get();

            if ($taskExists) {
                foreach ($taskExists as $taskExist) {
                    $file = new TaskFile();
                    $file->user_id = $taskExist->user_id;
                    $file->task_id = $task->id;

                    $fileName = Files::generateNewFileName($taskExist->filename);

                    Files::copy(TaskFile::FILE_PATH . '/' . $taskExist->task_id . '/' . $taskExist->hashname, TaskFile::FILE_PATH . '/' . $task->id . '/' . $fileName);

                    $file->filename = $taskExist->filename;
                    $file->hashname = $fileName;
                    $file->size = $taskExist->size;
                    $file->save();


                    $this->logTaskActivity($task->id, $this->user->id, 'fileActivity', $task->board_column_id);
                }
            }


            $subTask = SubTask::with(['files'])->where('task_id', $request->taskId)->get();


            if ($subTask) {
                foreach ($subTask as $subTasks) {
                    $subTaskData = new SubTask();
                    $subTaskData->title = $subTasks->title;
                    $subTaskData->task_id = $task->id;
                    $subTaskData->description = trim_editor($subTasks->description);

                    if ($subTasks->start_date != '' && $subTasks->due_date != '') {
                        $subTaskData->start_date = $subTasks->start_date;
                        $subTaskData->due_date = $subTasks->due_date;
                    }

                    $subTaskData->assigned_to = $subTasks->assigned_to;

                    $subTaskData->save();

                    if ($subTasks->files) {
                        foreach ($subTasks->files as $fileData) {
                            $file = new SubTaskFile();
                            $file->user_id = $fileData->user_id;
                            $file->sub_task_id = $subTaskData->id;

                            $fileName = Files::generateNewFileName($fileData->filename);

                            Files::copy(SubTaskFile::FILE_PATH . '/' . $fileData->sub_task_id . '/' . $fileData->hashname, SubTaskFile::FILE_PATH . '/' . $subTaskData->id . '/' . $fileName);

                            $file->filename = $fileData->filename;
                            $file->hashname = $fileName;
                            $file->size = $fileData->size;
                            $file->save();
                        }
                    }
                }
            }
        }

        // To add custom fields data
        if ($request->custom_fields_data) {
            $task->updateCustomFieldData($request->custom_fields_data);
        }

        // For gantt chart
        if ($request->page_name && !is_null($task->due_date) && $request->page_name == 'ganttChart') {
            $task = Task::find($task->id);
            $parentGanttId = $request->parent_gantt_id;

            /* @phpstan-ignore-next-line */

            $taskDuration = $task->due_date->diffInDays($task->start_date);
            /* @phpstan-ignore-line */
            $taskDuration = $taskDuration + 1;

            $ganttTaskArray[] = [
                'id' => $task->id,
                'text' => $task->heading,
                'start_date' => $task->start_date->format('Y-m-d'), /* @phpstan-ignore-line */
                'duration' => $taskDuration,
                'parent' => $parentGanttId,
                'taskid' => $task->id
            ];

            $gantTaskLinkArray[] = [
                'id' => 'link_' . $task->id,
                'source' => $task->dependent_task_id != '' ? $task->dependent_task_id : $parentGanttId,
                'target' => $task->id,
                'type' => $task->dependent_task_id != '' ? 0 : 1
            ];
        }

        // Si un client est sélectionné dans le formulaire et que le projet n'en a pas,
        // on l'enregistre automatiquement comme client du projet
        $selectedClients = $request->all_clients ?? [];
        if ($project && !$project->client_id && !empty($selectedClients)) {
            $project->client_id = $selectedClients[0];
            $project->save();
            // Rafraîchir la relation pour que les notifications aient le client
            $project->load('client');
            $task->load('project');
        }

        DB::commit();

        \Illuminate\Support\Facades\Log::info('DEBUG TaskController::store - After commit', [
            'task_id' => $task->id,
            'task_id_raw' => $task->getAttributes()['id'] ?? 'null',
            'task_heading' => $task->heading
        ]);

        // Dispatch pro notifications automatically
        $dispatcher = new \App\Services\NotificationDispatcher();
        $channels = $request->chosen_channels ?? ['email', 'whatsapp'];

        // Automatic dispatch (all assignees + project client - Type Init)
        $dispatcher->dispatchTaskDelegation($task, $channels, $request->all_clients ?? []);

        // Additional manual notifications if any extra employees/clients selected
        $dispatcher->dispatchManualNotifications($task, $request->all_employees ?? [], $request->all_clients ?? [], $channels);

        if (request()->add_more == 'true') {
            unset($request->project_id);
            $html = $this->create();

            return Reply::successWithData(__('messages.recordSaved'), ['html' => $html, 'add_more' => true, 'taskID' => $task->id]);
        }

        if ($request->page_name && $request->page_name == 'ganttChart') {

            return Reply::successWithData(
                'messages.recordSaved',
            [
                'tasks' => $ganttTaskArray,
                'links' => $gantTaskLinkArray
            ]
            );
        }

        $redirectUrl = urldecode($request->redirect_url);

        if ($redirectUrl == '') {
            $redirectUrl = route('taskboards.index');
        }

        return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => $redirectUrl, 'taskID' => $task->id]);

    }

    /**
     * XXXXXXXXXXX
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function edit($id)
    {
        $editTaskPermission = user()->permission('edit_tasks');
        $this->task = Task::with('users', 'label', 'project', )->findOrFail($id)->withCustomFields();
        $this->taskUsers = $taskUsers = $this->task->users->pluck('id')->toArray();
        /*
         * AUTORISATION D'EDITION (backend)
         *
         * Un collaborateur (non-admin) ne peut modifier une tâche que s'il
         * en est le créateur ou l'assigné. La vérification est faite côté
         * serveur pour éviter les contournements par manipulation d'URL.
         */
        abort_403(!peut_modifier_tache($this->task));

        $getCustomFieldGroupsWithFields = $this->task->getCustomFieldGroupsWithFields();

        if ($getCustomFieldGroupsWithFields) {
            $this->fields = $getCustomFieldGroupsWithFields->fields;
        }


        $this->pageTitle = __('modules.tasks.updateTask');
        $this->labelIds = $this->task->label->pluck('label_id')->toArray();
        $this->projects = Project::allProjects(true);
        $this->categories = TaskCategory::all();
        $projectId = $this->task->project_id;

        if ($projectId) {
            $this->taskLabels = TaskLabelList::where('project_id', $projectId)->orWhereNull('project_id')->get();
        }
        else {
            $this->taskLabels = TaskLabelList::whereNull('project_id')->get();
        }

        $this->taskboardColumns = TaskboardColumn::orderBy('priority', 'asc')->get();
        $this->changeStatusPermission = user()->permission('change_status');
        $completedTaskColumn = TaskboardColumn::where('slug', '=', 'completed')->first();
        $this->waitingApprovalTaskBoardColumn = TaskboardColumn::waitingForApprovalColumn();
        if ($completedTaskColumn) {
            $this->allTasks = Task::where('board_column_id', '<>', $completedTaskColumn->id)->whereNotNull('due_date')->where('id', '!=', $id)->where('project_id', $projectId)->get();
        }
        else {
            $this->allTasks = [];
        }

        if ($this->task->project_id) {
            if ($this->task->project->public) {
                $this->employees = User::allEmployees(null, false, ($editTaskPermission == 'all' ? 'all' : null));

            }
            else {
                $this->employees = $this->task->project->projectMembersWithoutScope;
            }
        }
        else {
            if ($editTaskPermission == 'added' || $editTaskPermission == 'owned') {
                $this->employees = ((count($this->task->users) > 0) ? $this->task->users : User::allEmployees(null, true, ($editTaskPermission == 'all' ? 'all' : null)));

            }
            else {
                $this->employees = User::allEmployees(null, false, ($editTaskPermission == 'all' ? 'all' : null));
            }
        }


        $uniqueId = $this->task->task_short_code;
        // check if unuqueId contains -
        if (strpos($uniqueId, '-') !== false) {
            $uniqueId = explode('-', $uniqueId, 2);
            $this->projectUniId = $uniqueId[0];
            $this->taskUniId = $uniqueId[1];
        }
        else {
            $this->projectUniId = ($this->task->project_id != null) ? $this->task->project->project_short_code : null;
            $this->taskUniId = $uniqueId;
        }

        $userId = $this->task->users->pluck('id')->toArray();
        $startDate = $this->task->start_date;
        $dueDate = $this->task->due_date;
        $leaves = $this->leaves($userId, $startDate, $dueDate);

        if (!is_null($leaves)) {
            $data = [];

            foreach ($leaves as $key => $value) {
                $values = implode(', ', $value);
                $data[] = $key . __('modules.tasks.leaveOn') . ' ' . $values;
            }

            $this->leaveData = implode("\n", $data);
        /* @phpstan-ignore-line */

        }

        $userData = [];

        $usersData = $this->employees;

        foreach ($usersData as $user) {

            $url = route('employees.show', [$user->id]);

            $userData[] = ['id' => $user->id, 'value' => $user->name, 'image' => $user->image_url, 'link' => $url];

        }

        $this->userData = $userData;

        $this->allEmployees = User::allEmployees(null, true, 'all');
        $this->allClients = User::allClients();

        $this->backUrl = $this->determinerUrlRetour((int) $id);
        $this->view = 'tasks.ajax.edit';

        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        return view('tasks.create', $this->data);

    }

    public function update(UpdateTask $request, $id)
    {
        $task = Task::with('users', 'label', 'project')->findOrFail($id)->withCustomFields();
        $editTaskPermission = user()->permission('edit_tasks');
        $taskUsers = $task->users->pluck('id')->toArray();

        /*
         * AUTORISATION DE MISE A JOUR (backend)
         *
         * Même règle que pour l'édition : seul le créateur ou l'assigné
         * (ou un admin/arbitre) peut modifier la tâche.
         */
        abort_403(!peut_modifier_tache($task));

        $dueDate = ($request->has('without_duedate') || empty($request->due_date)) ? null : companyToCarbon($request->due_date);
        $task->heading = $request->heading;
        $task->description = trim_editor($request->description);
        $task->start_date = companyToCarbon($request->start_date) ?? now();
        $task->due_date = $dueDate;
        $task->task_category_id = $request->category_id;
        $task->priority = $request->priority;


        if ($request->has('board_column_id')) {

            $taskBoardColumn = TaskboardColumn::findOrFail($request->board_column_id);

            // Meme regle que sur le tableau : le formulaire d'edition ne
            // doit offrir ni un contournement de l'etape d'approbation, ni
            // un changement de statut a qui n'est pas assigne.
            $this->verifierChangementStatut($task, $taskBoardColumn);

            $task->board_column_id = $request->board_column_id;
            $task->approval_send = 0;

            if ($taskBoardColumn->slug == 'completed') {
                $task->completed_on = now()->format('Y-m-d');
            }
            else {
                $task->completed_on = null;
            }
        }

        if ($request->select_value == 'Waiting Approval') {

            // Envoi en approbation depuis le formulaire : meme regle que
            // sendApproval(), reserve a l'assigne (ou a l'arbitre).
            $this->verifierDemandeApprobation($task);

            $taskBoardColumn = TaskboardColumn::where('column_name', $request->select_value)->where('company_id', company()->id)->first();
            $task->board_column_id = $taskBoardColumn->id;
            $task->approval_send = 1;
        }

        $task->dependent_task_id = $request->has('dependent') && $request->has('dependent_task_id') && $request->dependent_task_id != '' ? $request->dependent_task_id : null;
        $task->is_private = $request->has('is_private') ? 1 : 0;
        $task->billable = $request->has('billable') && $request->billable ? 1 : 0;
        $task->estimate_hours = (int) $request->estimate_hours;
        $task->estimate_minutes = (int) $request->estimate_minutes;

        if ($request->project_id != '') {
            $task->project_id = $request->project_id;
        }
        else {
            $task->project_id = null;
        }

        if ($request->has('milestone_id')) {
            $task->milestone_id = $request->milestone_id;
        }

        if ($request->has('dependent') && $request->has('dependent_task_id') && $request->dependent_task_id != '') {
            $dependentTask = Task::findOrFail($request->dependent_task_id);

            if (!is_null($dependentTask->due_date) && !is_null($dueDate) && $dependentTask->due_date->greaterThan($dueDate)) {
                return Reply::error(__('messages.taskDependentDate'));
            }

            $task->dependent_task_id = $request->dependent_task_id;
        }

        // Add repeated task
        $task->repeat = $request->repeat ? 1 : 0;

        if ($request->has('repeat')) {
            $task->repeat_count = $request->repeat_count;
            $task->repeat_type = $request->repeat_type;
            $task->repeat_cycles = $request->repeat_cycles;
        }

        $task->responsible_id = $request->responsible_id;

        $task->load('project');

        $project = $task->project;

        if ($project && $task->isDirty('project_id')) {
            $projectLastTaskCount = Task::projectTaskCount($project->id);
            $task->task_short_code = $project->project_short_code . '-' . $this->getTaskShortCode($project->project_short_code, $projectLastTaskCount);
        }
        $task->save();

        // save labels
        $task->labels()->sync($request->task_labels);

        // To add custom fields data
        if ($request->custom_fields_data) {
            $task->updateCustomFieldData($request->custom_fields_data);
        }

        // Sync task users
        $task->users()->sync($request->user_id);

        if (!empty($request->user_id)) {
            /* Les seuls nouveaux venus : reassigner une tache ne doit
               pas re-prevenir ceux qui la portaient deja.

               Meme methode qu'a la creation, donc meme protection
               contre un envoi qui echoue, et meme regle sur l'auteur,
               qui ne se notifie pas lui-meme. */
            $this->notifierNouveauxAssignes(
                $task, array_diff($request->user_id, $taskUsers));
        }

        // Dispatch pro notifications automatically
        $dispatcher = new \App\Services\NotificationDispatcher();
        $channels = $request->chosen_channels ?? ['email', 'whatsapp'];

        // Automatic dispatch (all assignees + project client - Type Update)
        $dispatcher->dispatchTaskUpdate($task, $channels, $request->all_clients ?? []);

        // Additional manual notifications if any extra employees/clients selected
        $dispatcher->dispatchManualNotifications($task, $request->all_employees ?? [], $request->all_clients ?? [], $channels);

        return Reply::successWithData(__('messages.updateSuccess'), ['redirectUrl' => route('tasks.show', $id)]);
    }

    /**
     * @param $projectShortCode
     * @param $lastProjectCount
     * @return mixed
     */
    public function getTaskShortCode($projectShortCode, $lastProjectCount)
    {
        $task = Task::where('task_short_code', $projectShortCode . '-' . $lastProjectCount)->exists();

        if ($task) {
            return $this->getTaskShortCode($projectShortCode, $lastProjectCount + 1);
        }

        return $lastProjectCount;

    }

    public function show($id)
    {

        $viewTaskFilePermission = user()->permission('view_task_files');
        $viewSubTaskPermission = user()->permission('view_sub_tasks');
        $this->viewTaskCommentPermission = user()->permission('view_task_comments');
        $this->viewTaskNotePermission = user()->permission('view_task_notes');
        $this->viewUnassignedTasksPermission = user()->permission('view_unassigned_tasks');

        $this->task = Task::with(
        ['boardColumn', 'project', 'users', 'label', 'approvedTimeLogs', 'mentionTask',
            'approvedTimeLogs.user', 'approvedTimeLogs.activeBreak', 'comments',
            'comments.commentEmoji', 'comments.like', 'comments.dislike', 'comments.likeUsers',
            'comments.dislikeUsers', 'comments.user', 'subtasks.files', 'userActiveTimer',
            'files' => function ($q) use ($viewTaskFilePermission) {
            if ($viewTaskFilePermission == 'added') {
                $q->where('added_by', user()->id);
            }
        },
            'subtasks' => function ($q) use ($viewSubTaskPermission) {
            if ($viewSubTaskPermission == 'added') {
                $q->where('added_by', user()->id);
            }
        }]
        )
            ->withCount('subtasks', 'files', 'comments', 'activeTimerAll')
            ->findOrFail($id)->withCustomFields();


        $this->taskUsers = $taskUsers = $this->task->users->pluck('id')->toArray();

        $taskuserData = [];

        $usersData = $this->task->users;

        if ($this->task->createBy && !in_array($this->task->createBy->id, $taskUsers)) {
            $url = route('employees.show', [$this->task->createBy->user_id ?? $this->task->createBy->id]);
            $taskuserData[] = ['id' => $this->task->createBy->user_id ?? $this->task->createBy->id, 'value' => $this->task->createBy->user->name ?? $this->task->createBy->name, 'image' => $this->task->createBy->user->image_url ?? $this->task->createBy->image_url, 'link' => $url];
        }

        foreach ($usersData as $user) {

            $url = route('employees.show', [$user->user_id ?? $user->id]);
            $taskuserData[] = ['id' => $user->user_id ?? $user->id, 'value' => $user->user->name ?? $user->name, 'image' => $user->user->image_url ?? $user->image_url, 'link' => $url];

        }

        $this->taskuserData = $taskuserData;

        $this->taskSettings = TaskSetting::first();
        $viewTaskPermission = user()->permission('view_tasks');
        $mentionUser = $this->task->mentionTask->pluck('user_id')->toArray();

        /*
         * AUTORISATION D'ACCES AU DETAIL D'UNE TACHE (backend)
         *
         * Un collaborateur (non-admin) peut consulter une tâche s'il en est
         * le créateur, l'assigné ou le mentionné, ou si sa permission
         * view_tasks vaut 'all' et que la tâche n'est pas privée. Cette
         * vérification est faite côté serveur : manipuler l'URL ou l'ID de
         * la tâche ne contourne pas cette règle.
         *
         * Les admins (roles_arbitres) voient toutes les tâches.
         * NOTE : le paramètre ?tab=project ne contourne PAS cette vérification.
         */
        $isAdmin = in_array('admin', user_roles());
        $isArbitre = est_arbitre();
        $isTaskCreator = ($this->task->added_by == user()->id || $this->task->created_by == user()->id);
        $isTaskAssignee = in_array(user()->id, $taskUsers);
        $isTaskMentioned = !is_null($this->task->mentionTask) && in_array(user()->id, $mentionUser);
        $isProjectAdmin = $this->task->project_id && !is_null($this->task->project) && $this->task->project->project_admin == user()->id;
        $isClientOfProject = in_array('client', user_roles())
            && $this->task->project_id
            && !is_null($this->task->project)
            && $this->task->project->client_id == user()->id;
        $hasAllPermission = ($viewTaskPermission == 'all' && $this->task->is_private == 0);

        abort_403(
            !(
            $isAdmin
            || $isArbitre
            || $isTaskCreator
            || $isTaskAssignee
            || $isTaskMentioned
            || $isProjectAdmin
            || $isClientOfProject
            || $hasAllPermission
            )
        );

        if ($this->task->task_short_code) {
            $this->pageTitle = __('app.task') . ' # ' . $this->task->task_short_code;
        }
        else {
            $this->pageTitle = __('app.task');
        }
        $this->status = TaskboardColumn::where('id', $this->task->board_column_id)->first();
        $getCustomFieldGroupsWithFields = $this->task->getCustomFieldGroupsWithFields();

        if ($getCustomFieldGroupsWithFields) {
            $this->fields = $getCustomFieldGroupsWithFields->fields;
        }


        $this->employees = User::join('employee_details', 'users.id', '=', 'employee_details.user_id')
            ->leftJoin('project_time_logs', 'project_time_logs.user_id', '=', 'users.id')
            ->leftJoin('designations', 'employee_details.designation_id', '=', 'designations.id');


        $this->employees = $this->employees->select(
            'users.name',
            'users.image',
            'users.id',
            'designations.name as designation_name'
        );

        $this->employees = $this->employees->where('project_time_logs.task_id', '=', $id);

        $this->employees = $this->employees->groupBy('project_time_logs.user_id')
            ->orderBy('users.name')
            ->get();

        $this->breakMinutes = ProjectTimeLogBreak::taskBreakMinutes($this->task->id);

        // Add Gitlab task details if available
        if (module_enabled('Gitlab')) {
            if (in_array('gitlab', user_modules()) && !is_null($this->task->project_id)) {

                /** @phpstan-ignore-next-line */
                $this->gitlabSettings = \Modules\Gitlab\Entities\GitlabSetting::where('user_id', user()->id)->first();

                if (!$this->gitlabSettings) {
                    /** @phpstan-ignore-next-line */
                    $this->gitlabSettings = \Modules\Gitlab\Entities\GitlabSetting::whereNull('user_id')->first();
                }

                if ($this->gitlabSettings) {
                    /** @phpstan-ignore-next-line */
                    Config::set('gitlab.connections.main.token', $this->gitlabSettings->personal_access_token);
                    /** @phpstan-ignore-next-line */
                    Config::set('gitlab.connections.main.url', $this->gitlabSettings->gitlab_url);

                    /** @phpstan-ignore-next-line */
                    $gitlabProject = \Modules\Gitlab\Entities\GitlabProject::where('project_id', $this->task->project_id)->first();
                    /** @phpstan-ignore-next-line */
                    $gitlabTask = \Modules\Gitlab\Entities\GitlabTask::where('task_id', $id)->first();

                    if ($gitlabTask) {
                        /** @phpstan-ignore-next-line */
                        $gitlabIssue = \GrahamCampbell\GitLab\Facades\GitLab::issues()->all(intval($gitlabProject->gitlab_project_id), ['iids' => [intval($gitlabTask->gitlab_task_iid)]]);

                        if ($gitlabIssue) {
                            $this->gitlabIssue = $gitlabIssue[0];
                        }
                    }
                }
            }
        }

        $tab = request('view');

        switch ($tab) {
            case 'sub_task':
                $this->tab = 'tasks.ajax.sub_tasks';
                break;
            case 'comments':
                abort_403($this->viewTaskCommentPermission == 'none');

                $this->tab = 'tasks.ajax.comments';
                break;
            case 'notes':
                abort_403($this->viewTaskNotePermission == 'none');
                $this->tab = 'tasks.ajax.notes';
                break;
            case 'history':
                $this->tab = 'tasks.ajax.history';
                break;
            case 'communication':
                $this->tab = 'tasks.ajax.communication';
                break;
            case 'notes_workspace':
                $this->notes = \App\Models\Note::where('noteable_id', $id)
                    ->where('noteable_type', \App\Models\Task::class)
                    ->orderBy('updated_at', 'desc')
                    ->get();
                $this->tab = 'tasks.ajax.notes_workspace';
                break;
            case 'time_logs':
                abort_403(!in_array('timelogs', user_modules()));
                $this->tab = 'tasks.ajax.timelogs';
                break;
            default:
                if ($this->taskSettings->files == 'yes' && in_array('client', user_roles())) {
                    $this->tab = 'tasks.ajax.files';
                }
                elseif ($this->taskSettings->sub_task == 'yes' && in_array('client', user_roles())) {
                    $this->tab = 'tasks.ajax.sub_tasks';
                }
                elseif ($this->taskSettings->comments == 'yes' && in_array('client', user_roles())) {
                    abort_403($this->viewTaskCommentPermission == 'none');
                    $this->tab = 'tasks.ajax.comments';
                }
                elseif ($this->taskSettings->time_logs == 'yes' && in_array('client', user_roles())) {
                    abort_403($this->viewTaskNotePermission == 'none');
                    $this->tab = 'tasks.ajax.timelogs';
                }
                elseif ($this->taskSettings->notes == 'yes' && in_array('client', user_roles())) {
                    abort_403($this->viewTaskNotePermission == 'none');
                    $this->tab = 'tasks.ajax.notes';
                }
                elseif ($this->taskSettings->history == 'yes' && in_array('client', user_roles())) {
                    abort_403($this->viewTaskNotePermission == 'none');
                    $this->tab = 'tasks.ajax.history';
                }
                elseif (!in_array('client', user_roles())) {
                    $this->tab = 'tasks.ajax.files';
                }
                break;
        }

        $this->backUrl = $this->determinerUrlRetour((int) $id);

        if (request()->ajax()) {
            $view = request('json') ? $this->tab : 'tasks.ajax.show';

            return $this->returnAjax($view);
        }


        $this->view = 'tasks.ajax.show';

        return view('tasks.create', $this->data);

    }

    /**
     * Determine l'URL de retour vers la vue d'origine (tableau des taches par defaut),
     * en preservant les filtres de navigation.
     */
    private function determinerUrlRetour(?int $taskId = null): string
    {
        $referer = request('redirectUrl') ?: request()->header('referer');

        if ($referer) {
            if (str_starts_with($referer, 'http%3A') || str_starts_with($referer, 'https%3A')) {
                $referer = urldecode($referer);
            }

            // On ne memorise pas l'URL si elle boucle sur le detail de cette tache
            $isSelf = $taskId ? str_contains($referer, '/account/tasks/' . $taskId) : false;

            if (!$isSelf && (str_contains($referer, '/account/taskboards') || str_contains($referer, '/account/tasks'))) {
                session(['task_back_url' => $referer]);
            }
        }

        $backUrl = session('task_back_url');

        // Si aucune URL memorisee ou si elle boucle sur la tache courante, retour au tableau des taches
        if (!$backUrl || ($taskId && str_contains($backUrl, '/account/tasks/' . $taskId))) {
            $backUrl = route('taskboards.index');
        }

        return $backUrl;
    }

    public function storePin(Request $request)
    {
        $pinned = new Pinned();
        $pinned->task_id = $request->task_id;
        $pinned->project_id = $request->project_id;
        $pinned->save();

        return Reply::success(__('messages.pinnedSuccess'));
    }

    public function destroyPin(Request $request, $id)
    {
        $type = ($request->type == 'task') ? 'task_id' : 'project_id';

        Pinned::where($type, $id)->where('user_id', user()->id)->delete();

        return Reply::success(__('messages.deleteSuccess'));
    }

    public function checkTask($taskID)
    {
        $task = Task::withTrashed()->findOrFail($taskID);
        $subTask = SubTask::where(['task_id' => $taskID, 'status' => 'incomplete'])->count();

        return Reply::dataOnly(['taskCount' => $subTask, 'lastStatus' => $task->boardColumn->slug]);
    }

    public function sendApproval(Request $request)
    {

        $task = Task::with('users', 'project')->findOrFail($request->taskId);
        $taskBoardColumn = TaskboardColumn::where('slug', 'waiting_approval')->first();

        // Cette route n'exigeait aucun droit : n'importe quel compte
        // authentifie pouvait y envoyer n'importe quelle tache par son id.
        // La demande reste ouverte aux personnes concernees par la tache.
        $this->verifierDemandeApprobation($task);

        $task->approval_send = $request->isApproval ?? 0;
        $task->board_column_id = $taskBoardColumn->id;
        $task->save();

        return Reply::success(__('messages.updateSuccess'));
    }

    public function waitingApproval(WaitingForApprovalDataTable $dataTable)
    {
        $viewPermission = user()->permission('view_tasks');

        abort_403(!in_array($viewPermission, ['all', 'added', 'owned', 'both']));

        if (!request()->ajax()) {
            $this->assignedTo = request()->assignedTo;

            if (request()->has('assignee') && request()->assignee == 'me') {
                $this->assignedTo = user()->id;
            }

            $this->projects = Project::allProjects();

            if (in_array('client', user_roles())) {
                $this->clients = User::client();
            }
            else {
                $this->clients = User::allClients();
            }

            $this->employees = User::allEmployees(null, true, ($viewPermission == 'all' ? 'all' : null));
            $this->taskBoardStatus = TaskboardColumn::all();
            $this->taskCategories = TaskCategory::all();
            $this->taskLabels = TaskLabelList::all();
            $this->milestones = ProjectMilestone::all();

            $taskBoardColumn = TaskboardColumn::waitingForApprovalColumn();

            $projectIds = Project::where('project_admin', user()->id)->pluck('id');

            if (!in_array('admin', user_roles()) && (in_array('employee', user_roles()) && $projectIds->isEmpty())) {
                $user = User::findOrFail(user()->id);
                $this->waitingApprovalCount = $user->tasks()->where('board_column_id', $taskBoardColumn->id)->count();
            }
            elseif (!in_array('admin', user_roles()) && (in_array('employee', user_roles()) && !$projectIds->isEmpty())) {
                $this->waitingApprovalCount = Task::whereIn('project_id', $projectIds)->where('board_column_id', $taskBoardColumn->id)->count();
            }
            else {
                $this->waitingApprovalCount = Task::where('board_column_id', $taskBoardColumn->id)->count();
            }
        }

        return $dataTable->render('tasks.waiting-approval', $this->data);
    }

    public function statusReason(Request $request)
    {

        $this->taskStatus = $request->taskStatus;
        $this->taskId = $request->taskId;
        $this->userId = $request->userId;

        return view('tasks.status_reason_modal', $this->data);
    }

    public function storeStatusReason(ActionTask $request)
    {

        $task = Task::with('project', 'users', 'boardColumn')->findOrFail($request->taskId);
        $taskBoardColumn = TaskboardColumn::where('slug', $request->taskStatus)->first();

        // Sortie de « En attente d'approbation » avec motif : c'est l'arbitrage
        // lui-meme, donc reserve a l'administrateur. Hors approbation, seul
        // l'assigne change le statut.
        $this->verifierChangementStatut($task, $taskBoardColumn);

        $task->board_column_id = $taskBoardColumn->id;
        $task->approval_send = 0;
        $task->save();

        $comment = new TaskComment();
        $comment->comment = $request->reason;
        $comment->task_id = $request->taskId;
        $comment->user_id = user()->id;
        $comment->save();

        return Reply::dataOnly(['status' => 'success']);
    }

    public function clientDetail(Request $request)
    {
        $project = Project::with('client')->findOrFail($request->id);

        if (!is_null($project->client)) {
            $data = '<h5 class= "mb-2 f-13"> ' . __('modules.projects.projectClient') . '</h5>';
            $data .= view('components.client', ['user' => $project->client]);
        /* @phpstan-ignore-line */
        }
        else {
            $data = '<p> ' . __('modules.projects.projectDoNotHaveClient') . '</p>';
        }

        return Reply::dataOnly(['data' => $data]);
    }

    public function updateTaskDuration(Request $request, $id)
    {
        $task = Task::with('users', 'project')->findOrFail($id);

        $editTaskPermission = user()->permission('edit_tasks');
        $taskUsers = $task->users->pluck('id')->toArray();

        abort_403(
            !(in_array('admin', user_roles())
            || est_arbitre()
            || ($task->project && $task->project->project_admin == user()->id)
            || ($editTaskPermission != 'none' && ($task->added_by == user()->id || in_array(user()->id, $taskUsers)))
            )
        );

        $task->start_date = Carbon::createFromFormat('d/m/Y', $request->start_date)->format('Y-m-d');
        $task->due_date = (!is_null($task->due_date)) ?Carbon::createFromFormat('d/m/Y', $request->end_date)->addDay()->format('Y-m-d') : null;
        $task->save();

        return Reply::success('messages.updateSuccess');
    }

    public function projectTasks($id)
    {
        if (request()->has('for_timelogs')) {
            $tasks = Task::projectLogTimeTasks($id);
            $options = BaseModel::options($tasks, null, 'heading');

            return Reply::dataOnly(['status' => 'success', 'data' => $options]);
        }

        $options = '<option value="">--</option>';

        $completedTaskColumn = TaskboardColumn::where('slug', '=', 'completed')->first();

        $tasks = Task::where('board_column_id', '<>', $completedTaskColumn->id)->whereNotNull('due_date');

        if ($id != 0 && $id != '') {
            $tasks = $tasks->where('project_id', $id);
        }

        $tasks = $completedTaskColumn ? $tasks->get() : [];

        foreach ($tasks as $item) {

            $options .= '<option  data-content="<div class=\'d-inline-block mr-1\'></div>  ' . $item->heading . ' ( Due date: ' . $item->due_date->format(company()->date_format) . ' ) " value="' . $item->id . '"> ' . $item->heading . '  ' . $item->due_date . ' </option>';
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $options]);
    }

    public function members($id)
    {
        $options = '<option value="">--</option>';

        if ($id != 0) {
            $members = Task::with('users', 'project')->findOrFail($id);

            // Seuls les utilisateurs ayant accès à la tâche peuvent voir ses membres
            $taskUsers = $members->users->pluck('id')->toArray();
            $canView = in_array('admin', user_roles())
                || est_arbitre()
                || $members->added_by == user()->id
                || in_array(user()->id, $taskUsers)
                || ($members->project && $members->project->project_admin == user()->id);

            abort_403(!$canView);

            foreach ($members->users as $item) {
                if ($item->status == 'active') {
                    $content = ($item->status == 'deactive') ? "<span class='badge badge-pill badge-danger border align-center ml-2 px-2'>Inactive</span>" : '';
                    $options .= '<option  data-content="<div class=\'d-inline-block mr-1\'><img class=\'taskEmployeeImg rounded-circle\' src=' . $item->image_url . ' ></div>  ' . $item->name . $content . '" value="' . $item->id . '"> ' . $item->name . ' </option>';
                }
            }
        }

        return Reply::dataOnly(['status' => 'success', 'data' => $options]);
    }

    public function reminder()
    {
        $taskID = request()->id;
        $task = Task::with('users', 'project')->findOrFail($taskID);

        // Vérification : seuls les utilisateurs liés à la tâche peuvent envoyer un rappel
        $taskUsers = $task->users->pluck('id')->toArray();
        abort_403(
            !(in_array('admin', user_roles())
            || est_arbitre()
            || $task->added_by == user()->id
            || in_array(user()->id, $taskUsers)
            || ($task->project && $task->project->project_admin == user()->id)
            )
        );

        // Send reminder notification to user
        event(new TaskReminderEvent($task));

        return Reply::success('messages.reminderMailSuccess');
    }

    /* Appele en AJAX par les formulaires de tache a chaque changement de date
       ou d'assigne. Le module Conges etant retire, leaves() renvoie toujours
       null : on ne parse plus les dates, dont la saisie libre (ex. "29-Aug-2003"
       hors format societe) faisait lever une InvalidFormatException (erreur 500). */
    public function checkLeaves()
    {
        return Reply::dataOnly(['data' => null]);
    }

    /* Module Conges retire du perimetre du cabinet (cf. audit nettoyage-modules) :
       la superposition "en conges" du calendrier de taches n'interroge plus le
       module Leave. La vue gere deja nativement l'absence de resultat. */
    public function leaves($userIds, $startDate, $dueDate)
    {
        return null;
    }

    public function sendNotifications($id)
    {
        $task = Task::with('users', 'project')->findOrFail($id);

        // Seuls les admins et les créateurs/assignés de la tâche peuvent re-déclencher des notifications
        $taskUsers = $task->users->pluck('id')->toArray();
        abort_403(
            !(in_array('admin', user_roles())
            || est_arbitre()
            || $task->added_by == user()->id
            || in_array(user()->id, $taskUsers)
            || ($task->project && $task->project->project_admin == user()->id)
            )
        );

        $dispatcher = new \App\Services\NotificationDispatcher();
        $channels = ['email', 'whatsapp'];

        try {
            $dispatcher->dispatchTaskDelegation($task, $channels);
            return Reply::success('Notifications envoyées avec succès.');
        } catch (\Throwable $e) {
            return Reply::error('Erreur lors de l\'envoi : ' . $e->getMessage());
        }
    }
}
