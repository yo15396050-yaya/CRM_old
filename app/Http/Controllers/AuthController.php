<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Stripe\Stripe;
use App\Models\Deal;
use App\Models\Lead;
use App\Models\Role;
use App\Models\Task;
use App\Models\User;
use App\Helper\Files;
use App\Helper\Reply;
use App\Models\Order;
use App\Models\Ticket;
use GuzzleHttp\Client;
use App\Models\Company;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Project;
use App\Models\LeadNote;
use App\Models\Proposal;
use App\Models\TaskFile;
use App\Models\LeadSource;
use App\Models\TicketType;
use App\Models\CreditNotes;
use App\Models\LeadProduct;
use App\Models\TicketGroup;
use App\Models\TicketReply;
use App\Scopes\ActiveScope;
use App\Models\InvoiceItems;
use App\Models\LeadPipeline;
use App\Models\ProposalItem;
use App\Models\ProposalSign;
use App\Models\SituationFiscale;
use App\Models\SituationSociale;
use App\Scopes\CompanyScope;
use Illuminate\Http\Request;
use App\Models\ClientDetails;
use App\Models\ClientDocument;
use App\Models\GlobalSetting;
use App\Models\Notification;
use App\Models\ProjectCategory;
use App\Models\ProjectDepartment;
use App\Models\ProjectFile;
use App\Models\ProjectMember;
use App\Models\ProjectNote;
use App\Models\ProjectStatusSetting;
use App\Models\ProjectTemplate;
use App\Models\ProjectTimeLog;
use App\Models\PipelineStage;
use App\Models\LeadCustomForm;
use App\Models\TaskboardColumn;
use App\Models\TicketCustomForm;
use Froiden\RestAPI\ApiResponse;
use App\Traits\EmployeeDashboard;
use Illuminate\Support\Facades\DB;
use App\Models\ProjectTimeLogBreak;
use Illuminate\Support\Facades\App;
use Nwidart\Modules\Facades\Module;
use App\Traits\UniversalSearchTrait;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Artisan;
use App\Models\PaymentGatewayCredentials;
use App\Http\Requests\Lead\StorePublicLead;
use App\Http\Requests\ProposalAcceptRequest;
use App\Http\Requests\Stripe\StoreStripeDetail;
use App\Http\Requests\Tickets\StoreCustomTicket;
use App\Models\GanttLink;
use App\Models\CustomerRequest;
use App\Models\Service;
use App\Models\LanguageSetting;
use App\Models\ProjectMilestone;
use App\Events\NewUserEvent;
use App\Models\Contract;
use App\Models\KnowledgeBase;
use App\Models\Leave;
use App\Models\UserChat;
use App\Models\BankAccount;
use App\Models\Expense;
use App\Models\Attendance;
use Illuminate\Support\Facades\Session;

use Illuminate\Routing\Exceptions\InvalidSignatureException;
use App\Http\Requests\ClientDocs\CreateRequest;
use App\Http\Requests\ClientDocs\UpdateRequest;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    /**
     * Méthode utilitaire pour récupérer l'utilisateur via le token (Header ou URL)
     * Très important pour le bon fonctionnement sur cPanel/Apache
     */
    public function getApiUser()
    {
        try {
            $token = request()->query('token') ?? request()->bearerToken();

            if (!$token) {
                return null;
            }

            JWTAuth::setToken($token);
            $user = JWTAuth::authenticate();

            \Illuminate\Support\Facades\Log::info("Utilisateur authentifie via API : " . ($user ? $user->email : 'AUCUN'));

            return $user;
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Erreur getApiUser : " . $e->getMessage() . " - Trace: " . $e->getTraceAsString());
            return null;
        }
    }

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        try {
            if (!$token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Identifiants invalides'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Impossible de créer le token'], 500);
        }

        $user = auth()->user();
        
        // On s'assure de charger les rôles si nécessaire
        $user->load('roles');

        return response()->json([
            'token' => $token,
            'user' => $user,
            'userName' => $user->name
        ]);
    }

   public function dashboard(Request $request)
{
    // On efface tout et on met juste ça pour tester :
    return response()->json(['userName' => 'Connexion Réussie !']);
}

    public function userInfos(Request $request){
        try {
            $user = $this->getApiUser();
            $company = ClientDetails::where('user_id', $user->id)->first();
            return response()->json([
                'name' => $company->company_name,
                'email' => $user->email,
                'logo' => $company->company_logo,
                'regime' => $company->regime,
                'centreimp' => $company->imp_centre,
                'formjurid' => $company->formjurid
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    } 

    public function honoraires($id) {
        try {   
            // Sommes du des montant non payé au cabinet associés au client
            if (is_numeric($id)) {
                // Somme des montants non payés au cabinet associés au client
                $sumAmount = Invoice::where('client_id', $id)
                                    ->where('status', 'unpaid')
                                    ->sum('total');
            
                // Vérifiez si la somme est bien calculée
                if ($sumAmount === null) {
                    $sumAmount = 0; // Si aucune facture n'est trouvée, on initialise à 0
                }
            } else {
                // Gérer le cas où l'ID du client n'est pas valide
                throw new \InvalidArgumentException('ID de client invalide.');
            }
    
            // Retourner le compte des projets
            return response()->json([
                'sum' => number_format($sumAmount, 0, '.', ' '),
            ]);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner une réponse d'erreur
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }


    public function cptedilis($id) {
        try {   
            // Compter le nombre de projets associés au client
            $cpteproject = Project::where('client_id', $id)->where('status', 'En cours')->count();
    
            // Retourner le compte des projets
            return response()->json([
                'cpte' => $cpteproject,
            ]);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner une réponse d'erreur
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function cptedocs($id) {
        try {   
            // Compter le nombre de projets associés au client
            $cptedocs = ClientDocument::where('user_id', $id)->count();
    
            // Retourner le compte des projets
            return response()->json([
                'cpteDocs' => $cptedocs,
            ]);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner une réponse d'erreur
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function cptereqs($id) {
        try {   
            // Compter le nombre de requetes associés au client
            $cptereqs = CustomerRequest::where('client_id', $id)->where('status', 'pending')->count();
    
            // Retourner le compte des requetes
            return response()->json([
                'cpteReqs' => $cptereqs,
            ]);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner une réponse d'erreur
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function getAlldiligences(Request $request)
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $diligences = Project::select(
                    'projects.id',
                    'projects.project_name',
                    'projects.deadline',
                    'projects.status',  
                    'project_category.category_name'
                )
                ->leftJoin('project_category', 'project_category.id', '=', 'projects.category_id')
                ->where(function($q) use ($user) {
                    $q->where('projects.client_id', $user->id)
                      ->orWhereExists(function($query) use ($user) {
                          $query->select(DB::raw(1))
                                ->from('project_members')
                                ->whereColumn('project_members.project_id', 'projects.id')
                                ->where('project_members.user_id', $user->id);
                      });
                })
                ->orderBy('projects.created_at', 'desc')
                ->get()
                ->map(function ($project) {
                    $collaborators = DB::table('project_members')
                        ->join('users', 'users.id', '=', 'project_members.user_id')
                        ->where('project_members.project_id', $project->id)
                        ->pluck('users.name')
                        ->unique()
                        ->toArray();

                    return [
                        'id' => $project->id,
                        'project_name' => $project->project_name,
                        'deadline' => $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d/m/Y') : 'N/A',
                        'status' => $project->status,
                        'category_name' => $project->category_name ?? 'Général',
                        'collaborators' => $collaborators
                    ];
                });

            return response()->json($diligences);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function diligencesShow($id) {
        try {
            // Récupérer les projets associés au client
            $projects = Project::where('client_id', $id)->get();
    
            // Vérifier si des projets existent
            if ($projects->isEmpty()) {
                return response()->json(['message' => 'No projects found for this client.'], 404);
            }
    
            // Retourner les détails des projets
            return response()->json($projects);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner une réponse d'erreur
            return response()->json(['error' => 'An error occurred while fetching projects.'], 500);
        }
    }

    public function situationsFiscal($id)
    {
        try {
            $client = \App\Models\User::with('clientDetails')->find($id);
            $clientName = $client ? $client->name : 'N/A';
            $ncc = $client && $client->clientDetails ? $client->clientDetails->gst_number : 'N/A';
            $regime = $client && $client->clientDetails ? $client->clientDetails->regime : 'N/A';
            $activite = $client && $client->clientDetails ? $client->clientDetails->acti_prin : 'N/A';

            $situations = SituationFiscale::where('client_id', $id)
                ->orderBy('date_paiement', 'desc')
                ->get()
                ->map(function($sit) use ($clientName, $ncc, $regime, $activite) {
                    return [
                        'id' => (string)$sit->id,
                        'client' => $clientName,
                        'ncc' => $ncc,
                        'name' => $sit->type_impot,
                        'regime' => $sit->regime ?? $regime,
                        'activite' => $activite,
                        'date' => $sit->date_paiement ? \Carbon\Carbon::parse($sit->date_paiement)->format('d/m/Y') : 'N/A',
                        'lastDecl' => $sit->date_paiement ? \Carbon\Carbon::parse($sit->date_paiement)->format('d/m/Y') : 'N/A',
                        'montant' => number_format($sit->montant, 0, ',', ' ') . ' XOF',
                        'total' => number_format($sit->montant, 0, ',', ' ') . ' XOF',
                        'status' => $sit->status
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $situations
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function situationsSocial($id)
    {
        try {
            $client = \App\Models\User::with('clientDetails')->find($id);
            $clientName = $client ? $client->name : 'N/A';
            $ncc = $client && $client->clientDetails ? $client->clientDetails->gst_number : 'N/A';
            $regime = $client && $client->clientDetails ? $client->clientDetails->regime : 'N/A';
            $activite = $client && $client->clientDetails ? $client->clientDetails->acti_prin : 'N/A';

            $situations = SituationSociale::where('client_id', $id)
                ->orderBy('date_paiement', 'desc')
                ->get()
                ->map(function($sit) use ($clientName, $ncc, $regime, $activite) {
                    return [
                        'id' => (string)$sit->id,
                        'client' => $clientName,
                        'ncc' => $ncc,
                        'name' => $sit->type_sociale,
                        'regime' => $sit->regime ?? $regime,
                        'activite' => $activite,
                        'date' => $sit->date_paiement ? \Carbon\Carbon::parse($sit->date_paiement)->format('d/m/Y') : 'N/A',
                        'lastDecl' => $sit->date_paiement ? \Carbon\Carbon::parse($sit->date_paiement)->format('d/m/Y') : 'N/A',
                        'montant' => number_format($sit->montant, 0, ',', ' ') . ' XOF',
                        'total' => number_format($sit->montant, 0, ',', ' ') . ' XOF',
                        'status' => $sit->status
                    ];
                });

            return response()->json([
                'status' => 'success',
                'data' => $situations
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur situation sociale: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération de la situation sociale'], 500);
        }
    }
    public function getFiscalSituation()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);
            
            $situations = SituationFiscale::where('client_id', $user->id)
                ->orderBy('date_paiement', 'desc')
                ->get()
                ->groupBy('type_impot')
                ->map(function ($items) {
                    $latest = $items->first();
                    return [
                        'id' => (string)$latest->id,
                        'type' => $latest->type_impot,
                        'regime' => $latest->regime,
                        'montant' => (float)$latest->montant,
                        'periode' => $latest->periode,
                        'date_paiement' => $latest->date_paiement ? \Carbon\Carbon::parse($latest->date_paiement)->format('d/m/Y') : 'N/A',
                        'status' => $latest->status,
                        'document' => $latest->file
                    ];
                })
                ->values();

            return response()->json($situations);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération de la situation fiscale'], 500);
        }
    }

    public function getSocialSituation()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);
            
            $situations = SituationSociale::where('client_id', $user->id)
                ->orderBy('date_paiement', 'desc')
                ->get()
                ->groupBy('type_sociale')
                ->map(function ($items) {
                    $latest = $items->first();
                    return [
                        'id' => (string)$latest->id,
                        'type' => $latest->type_sociale,
                        'regime' => $latest->regime,
                        'montant' => (float)$latest->montant,
                        'periode' => $latest->periode,
                        'date_paiement' => $latest->date_paiement ? \Carbon\Carbon::parse($latest->date_paiement)->format('d/m/Y') : 'N/A',
                        'status' => $latest->status,
                        'document' => $latest->file
                    ];
                })
                ->values();

            return response()->json($situations);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Erreur lors de la récupération de la situation sociale'], 500);
        }
    }


    public function servicesSubscribe(Request $request, $serviceId){
        try {
            // Validation des données
            $validated = $request->validate([
                'service_id' => 'required',
                'name' => 'required|string',
                'type_request' => 'required|string',
                'request_text' => 'required|string',
            ]);
    
            $user = $this->getApiUser();
            
            // Vérifier si la diligence existe
            $service = Service::findOrFail($serviceId);
            
            // Créer la requête
            $customerRequest = CustomerRequest::create([
                'client_id' => $user->id,
                'service_id' => $validated['service_id'],
                'name' => $validated['name'],
                'type_request' => $validated['type_request'],
                'request_text' => $validated['request_text'],
                'status' => 'pending',
                'created_by' => $user->id,
            ]);
    
            return response()->json($customerRequest, 201);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Service non trouvée'], 404);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la soucription à ce service: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la soucription à ce service'], 500);
        }
    }

    public function indexRequest(Request $request)
    {
        try {  

            $user = $this->getApiUser();
            $requests = CustomerRequest::where('client_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get();
                
            return response()->json($requests);

        } catch (\Exception $e) {
            // Gérer les exceptions et retourner une réponse d'erreur
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function storeRequest(Request $request)
    {
        // Validation des données
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type_request' => 'required|string',
            'request_text' => 'required|string',
        ]);

        try {
            $user = $this->getApiUser();
            
            $customerRequest = CustomerRequest::create([
                'client_id' => $user->id, // Utiliser l'ID de l'utilisateur authentifié
                'name' => $validated['name'],
                'type_request' => $validated['type_request'],
                'request_text' => $validated['request_text'],
                'status' => 'pending',
                'created_by' => $user->id,
            ]);

            return response()->json($customerRequest, 201);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de la requête: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la création de la requête'], 500);
        }
    }

    public function showRequest($id)
    {
        try {
                $user = $this->getApiUser();
                $request = CustomerRequest::where('client_id', Auth::id())
                    ->findOrFail($id);
                    
                return response()->json($request);
            } catch (\Exception $e) {
                \Log::error('Erreur lors de l\'affichage de la requête: ' . $e->getMessage());
                return response()->json(['error' => 'Une erreur est survenue lors de l\'affichage de la requête'], 500);
            }
    }

    public function updateRequest(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'type_request' => 'required|string',
            'request_text' => 'required|string',
        ]);
    
        try {
            $user = $this->getApiUser();
            
            $customerRequest = CustomerRequest::where('id', $id)
                ->where('client_id', $user->id)
                ->firstOrFail();
    
            if ($customerRequest->status !== 'pending') {
                return response()->json(['error' => 'Seules les requêtes en attente peuvent être modifiées'], 403);
            }
    
            $customerRequest->update($validated);
    
            return response()->json($customerRequest);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Requête non trouvée'], 404);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la modification de la requête: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la modification de la requête'], 500);
        }
    }

    public function diligencesRequests(Request $request, $diligenceId)
    {
        try {
            // Validation des données
            $validated = $request->validate([
                'project_id' => 'required',
                'name' => 'required|string',
                'type_request' => 'required|string',
                'request_text' => 'required|string',
            ]);
    
            $user = $this->getApiUser();
            
            // Vérifier si la diligence existe
            $diligence = Project::findOrFail($diligenceId);
            
            // Créer la requête
            $customerRequest = CustomerRequest::create([
                'client_id' => $user->id,
                'project_id' => $validated['project_id'],
                'name' => $validated['name'],
                'type_request' => $validated['type_request'],
                'request_text' => $validated['request_text'],
                'status' => 'pending',
                'created_by' => $user->id,
            ]);
    
            return response()->json($customerRequest, 201);
            
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            return response()->json(['error' => 'Diligence non trouvée'], 404);
        } catch (ValidationException $e) {
            return response()->json(['error' => $e->errors()], 422);
        } catch (\Exception $e) {
            \Log::error('Erreur lors de la création de la requête: ' . $e->getMessage());
            return response()->json(['error' => 'Une erreur est survenue lors de la création de la requête'], 500);
        }
    }
    



     public function indexNotification()
    {
        try { 
            $user = $this->getApiUser();

            $notifications = Notification::where('notifiable_id', $user->id)
                ->where('notifiable_type', 'App\Models\User')
                ->where('type', 'App\Notifications\NewProject')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json([
                'notifications' => $notifications
            ]);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner une réponse d'erreur
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function unreadCount()
    {
        try { 
            $user = $this->getApiUser();

            $count = Notification::where('notifiable_id', $user->id)
                ->where('notifiable_type', 'App\Models\User')
                ->where('type', 'App\Notifications\NewProject')
                ->whereNull('read_at')
                ->count();

            return response()->json([
                'count' => $count
            ]);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner une réponse d'erreur
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function markAsRead($id)
    {
        $user = JWTAuth::parseToken()->authenticate();

        $notification = Notification::where('id', $id)->first();
        
        if ($notification->notifiable_id !== $user->id) {
            return response()->json(['message' => 'Non autorisé'], 403);
        }

        $notification->update(['read_at' => now()]);

        return response()->json(['message' => 'Notification marquée comme lue']);
    }

    public function markAllAsRead()
    {
        try { 
            $user = $this->getApiUser();

            Notification::where('notifiable_id', $user->id)
                ->where('notifiable_type', 'App\Models\User')
                ->where('type', 'App\Notifications\NewProject')
                ->whereNull('read_at')
                ->update(['read_at' => now()]);

            return response()->json(['message' => 'Toutes les notifications marquées comme lues']);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner une réponse d'erreur
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    // --- MÉTHODES API CENTRALISÉES ---

    /**
     * MODULE RH : Historique des présences
     */
    public function getAttendances()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $active = \App\Models\Attendance::where('user_id', $user->id)
                ->whereNull('clock_out_time')
                ->exists();

            $attendances = \App\Models\Attendance::where('user_id', $user->id)
                ->orderBy('clock_in_time', 'desc')
                ->limit(30)
                ->get()
                ->map(function ($att) {
                    $total = 'N/A';
                    if ($att->clock_out_time) {
                        $diff = $att->clock_in_time->diff($att->clock_out_time);
                        $total = $diff->format('%h h %i m');
                    }

                    return [
                        'id' => $att->id,
                        'date' => $att->clock_in_time->format('d/m/Y'),
                        'in' => $att->clock_in_time->format('H:i'),
                        'out' => $att->clock_out_time ? $att->clock_out_time->format('H:i') : '--:--',
                        'total' => $total,
                        'status' => $att->clock_out_time ? 'Terminé' : 'En cours'
                    ];
                });

            return response()->json([
                'active' => $active,
                'history' => $attendances
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE RH : Pointer (In/Out)
     */
    public function clockInOut(Request $request)
    {
        try {
            $user = $this->getApiUser();
            $now = now();
            
            $attendance = \App\Models\Attendance::where('user_id', $user->id)
                ->whereNull('clock_out_time')
                ->first();

            if ($attendance) {
                $attendance->update(['clock_out_time' => $now]);
                return response()->json(['message' => 'Pointage de sortie réussi', 'type' => 'out']);
            }

            \App\Models\Attendance::create([
                'user_id' => $user->id,
                'clock_in_time' => $now,
                'clock_in_ip' => $request->ip(),
                'working_from' => 'mobile'
            ]);

            return response()->json(['message' => 'Pointage d\'entrée réussi', 'type' => 'in']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE FINANCE : Liste des dépenses
     */
    public function getExpenses()
    {
        try {
            $user = $this->getApiUser();
            $expenses = \App\Models\Expense::where('user_id', $user->id)
                ->orderBy('purchase_date', 'desc')
                ->get()
                ->map(function ($exp) {
                    return [
                        'id' => $exp->id,
                        'item' => $exp->item_name,
                        'price' => $exp->price,
                        'status' => $exp->status,
                        'date' => $exp->purchase_date ? $exp->purchase_date->format('d/m/Y') : 'N/A'
                    ];
                });
            return response()->json($expenses);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE TRAVAIL : Liste des tâches
     */
    public function getEmployees()
    {
        try {
            // Tentative de récupération via une méthode personnalisée
            try {
                $employees = User::allEmployees();
            } catch (\Exception $e) {
                $employees = null;
            }
            
            if (!$employees) {
                $employees = User::with('employeeDetail', 'employeeDetail.department')->get();
            }

            $formatted = $employees->map(function($emp) {
                $role = 'Employé';
                if (isset($emp->roles) && count($emp->roles) > 0) {
                    $role = $emp->roles[0]->name;
                }

                $dept = 'N/A';
                if ($emp->employeeDetail && $emp->employeeDetail->department) {
                    $dept = $emp->employeeDetail->department->team_name;
                }

                return [
                    'id' => $emp->id,
                    'name' => $emp->name,
                    'role' => $role,
                    'dept' => $dept,
                    'avatar' => $emp->image_url,
                    'status' => 'En ligne'
                ];
            });

            return response()->json($formatted);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function storeEmployee(Request $request)
    {
        DB::beginTransaction();
        try {
            $authUser = $this->getApiUser();
            if (!$authUser) return response()->json(['error' => 'Unauthenticated'], 401);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email',
                'password' => 'required|string|min:8',
                'employee_id' => 'required|string|unique:employee_details,employee_id',
                'address' => 'nullable|string',
                'mobile' => 'nullable|string',
                'joining_date' => 'required|date',
                'gender' => 'required|in:male,female,others',
                'designation_id' => 'nullable|integer',
                'department_id' => 'nullable|integer',
                'marital_status' => 'nullable|string',
                'date_of_birth' => 'nullable|date',
                'parent_id' => 'nullable|integer', // Responsable
            ]);

            // Mapper les valeurs de marital_status vers l'enum MaritalStatus
            $maritalStatusMap = [
                'unmarried' => 'single',
                'single' => 'single',
                'married' => 'married',
                'widower' => 'widower',
                'widow' => 'widow',
                'separate' => 'separate',
                'divorced' => 'divorced',
                'engaged' => 'engaged',
            ];
            $maritalStatus = $maritalStatusMap[$validated['marital_status'] ?? 'single'] ?? 'single';

            $companyId = $authUser->company_id ?? 1;

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'password' => Hash::make($validated['password']),
                'mobile' => $validated['mobile'] ?? null,
                'gender' => $validated['gender'],
                'status' => 'active',
                'company_id' => $companyId,
            ]);

            \App\Models\EmployeeDetails::create([
                'user_id' => $user->id,
                'employee_id' => $validated['employee_id'],
                'address' => $validated['address'] ?? null,
                'joining_date' => $validated['joining_date'],
                'designation_id' => $validated['designation_id'] ?? null,
                'department_id' => $validated['department_id'] ?? null,
                'marital_status' => $maritalStatus,
                'date_of_birth' => $validated['date_of_birth'] ?? null,
                'reporting_to' => $validated['parent_id'] ?? null,
                'added_by' => $authUser->id,
                'company_id' => $companyId,
            ]);

            // Assigner le rôle 'employee' (indispensable pour que l'employé apparaisse dans les listes)
            $employeeRole = Role::where('name', 'employee')->first();
            if ($employeeRole) {
                $user->attachRole($employeeRole);
                $user->assignUserRolePermission($employeeRole->id);
            }

            DB::commit();
            return response()->json([
                'status' => 'success',
                'message' => 'Employé créé avec succès',
                'user' => $user
            ], 201);
        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur storeEmployee: ' . $e->getMessage() . ' - Trace: ' . $e->getTraceAsString());
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    /**
     * MODULE RH : Détail d'un employé par ID
     */
    public function getEmployeeDetail($id)
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $employee = User::withoutGlobalScope(\App\Scopes\ActiveScope::class)
                ->with(['employeeDetail.department', 'employeeDetail.designation', 'employeeDetail.reportingTo'])
                ->findOrFail($id);

            $detail = $employee->employeeDetail;

            // Compter les projets et tâches assignés
            $projectsCount = DB::table('project_members')->where('user_id', $id)->count();
            $tasksCount = DB::table('task_users')->where('user_id', $id)->count();

            return response()->json([
                'id' => $employee->id,
                'name' => $employee->name,
                'email' => $employee->email,
                'mobile' => $employee->mobile,
                'gender' => $employee->gender,
                'avatar' => $employee->image_url,
                'status' => $employee->status,
                'role' => $detail && $detail->designation ? $detail->designation->name : 'Employé',
                'dept' => $detail && $detail->department ? $detail->department->team_name : 'N/A',
                'joining_date' => $detail && $detail->joining_date ? $detail->joining_date->format('d/m/Y') : 'N/A',
                'address' => $detail ? $detail->address : null,
                'marital_status' => $detail ? ($detail->marital_status->value ?? $detail->marital_status ?? 'N/A') : 'N/A',
                'date_of_birth' => $detail && $detail->date_of_birth ? $detail->date_of_birth->format('d/m/Y') : 'N/A',
                'reporting_to' => $detail && $detail->reportingTo ? $detail->reportingTo->name : 'N/A',
                'employee_id' => $detail ? $detail->employee_id : 'N/A',
                'projects_count' => $projectsCount,
                'tasks_count' => $tasksCount,
            ]);
        } catch (\Exception $e) {
            \Log::error('Erreur getEmployeeDetail: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getFormData()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $departments = DB::table('teams')->where('company_id', $user->company_id)->select('id', 'team_name as name')->get();
            $designations = DB::table('designations')->where('company_id', $user->company_id)->select('id', 'name')->get();
            $employees = User::allEmployees(null, true, null, $user->company_id)->map(function($u) {

                return ['id' => $u->id, 'name' => $u->name];
            });

            return response()->json([
                'departments' => $departments,
                'designations' => $designations,
                'employees' => $employees
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public function getTasks()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $tasks = \App\Models\Task::where(function($q) use ($user) {
                    $q->whereHas('project', function($query) use ($user) {
                        $query->where('client_id', $user->id)
                              ->orWhereExists(function($sub) use ($user) {
                                  $sub->select(DB::raw(1))
                                      ->from('project_members')
                                      ->whereColumn('project_members.project_id', 'projects.id')
                                      ->where('project_members.user_id', $user->id);
                              });
                    })
                    ->orWhereHas('users', function($query) use ($user) {
                        $query->where('user_id', $user->id);
                    })
                    ->orWhere('added_by', $user->id);
                })
                ->with(['project'])
                ->orderBy('due_date', 'asc')
                ->get()
                ->map(function ($task) {
                    return [
                        'id' => $task->id,
                        'heading' => $task->heading,
                        'status' => $task->status,
                        'priority' => $task->priority ?? 'medium',
                        'due_date' => $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') : 'N/A',
                        'project_name' => $task->project ? $task->project->project_name : 'Général'
                    ];
                });

            return response()->json($tasks);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getTaskDetail($id)
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $task = \App\Models\Task::with(['project', 'users', 'createBy', 'boardColumn'])
                ->findOrFail($id);

            return response()->json([
                'id' => $task->id,
                'heading' => $task->heading,
                'description' => $task->description,
                'status' => $task->status,
                'priority' => $task->priority,
                'due_date' => $task->due_date ? \Carbon\Carbon::parse($task->due_date)->format('d/m/Y') : 'N/A',
                'project_name' => $task->project ? $task->project->project_name : 'Général',
                'created_by' => $task->createBy ? $task->createBy->name : 'N/A',
                'column' => $task->boardColumn ? $task->boardColumn->column_name : 'N/A',
                'assignees' => $task->users->map(function($u) {
                    return [
                        'id' => $u->id,
                        'name' => $u->name,
                        'image_url' => $u->image_url
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE TRAVAIL : Mise à jour d'une tâche (status, priority, etc.)
     */
    public function updateTask(Request $request, $id)
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $task = \App\Models\Task::findOrFail($id);

            // Mise à jour des champs envoyés
            if ($request->has('status')) {
                $task->status = $request->status;
            }
            if ($request->has('priority')) {
                $task->priority = $request->priority;
            }
            if ($request->has('heading')) {
                $task->heading = $request->heading;
            }

            $task->save();

            return response()->json([
                'message' => 'Tâche mise à jour avec succès',
                'task' => [
                    'id' => $task->id,
                    'heading' => $task->heading,
                    'status' => $task->status,
                    'priority' => $task->priority,
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getClients()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $clientsQuery = \App\Models\User::without('session')
                ->join('role_user', 'role_user.user_id', '=', 'users.id')
                ->join('roles', 'roles.id', '=', 'role_user.role_id')
                ->leftJoin('client_details', 'users.id', '=', 'client_details.user_id')
                ->select('users.id', 'users.name', 'users.email', 'client_details.company_name', 'users.mobile', 'users.country_id')
                ->where('roles.name', 'client')
                ->where('users.status', 'active');
                
            if ($user->company_id) {
                $clientsQuery->where('users.company_id', $user->company_id);
            }
            
            if ($user->hasRole('client')) {
                $clientsQuery->where('users.id', $user->id);
            }
            
            $viewClientPermission = $user->permission('view_clients');
            if ($viewClientPermission == 'none') {
                return response()->json([]);
            } elseif ($viewClientPermission == 'added') {
                $clientsQuery->where('client_details.added_by', $user->id);
            }

            $clients = $clientsQuery->orderBy('users.name', 'asc')->get()->map(function ($client) {
                return [
                    'id' => $client->id,
                    'name' => $client->name,
                    'company_name' => $client->company_name ?? 'Individuel',
                    'email' => $client->email,
                    'mobile' => $client->mobile,
                    'country' => 'N/A'
                ];
            });

            return response()->json($clients);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getProjectDetail($id)
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $project = Project::with(['client', 'members', 'category'])
                ->findOrFail($id);

            return response()->json([
                'id' => $project->id,
                'project_name' => $project->project_name,
                'project_summary' => $project->project_summary,
                'status' => $project->status,
                'deadline' => $project->deadline ? \Carbon\Carbon::parse($project->deadline)->format('d/m/Y') : 'N/A',
                'start_date' => $project->start_date ? \Carbon\Carbon::parse($project->start_date)->format('d/m/Y') : 'N/A',
                'category_name' => $project->category ? $project->category->category_name : 'Général',
                'client' => [
                    'name' => $project->client ? $project->client->name : 'N/A',
                    'company_name' => $project->client && $project->client->clientDetails ? $project->client->clientDetails->company_name : 'Individuel'
                ],
                'members' => $project->members->map(function($u) {
                    return [
                        'id' => $u->id,
                        'name' => $u->name
                    ];
                })
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE FINANCE : États Financiers (Recettes vs Dépenses par Projet)
     */
    public function getFinancialStates()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            // On récupère les projets (diligences) du client
            $projects = Project::where('client_id', $user->id)
                ->with(['expenses', 'payments'])
                ->get();

            $financialData = $projects->map(function ($project) {
                // Somme des dépenses liées au projet
                $totalExpenses = $project->expenses->sum('amount');
                
                // Somme des paiements reçus pour ce projet
                $totalRecettes = $project->payments->sum('amount');
                
                $solde = $totalRecettes - $totalExpenses;
                
                // Détermination du statut pour l'affichage mobile
                $status = 'En cours';
                if ($project->status == 'finished' || $project->status == 'completed') {
                    $status = ($solde >= 0) ? 'Rentable' : 'Déficitaire';
                } elseif ($solde < 0 && $totalExpenses > ($project->project_budget ?? 0)) {
                    $status = 'Déficitaire';
                }

                return [
                    'id' => (string)$project->id,
                    'diligence' => $project->project_name,
                    'client' => auth()->user()->name,
                    'budget' => (float)$project->project_budget ?? 0,
                    'depenses' => (float)$totalExpenses,
                    'recettes' => (float)$totalRecettes,
                    'solde' => (float)$solde,
                    'status' => $status,
                    'progression' => ($project->completion_percent ?? 0) / 100,
                ];
            });

            // Calcul des totaux globaux pour les widgets du mobile
            $summary = [
                'totalBudget' => $financialData->sum('budget'),
                'totalDepenses' => $financialData->sum('depenses'),
                'totalRecettes' => $financialData->sum('recettes'),
                'totalSolde' => $financialData->sum('solde'),
            ];

            return response()->json([
                'data' => $financialData,
                'summary' => $summary
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur API Financial States: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur: ' . $e->getMessage()], 500);
        }
    }

    /**
     * MODULE FINANCE : Trésorerie (Comptes Bancaires & Caisses)
     */
    public function getTreasury()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            // Récupérer tous les comptes bancaires de l'entreprise
            $accounts = BankAccount::where('company_id', $user->company_id)
                ->where('status', 1) // Uniquement les comptes actifs
                ->with('currency')
                ->get();

            $treasuryData = $accounts->map(function ($acc) {
                // Déterminer le type de paiement pour l'icône mobile
                $paymentType = 'Banque';
                if ($acc->type == 'cash') $paymentType = 'Espèces';
                
                // On peut aussi détecter si c'est du mobile money via le nom (ex: Orange Money, Wave)
                if (stripos($acc->bank_name, 'Orange') !== false || stripos($acc->bank_name, 'Wave') !== false || stripos($acc->bank_name, 'MTN') !== false) {
                    $paymentType = 'Mobile';
                }

                return [
                    'id' => (string)$acc->id,
                    'bankName' => $acc->bank_name ?? 'N/A',
                    'accountName' => $acc->account_name,
                    'accountType' => $acc->account_type ?? ($acc->type == 'cash' ? 'Caisse' : 'Courant'),
                    'paymentType' => $paymentType,
                    'currency' => $acc->currency->currency_code ?? 'XOF',
                    'balance' => (float)$acc->bank_balance,
                    'status' => 'Actif',
                ];
            });

            return response()->json([
                'data' => $treasuryData,
                'totalBalance' => $treasuryData->sum('balance')
            ]);

        } catch (\Exception $e) {
            \Log::error('Erreur API Trésorerie: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération de la trésorerie'], 500);
        }
    }

    /**
     * MODULE MESSAGERIE : Liste des conversations
     */
    public function getConversations()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $userId = $user->id;
            
            // On utilise la logique existante du modèle UserChat
            $conversations = UserChat::with('fromUser', 'toUser')
                ->where(function ($query) use ($userId) {
                    $query->where('from', $userId)->orWhere('to', $userId);
                })
                ->whereIn('id', function ($query) {
                    $query->from('users_chat')
                        ->selectRaw('MAX(id)')
                        ->groupByRaw('LEAST(user_one, user_id), GREATEST(user_one, user_id)');
                })
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($chat) use ($userId) {
                    $otherUser = ($chat->from == $userId) ? $chat->toUser : $chat->fromUser;
                    return [
                        'id' => (string)$otherUser->id,
                        'name' => $otherUser->name,
                        'lastMessage' => $chat->message,
                        'time' => $chat->created_at->diffForHumans(),
                        'unread' => 0, // Idéalement, compter les messages non vus
                        'avatar' => $otherUser->image_url,
                        'online' => false, // Géré par une autre logique si nécessaire
                    ];
                });

            return response()->json($conversations);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE MESSAGERIE : Messages d'une conversation
     */
    public function getMessages($id)
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $messages = UserChat::chatDetail($id, $user->id);

            $formattedMessages = $messages->map(function($msg) use ($user) {
                return [
                    'id' => (string)$msg->id,
                    'text' => $msg->message,
                    'senderId' => (string)$msg->from,
                    'time' => $msg->created_at->format('H:i'),
                    'isMine' => $msg->from == $user->id
                ];
            });

            return response()->json($formattedMessages);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE MESSAGERIE : Liste des contacts pour nouvelle discussion
     */
    public function getChatContacts()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $employees = User::allEmployees()->map(function($u) {
                return ['id' => (string)$u->id, 'name' => $u->name, 'avatar' => $u->image_url];
            });

            $clients = User::allClients()->map(function($u) {
                return ['id' => (string)$u->id, 'name' => $u->name, 'avatar' => $u->image_url];
            });

            return response()->json([
                'employee' => $employees,
                'client' => $clients
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE MESSAGERIE : Envoyer un message
     */
    public function sendMessage(Request $request)
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $validated = $request->validate([
                'to' => 'required|exists:users,id',
                'message' => 'required|string'
            ]);

            $toUserId = $validated['to'];
            
            // Dans votre modèle UserChat, user_one est toujours le plus petit ID
            // et user_id est le plus grand ID pour grouper les conversations.
            $userOne = ($user->id < $toUserId) ? $user->id : $toUserId;
            $userId  = ($user->id > $toUserId) ? $user->id : $toUserId;

            $chat = UserChat::create([
                'user_one' => $userOne,
                'user_id' => $userId,
                'from' => $user->id,
                'to' => $toUserId,
                'message' => $validated['message'],
                'company_id' => $user->company_id,
                'message_seen' => 'no'
            ]);

            return response()->json([
                'id' => (string)$chat->id,
                'text' => $chat->message,
                'senderId' => (string)$chat->from,
                'time' => $chat->created_at->format('H:i'),
                'isMine' => true
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Erreur envoi message API: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE CLIENTS : Liste des clients
     */


    /**
     * MODULE CLIENTS : Détails d'un client
     */
    public function getClientDetails($id)
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $client = User::with(['clientDetails', 'country'])->findOrFail($id);

            // On pourrait ajouter ici ses projets récents, factures, etc.
            $details = [
                'id' => (string)$client->id,
                'name' => $client->name,
                'company_name' => $client->clientDetails->company_name ?? 'N/A',
                'email' => $client->email,
                'mobile' => $client->mobile,
                'office_phone' => $client->clientDetails->office_phone ?? 'N/A',
                'website' => $client->clientDetails->website ?? 'N/A',
                'address' => $client->clientDetails->address ?? 'N/A',
                'city' => $client->clientDetails->city ?? 'N/A',
                'state' => $client->clientDetails->state ?? 'N/A',
                'postal_code' => $client->clientDetails->postal_code ?? 'N/A',
                'country' => $client->country->nicename ?? 'N/A',
                'avatar' => $client->image_url,
                'shipping_address' => $client->clientDetails->shipping_address ?? 'N/A',
            ];

            return response()->json($details);

        } catch (\Exception $e) {
            return response()->json(['error' => 'Client non trouvé'], 404);
        }
    }

    public function storeClient(Request $request)
    {
        DB::beginTransaction();
        try {
            $user_auth = $this->getApiUser();
            
            // Validation de base
            $request->validate([
                'companyName' => 'required',
                'leaderName' => 'required',
                'email' => 'required|email|unique:users,email',
                'mobile' => 'required',
                'password' => 'required|min:8',
            ]);

            // 1. Création de l'utilisateur (Auth)
            $user = new User();
            $user->name = $request->leaderName;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->mobile = $request->mobile;
            $user->gender = strtolower($request->gender ?? 'male');
            $user->status = 'active';
            $user->login = 'enable';
            $user->company_id = $user_auth->company_id ?? 1; // On récupère la company de celui qui crée
            
            // Gestion de l'avatar
            if ($request->hasFile('avatar')) {
                $user->image = Files::upload($request->avatar, 'avatar');
            }
            
            $user->save();

            // Attacher le rôle client
            $role = Role::where('name', 'client')->first();
            if ($role) {
                $user->attachRole($role->id);
            }

            // 2. Détails du client
            $client = new ClientDetails();
            $client->user_id = $user->id;
            $client->company_id = $user->company_id;
            $client->company_name = $request->companyName;
            $client->company_name_com = $request->tradeName; // Nom commercial
            $client->address = $request->address;
            $client->website = $request->website;
            $client->office = $request->officePhone; // Correction: office_phone -> office
            $client->city = $request->city;
            $client->postal_code = $request->postalCode;
            $client->shipping_address = $request->shippingAddress;
            $client->skype = $request->whatsapp;

            $client->note = $request->note;
            
            // Nouveaux champs mappés selon ClientDetails.php
            $client->formjurid = $request->legalForm;
            $client->regime = $request->taxRegime;
            $client->acti_prin = $request->mainActivities;
            $client->section = $request->section;
            $client->parcelle = $request->parcelle;
            $client->imp_centre = $request->taxCenter;
            $client->codeacti = $request->activityCode;
            $client->montcapit = $request->capital;
            $client->numrccm = $request->rccm;
            $client->gst_number = $request->taxAccount;
            
            $client->added_by = $user_auth->id ?? null;

            // Gestion du logo
            if ($request->hasFile('logo')) {
                $client->company_logo = Files::upload($request->logo, 'client-logo');
            }

            $client->save();

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Client créé avec succès',
                'user' => $user
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur storeClient: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }

    public function destroyClient($id)
    {
        DB::beginTransaction();
        try {
            $user_auth = $this->getApiUser();
            if (!$user_auth) return response()->json(['error' => 'Unauthenticated'], 401);

            $client = User::findOrFail($id);
            
            // On s'assure qu'on ne supprime pas un admin ou soi-même par erreur
            if ($client->hasRole('admin')) {
                return response()->json(['error' => 'Impossible de supprimer un administrateur'], 403);
            }

            // Suppression des détails d'abord (clé étrangère potentielle)
            ClientDetails::where('user_id', $id)->delete();
            
            // Suppression de l'utilisateur
            $client->delete();

            DB::commit();
            return response()->json(['message' => 'Client supprimé avec succès']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function updateClient(Request $request, $id)
    {
        DB::beginTransaction();
        try {
            $user = User::findOrFail($id);
            $client = ClientDetails::where('user_id', $id)->firstOrFail();

            // Validation (email unique sauf pour cet utilisateur)
            $request->validate([
                'companyName' => 'required',
                'leaderName' => 'required',
                'email' => 'required|email|unique:users,email,' . $id,
                'mobile' => 'required',
            ]);

            // 1. Mise à jour User
            $user->name = $request->leaderName;
            $user->email = $request->email;
            if ($request->password) {
                $user->password = Hash::make($request->password);
            }
            $user->mobile = $request->mobile;
            $user->gender = strtolower($request->gender ?? $user->gender);
            
            if ($request->hasFile('avatar')) {
                $user->image = Files::upload($request->avatar, 'avatar');
            }
            $user->save();

            // 2. Mise à jour ClientDetails
            $client->company_name = $request->companyName;
            $client->company_name_com = $request->tradeName;
            $client->address = $request->address;
            $client->website = $request->website;
            $client->office = $request->officePhone;
            $client->city = $request->city;
            $client->postal_code = $request->postalCode;
            $client->shipping_address = $request->shippingAddress;
            $client->skype = $request->whatsapp;
            $client->note = $request->note;
            
            $client->formjurid = $request->legalForm;
            $client->regime = $request->taxRegime;
            $client->acti_prin = $request->mainActivities;
            $client->section = $request->section;
            $client->parcelle = $request->parcelle;
            $client->imp_centre = $request->taxCenter;
            $client->codeacti = $request->activityCode;
            $client->montcapit = $request->capital;
            $client->numrccm = $request->rccm;
            $client->gst_number = $request->taxAccount;

            if ($request->hasFile('logo')) {
                $client->company_logo = Files::upload($request->logo, 'client-logo');
            }

            $client->save();

            DB::commit();
            return response()->json(['message' => 'Client mis à jour avec succès']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 422);
        }
    }






    /**
     * MODULE FINANCE : Produits & Services
     */
    public function services()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $products = Product::where('company_id', $user->company_id)
                ->with('category')
                ->get()
                ->map(function($product) {
                    return [
                        'id' => (string)$product->id,
                        'name' => $product->name,
                        'category' => $product->category->category_name ?? 'Général',
                        'price' => number_format($product->price, 0, ',', ' ') . ' FCFA',
                        'image' => $product->image_url,
                        'description' => $product->description
                    ];
                });

            return response()->json($products);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function getServiceDetail($id)
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $product = Product::where('company_id', $user->company_id)
                ->with(['category', 'subCategory', 'unit', 'tax'])
                ->findOrFail($id);

            return response()->json([
                'id' => (string)$product->id,
                'name' => $product->name,
                'price' => number_format($product->price, 0, ',', ' ') . ' FCFA',
                'tax' => $product->tax ? ($product->tax->tax_name . ' (' . $product->tax->rate_percent . '%)') : '--',
                'unitType' => $product->unit ? $product->unit->unit_type : '-',
                'hsnSac' => $product->hsn_sac_code ?? '--',
                'sku' => $product->sku ?? '--',
                'category' => $product->category->category_name ?? '--',
                'subCategory' => $product->subCategory->category_name ?? '--',
                'canPurchase' => $product->allow_purchase == 1,
                'isDownloadable' => $product->downloadable == 1,
                'description' => $product->description ?? '--',
                'image' => $product->image_url ?: null
            ]);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE RH : Permissions (Demandes de congés)
     */
    public function getPermissions()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $leaves = Leave::with('type')
                ->where('user_id', $user->id)
                ->orderBy('leave_date', 'desc')
                ->get()
                ->map(function($leave) {
                    return [
                        'id' => (string)$leave->id,
                        'type' => $leave->type->type_name,
                        'date' => $leave->leave_date->format('d/m/Y'),
                        'duration' => $leave->duration,
                        'reason' => $leave->reason,
                        'status' => $leave->status,
                        'color' => $leave->status == 'approved' ? '#2ecc71' : ($leave->status == 'pending' ? '#f39c12' : '#e74c3c')
                    ];
                });

            return response()->json($leaves);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE RH : Soumettre une demande de permission (conges)
     */
    public function storePermission(Request $request)
    {
        DB::beginTransaction();
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $validated = $request->validate([
                'type' => 'required|string',
                'start_date' => 'required|string',
                'end_date' => 'required|string',
                'reason' => 'nullable|string',
            ]);

            // Parse dates
            try {
                if (str_contains($validated['start_date'], '/')) {
                    $startDate = Carbon::createFromFormat('d/m/Y', $validated['start_date'])->startOfDay();
                } else {
                    $startDate = Carbon::parse($validated['start_date'])->startOfDay();
                }
            } catch (\Exception $e) {
                return response()->json(['error' => 'Format de date de début invalide (attendu JJ/MM/AAAA)'], 422);
            }

            try {
                if (str_contains($validated['end_date'], '/')) {
                    $endDate = Carbon::createFromFormat('d/m/Y', $validated['end_date'])->startOfDay();
                } else {
                    $endDate = Carbon::parse($validated['end_date'])->startOfDay();
                }
            } catch (\Exception $e) {
                return response()->json(['error' => 'Format de date de fin invalide (attendu JJ/MM/AAAA)'], 422);
            }

            if ($startDate->gt($endDate)) {
                return response()->json(['error' => 'La date de début doit être antérieure à la date de fin'], 422);
            }

            // Trouver un type de congé correspondant
            $leaveType = \App\Models\LeaveType::where('type_name', 'like', '%' . $validated['type'] . '%')->first();
            if (!$leaveType) {
                $leaveType = \App\Models\LeaveType::first();
            }

            if (!$leaveType) {
                return response()->json(['error' => 'Aucun type de congé configuré dans le système.'], 422);
            }

            $uniqueId = \Illuminate\Support\Str::random(16);
            $multipleDates = \Carbon\CarbonPeriod::create($startDate, $endDate);
            $duration = $startDate->eq($endDate) ? 'full day' : 'multiple';

            foreach ($multipleDates as $date) {
                $leave = new Leave();
                $leave->user_id = $user->id;
                $leave->unique_id = $uniqueId;
                $leave->leave_type_id = $leaveType->id;
                $leave->duration = $duration;
                $leave->leave_date = $date->format('Y-m-d');
                $leave->reason = $validated['reason'] ?? '';
                $leave->status = 'pending';
                $leave->added_by = $user->id;
                $leave->save();
            }

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Demande de permission soumise avec succès.'
            ], 201);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Erreur storePermission: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE TRAVAIL : Contrats
     */
    public function getContracts()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            // Pour un client, on filtre par client_id. Pour un employé, on voit tout l'entreprise ou rien selon les droits (ici on simplifie).
            $query = Contract::with(['contractType', 'client', 'currency'])
                ->where('company_id', $user->company_id);
            
            if ($user->hasRole('client')) {
                $query->where('client_id', $user->id);
            }

            $contracts = $query->orderBy('start_date', 'desc')
                ->get()
                ->map(function($contract) {
                    return [
                        'id' => (string)$contract->id,
                        'subject' => $contract->subject,
                        'amount' => number_format($contract->amount, 0, ',', ' ') . ' ' . ($contract->currency->currency_code ?? 'XOF'),
                        'start_date' => $contract->start_date->format('d/m/Y'),
                        'end_date' => $contract->end_date ? $contract->end_date->format('d/m/Y') : 'Indéfini',
                        'type' => $contract->contractType->name ?? 'N/A',
                        'status' => $contract->end_date && $contract->end_date->isPast() ? 'Expiré' : 'Actif'
                    ];
                });

            return response()->json($contracts);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE TRAVAIL : Base de Connaissances
     */
    public function getKnowledgeBase()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $articles = KnowledgeBase::with('knowledgebasecategory')
                ->where('company_id', $user->company_id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function($article) {
                    return [
                        'id' => (string)$article->id,
                        'title' => $article->heading,
                        'category' => $article->knowledgebasecategory->name ?? 'Général',
                        'content' => strip_tags($article->description),
                        'date' => $article->created_at->format('d/m/Y')
                    ];
                });

            return response()->json($articles);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE FINANCE : Factures
     */
    public function getInvoices()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $query = Invoice::where('company_id', $user->company_id);
            if ($user->hasRole('client')) {
                $query->where('client_id', $user->id);
            }

            $invoices = $query->orderBy('issue_date', 'desc')
                ->get()
                ->map(function($inv) {
                    return [
                        'id' => $inv->id,
                        'invoiceNumber' => $inv->invoice_number,
                        'client' => $inv->client->name ?? 'N/A',
                        'amount' => number_format($inv->total, 0, ',', ' ') . ' FCFA',
                        'date' => $inv->issue_date->format('d/m/Y'),
                        'status' => $inv->status,
                        'statusColor' => $inv->status == 'paid' ? '#2ecc71' : '#f1c40f'
                    ];
                });

            return response()->json($invoices);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE DOCUMENTS : Liste des documents
     */
    public function documents()
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $documents = \App\Models\EmployeeDocument::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($doc) {
                    $sizeFormatted = 'Inconnue';
                    if ($doc->size) {
                        $bytes = intval($doc->size);
                        if ($bytes >= 1048576) {
                            $sizeFormatted = number_format($bytes / 1048576, 2) . ' Mo';
                        } elseif ($bytes >= 1024) {
                            $sizeFormatted = number_format($bytes / 1024, 2) . ' Ko';
                        } else {
                            $sizeFormatted = $bytes . ' o';
                        }
                    }

                    return [
                        'id' => $doc->id,
                        'name' => $doc->name ?: $doc->filename,
                        'filename' => $doc->filename,
                        'size' => $sizeFormatted,
                        'created_at' => $doc->created_at ? $doc->created_at->format('d/m/Y') : 'Récemment',
                        'doc_url' => $doc->doc_url
                    ];
                });

            return response()->json($documents);
        } catch (\Exception $e) {
            \Log::error('Erreur getDocuments API: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE DOCUMENTS : Téléchargement direct
     */
    public function documentsDownload($id)
    {
        try {
            $user = $this->getApiUser() ?: auth()->user();
            
            if (!$user && request()->has('token')) {
                try {
                    $user = auth('api')->setToken(request()->query('token'))->authenticate();
                } catch (\Exception $e) {}
            }

            $file = \App\Models\EmployeeDocument::findOrFail($id);

            if ($user && $file->user_id != $user->id && !$user->hasRole('admin')) {
                return response()->json(['error' => 'Unauthorized'], 403);
            }

            $path = storage_path('app/' . \App\Models\EmployeeDocument::FILE_PATH . '/' . $file->user_id . '/' . $file->hashname);
            
            if (!file_exists($path)) {
                $path = public_path('user-uploads/' . \App\Models\EmployeeDocument::FILE_PATH . '/' . $file->user_id . '/' . $file->hashname);
                if (!file_exists($path)) {
                    return response()->json(['message' => 'Fichier introuvable sur le serveur.'], 404);
                }
            }

            return response()->download($path, $file->filename);
        } catch (\Exception $e) {
            \Log::error('Erreur documentsDownload: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * Récupérer tous les utilisateurs de l'application
     */
    public function getAllUsers(Request $request)
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            // Récupérer tous les utilisateurs actifs avec leurs données
            $users = User::select('id', 'name', 'email', 'mobile', 'image', 'status')
                        ->where('status', 'active')
                        ->orderBy('name', 'asc')
                        ->get()
                        ->map(function($u) {
                            return [
                                'id' => (string)$u->id,
                                'name' => $u->name,
                                'email' => $u->email,
                                'phone' => $u->mobile ?? '',
                                'image_url' => $u->image_url,
                                'status' => $u->status
                            ];
                        });

            return response()->json($users);
        } catch (\Exception $e) {
            \Log::error('Erreur getAllUsers: ' . $e->getMessage());
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'new_password' => 'required|min:6|confirmed',
        ]);

        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            if (!\Hash::check($request->current_password, $user->password)) {
                return response()->json(['error' => 'L\'ancien mot de passe est incorrect.'], 422);
            }

            $user->password = \Hash::make($request->new_password);
            $user->save();

            return response()->json(['message' => 'Mot de passe mis à jour avec succès !']);

        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE NOTES : Liste des notes et dossiers
     */
    public function getNotes(Request $request)
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $parentId = $request->query('parent_id');

            $notes = \App\Models\Note::with('children')
                ->where('user_id', $user->id)
                ->where(function($q) use ($parentId) {
                    if ($parentId) {
                        $q->where('parent_id', $parentId);
                    } else {
                        $q->whereNull('parent_id');
                    }
                })
                ->orderBy('updated_at', 'desc')
                ->get();

            return response()->json($notes);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE NOTES : Détail d'une note avec ses blocs
     */
    public function getNoteDetails($id)
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $note = \App\Models\Note::with(['blocks' => function($q) {
                $q->orderBy('order');
            }])
            ->where('user_id', $user->id)
            ->findOrFail($id);

            return response()->json($note);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE NOTES : Création d'une note ou dossier
     */
    public function createNote(Request $request)
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $isFolder = $request->is_folder ?? false;

            $note = \App\Models\Note::create([
                'title' => $request->title ?? '',
                'icon' => $request->icon ?? ($isFolder ? 'Folder' : 'FileText'),
                'user_id' => $user->id,
                'parent_id' => $request->parent_id,
                'is_folder' => $isFolder,
                'is_private' => $request->is_private ?? false,
            ]);

            if (!$isFolder) {
                \App\Models\NoteBlock::create([
                    'note_id' => $note->id,
                    'type' => 'text',
                    'content' => '',
                    'order' => 0
                ]);
            }

            return response()->json($note);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE NOTES : Mise à jour des métadonnées (titre, icône, parent_id, etc.)
     */
    public function updateNote(Request $request, $id)
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $note = \App\Models\Note::where('user_id', $user->id)->findOrFail($id);
            $note->update($request->only(['title', 'icon', 'is_private', 'parent_id']));

            return response()->json(['status' => 'success', 'note' => $note]);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE NOTES : Synchronisation complète des blocs (auto-save)
     */
    public function syncNoteBlocks(Request $request, $id)
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $note = \App\Models\Note::where('user_id', $user->id)->findOrFail($id);

            \DB::transaction(function () use ($note, $request) {
                $incomingBlockIds = collect($request->blocks)->pluck('id')->filter();
                $note->blocks()->whereNotIn('id', $incomingBlockIds)->delete();

                foreach ($request->blocks as $index => $blockData) {
                    \App\Models\NoteBlock::updateOrCreate(
                        ['id' => $blockData['id'] ?? null, 'note_id' => $note->id],
                        [
                            'type' => $blockData['type'],
                            'content' => $blockData['content'] ?? '',
                            'data' => $blockData['data'] ?? null,
                            'reminder_at' => $blockData['reminder_at'] ?? null,
                            'order' => $index
                        ]
                    );
                }
            });

            $note->touch();
            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    /**
     * MODULE NOTES : Suppression d'une note ou dossier
     */
    public function deleteNote($id)
    {
        try {
            $user = $this->getApiUser();
            if (!$user) return response()->json(['error' => 'Unauthenticated'], 401);

            $note = \App\Models\Note::where('user_id', $user->id)->findOrFail($id);
            $note->delete();

            return response()->json(['status' => 'success']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}



