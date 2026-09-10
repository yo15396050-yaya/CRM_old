<?php

namespace App\Http\Controllers;

use App\Helper\Reply;
use App\Http\Requests\SearchRequest;
use App\Models\Contract;
use App\Models\Project;
use App\Models\Task;
use App\Models\User;
use App\Scopes\ActiveScope;
use Illuminate\Http\Request;

class SearchController extends AccountBaseController
{

    public function index()
    {
        return view('search.index', $this->data);
    }

    /**
     * @param SearchRequest $request
     * @return array|string[]|void
     */
    public function store(SearchRequest $request)
    {
        $module = $request->search_module;

        switch ($module) {
        case 'project':
            return Reply::redirect(route('projects.index') . '?search_keyword=' . $request->search_keyword);
        case 'ticket':
            return Reply::redirect(route('tickets.index') . '?search_keyword=' . $request->search_keyword);
        case 'invoice':
            return Reply::redirect(route('invoices.index') . '?search_keyword=' . $request->search_keyword);
        case 'notice':
            return Reply::redirect(route('notices.index') . '?search_keyword=' . $request->search_keyword);
        case 'task':
            return Reply::redirect(route('tasks.index') . '?search_keyword=' . $request->search_keyword);
        case 'creditNote':
            return Reply::redirect(route('creditnotes.index') . '?search_keyword=' . $request->search_keyword);
        case 'employee':
            return Reply::redirect(route('employees.index') . '?search_keyword=' . $request->search_keyword);
        case 'client':
            return Reply::redirect(route('clients.index') . '?search_keyword=' . $request->search_keyword);
        case 'estimate':
            return Reply::redirect(route('estimates.index') . '?search_keyword=' . $request->search_keyword);
        case 'lead':
            return Reply::redirect(route('deals.index') . '?search_keyword=' . $request->search_keyword);
        default:
            // Code...
            break;
        }
    }

    /**
     * Recherche transversale : une seule saisie interroge les taches,
     * les diligences et les clients.
     *
     * Les droits sont appliques ici, requete par requete, et non apres
     * coup sur les resultats : un enregistrement qu'un utilisateur n'a
     * pas le droit de voir ne doit pas remonter, meme en titre.
     */
    public function resultats(Request $request)
    {
        $mot = trim((string) $request->input('q', ''));

        $this->pageTitle = __('app.search');
        $this->mot = $mot;
        $this->taches = collect();
        $this->diligences = collect();
        $this->clients = collect();
        $this->contrats = collect();

        // Deux caracteres au moins : en dessous, toute la base remonterait.
        if (mb_strlen($mot) < 2) {
            $this->tropCourt = mb_strlen($mot) > 0;

            return view('search.resultats', $this->data);
        }

        $this->tropCourt = false;
        $estClient = in_array('client', user_roles());
        $moi = user()->id;

        /* ── Taches ──────────────────────────────────────────── */
        $taches = Task::with('boardColumn:id,column_name,label_color')
            ->select('id', 'heading', 'task_short_code', 'board_column_id', 'due_date', 'project_id')
            ->where('company_id', company()->id)
            ->where(fn ($q) => $q->where('heading', 'like', "%$mot%")
                ->orWhere('task_short_code', 'like', "%$mot%"));

        if ($estClient) {
            // Un client ne voit que les taches des diligences qui lui appartiennent.
            $taches->where('is_private', 0)
                ->whereHas('project', fn ($q) => $q->where('client_id', $moi));
        }
        elseif (user()->permission('view_tasks') !== 'all') {
            // Sinon : ses taches, celles qu'il a creees, ou celles de ses diligences.
            $taches->where(function ($q) use ($moi) {
                $q->whereHas('users', fn ($u) => $u->where('users.id', $moi))
                    ->orWhere('added_by', $moi)
                    ->orWhereHas('project', fn ($p) => $p->where('project_admin', $moi));
            });
        }

        $this->taches = $taches->latest('id')->limit(6)->get();

        /* ── Diligences ──────────────────────────────────────── */
        $diligences = Project::select('id', 'project_name', 'project_short_code', 'client_id')
            ->where('company_id', company()->id)
            ->where(fn ($q) => $q->where('project_name', 'like', "%$mot%")
                ->orWhere('project_short_code', 'like', "%$mot%"));

        if ($estClient) {
            $diligences->where('client_id', $moi);
        }
        elseif (user()->permission('view_projects') !== 'all') {
            $diligences->where(function ($q) use ($moi) {
                $q->whereHas('members', fn ($m) => $m->where('user_id', $moi))
                    ->orWhere('added_by', $moi)
                    ->orWhere('project_admin', $moi);
            });
        }

        $this->diligences = $diligences->latest('id')->limit(6)->get();

        /* ── Clients ─────────────────────────────────────────── */
        // Un client ne recherche pas dans le repertoire des clients.
        if (!$estClient && user()->permission('view_clients') !== 'none' && user()->permission('view_clients') !== false) {
            /* La recherche porte aussi sur la raison sociale : au cabinet,
               un client se designe par le nom de son entreprise bien plus
               souvent que par celui de son dirigeant. Ce nom vit dans
               client_details, pas dans users. */
            $this->clients = User::withoutGlobalScope(ActiveScope::class)
                ->select('users.id', 'users.name', 'users.email', 'users.image',
                    'client_details.company_name')
                ->join('role_user', 'role_user.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->leftJoin('client_details', 'client_details.user_id', '=', 'users.id')
                ->where('roles.name', 'client')
                ->where('users.company_id', company()->id)
                ->where(fn ($q) => $q->where('users.name', 'like', "%$mot%")
                    ->orWhere('users.email', 'like', "%$mot%")
                    ->orWhere('client_details.company_name', 'like', "%$mot%"))
                ->distinct()
                ->limit(6)
                ->get();
        }

        /* ── Contrats ────────────────────────────────────────── */
        $contrats = Contract::with('client:id,name')
            ->select('id', 'subject', 'contract_number', 'client_id', 'start_date', 'end_date')
            ->where('company_id', company()->id)
            ->where(fn ($q) => $q->where('subject', 'like', "%$mot%")
                ->orWhere('contract_number', 'like', "%$mot%"));

        $droitContrat = user()->permission('view_contract');

        if ($estClient) {
            $contrats->where('client_id', $moi);
        }
        elseif ($droitContrat === 'none' || $droitContrat === false) {
            // Aucun droit : la requete ne doit rien rapporter.
            $contrats->whereRaw('1 = 0');
        }
        elseif ($droitContrat === 'added') {
            $contrats->where('added_by', $moi);
        }
        elseif ($droitContrat === 'owned') {
            $contrats->where('client_id', $moi);
        }
        elseif ($droitContrat === 'both') {
            $contrats->where(fn ($q) => $q->where('added_by', $moi)->orWhere('client_id', $moi));
        }

        $this->contrats = $contrats->latest('id')->limit(6)->get();

        $this->total = $this->taches->count() + $this->diligences->count()
            + $this->clients->count() + $this->contrats->count();

        return view('search.resultats', $this->data);
    }

}
