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
use Illuminate\Support\Facades\Session;
use Illuminate\Routing\Exceptions\InvalidSignatureException;
use App\Http\Requests\ClientDocs\CreateRequest;
use App\Http\Requests\ClientDocs\UpdateRequest;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('username', 'password');

        try {
            if (! $token = JWTAuth::attempt($credentials)) {
                return response()->json(['error' => 'Unauthorized'], 401);
            }
        } catch (JWTException $e) {
            return response()->json(['error' => 'Could not create token'], 500);
        }

        return response()->json(compact('token'));
    }

    public function dashboard(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            $company = ClientDetails::where('user_id', $user->id)->first();
            return response()->json([
                'name' => $company->company_name,
                'email' => $user->email,
                'id' => $user->id,
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
        }
    }

    public function userInfos(Request $request){
        try {
            $user = JWTAuth::parseToken()->authenticate();
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
                throw new InvalidArgumentException('ID de client invalide.');
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
            $user = JWTAuth::parseToken()->authenticate();
            
            // D'abord, récupérons les projets uniques
            $diligences = Project::select(
                    'projects.id',
                    'projects.project_name',
                    'projects.deadline',
                    'projects.status',  
                    'project_category.category_name',
                    'projects.client_id'
                )
                ->leftJoin('project_category', 'project_category.id', '=', 'projects.category_id')
                ->where('projects.client_id', $user->id)
                ->get();

            // Ensuite, pour chaque projet, récupérons les collaborateurs
            $formattedDiligences = $diligences->map(function ($project) {
                // Récupérer tous les collaborateurs pour ce projet
                $collaborators = DB::table('project_members')
                    ->join('users', 'users.id', '=', 'project_members.user_id')
                    ->where('project_members.project_id', $project->id)
                    ->pluck('users.name')
                    ->unique()
                    ->implode(', ');

                return [
                    'id' => $project->id,
                    'project_name' => $project->project_name,
                    'deadline' => \Carbon\Carbon::parse($project->deadline)->locale('fr')->isoFormat('DD MMMM YYYY'),
                    'status' => $project->status,
                    'category_name' => $project->category_name,
                    'collaborators' => $collaborators ?: 'Aucun collaborateur'
                ];
            });

            // Pour debug
            \Log::info('Diligences formatées:', ['data' => $formattedDiligences]);
            
            return response()->json($formattedDiligences);
        } catch (\Exception $e) {
            \Log::error('Erreur dans diligences: ' . $e->getMessage());
            return response()->json([
                'error' => 'Une erreur s\'est produite lors de la récupération des diligences.',
                'details' => $e->getMessage()
            ], 500);
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
            //$user = JWTAuth::parseToken()->authenticate();
            
            $situations = SituationFiscale::where('client_id', $id)
                ->select(
                    'id',
                    'type_impot',
                    'regime',
                    'montant',
                    'periode',
                    'date_paiement',
                    'status',
                    'file'
                )
                ->orderBy('date_paiement', 'desc')
                ->get()
                ->groupBy('type_impot')
                ->map(function ($items) {
                    $latest = $items->first();
                    return [
                        'id' => $latest->id,
                        'type' => $latest->type_impot,
                        'regime' => $latest->regime,
                        'montant' => number_format($latest->montant, 0, ',', ' ') . ' FCFA',
                        'periode' => $latest->periode,
                        'prochaine_echeance' => \Carbon\Carbon::parse($latest->date_paiement)->locale('fr')->isoFormat('DD MMMM YYYY'),
                        'status' => $latest->status,
                        'document' => $latest->file
                    ];
                })
                ->values();

            return response()->json($situations);
        } catch (\Exception $e) {
            \Log::error('Erreur situation fiscale: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération de la situation fiscale'], 500);
        }
    }

    public function situationsSocial($id)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            $situations = SituationSociale::where('client_id', $id)
                ->select(
                    'id',
                    'type_sociale',
                    'regime',
                    'montant',
                    'periode',
                    'date_paiement',
                    'status',
                    'file'
                )
                ->orderBy('date_paiement', 'desc')
                ->get()
                ->groupBy('type_sociale')
                ->map(function ($items) {
                    $latest = $items->first();
                    return [
                        'id' => $latest->id,
                        'type' => $latest->type_sociale,
                        'regime' => $latest->regime,
                        'montant' => number_format($latest->montant, 0, ',', ' ') . ' FCFA',
                        'periode' => $latest->periode,
                        'prochaine_echeance' => \Carbon\Carbon::parse($latest->date_paiement)->locale('fr')->isoFormat('DD MMMM YYYY'),
                        'status' => $latest->status,
                        'document' => $latest->file
                    ];
                })
                ->values();

            return response()->json($situations);
        } catch (\Exception $e) {
            \Log::error('Erreur situation sociale: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération de la situation sociale'], 500);
        }
    }

    public function getFiscalSituation()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            $situations = SituationFiscale::where('client_id', $user->id)
                ->select(
                    'id',
                    'type_impot',
                    'regime',
                    'montant',
                    'periode',
                    'date_paiement',
                    'status',
                    'file'
                )
                ->orderBy('date_paiement', 'desc')
                ->get()
                ->groupBy('type_impot')
                ->map(function ($items) {
                    $latest = $items->first();
                    return [
                        'id' => $latest->id,
                        'type' => $latest->type_impot,
                        'regime' => $latest->regime,
                        'montant' => number_format($latest->montant, 0, ',', ' ') . ' FCFA',
                        'periode' => $latest->periode,
                        'prochaine_echeance' => \Carbon\Carbon::parse($latest->date_paiement)->locale('fr')->isoFormat('DD MMMM YYYY'),
                        'status' => $latest->status,
                        'document' => $latest->file
                    ];
                })
                ->values();

            return response()->json($situations);
        } catch (\Exception $e) {
            \Log::error('Erreur situation fiscale: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération de la situation fiscale'], 500);
        }
    }

    public function getSocialSituation()
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            $situations = SituationSociale::where('client_id', $user->id)
                ->select(
                    'id',
                    'type_sociale',
                    'regime',
                    'montant',
                    'periode',
                    'date_paiement',
                    'status',
                    'file'
                )
                ->orderBy('date_paiement', 'desc')
                ->get()
                ->groupBy('type_sociale')
                ->map(function ($items) {
                    $latest = $items->first();
                    return [
                        'id' => $latest->id,
                        'type' => $latest->type_sociale,
                        'regime' => $latest->regime,
                        'montant' => number_format($latest->montant,  0, ',', ' ') . ' FCFA',
                        'periode' => $latest->periode,
                        'prochaine_echeance' => \Carbon\Carbon::parse($latest->date_paiement)->locale('fr')->isoFormat('DD MMMM YYYY'),
                        'status' => $latest->status,
                        'document' => $latest->file
                    ];
                })
                ->values();

            return response()->json($situations);
        } catch (\Exception $e) {
            \Log::error('Erreur situation sociale: ' . $e->getMessage());
            return response()->json(['error' => 'Erreur lors de la récupération de la situation sociale'], 500);
        }
    }

    public function services(Request $request) {
        try {   
            $user = JWTAuth::parseToken()->authenticate();
            
            // Récupérer tous les services
            $services = Service::all();
    
            // Transformation des données pour s'assurer qu'elles sont au bon format
            $formattedServices = $services->map(function ($service) {
                return [
                    'id' => $service->id,
                    'name' => $service->name,
                    //'price' => $service->price,
                    'description' => $service->description,
                ];
            });
    
            // Retourner les services formatés
            return response()->json([
                'services' => $formattedServices,
                'user_id'  => $user->id,
            ]);
        } catch (\Exception $e) {
            // Gérer les exceptions et retourner une réponse d'erreur
            return response()->json(['error' => 'Utilisateur non connecté.'], 401);
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
    
            $user = JWTAuth::parseToken()->authenticate();
            
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

            $user = JWTAuth::parseToken()->authenticate();
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
            $user = JWTAuth::parseToken()->authenticate();
            
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
                $user = JWTAuth::parseToken()->authenticate();
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
            $user = JWTAuth::parseToken()->authenticate();
            
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
    
            $user = JWTAuth::parseToken()->authenticate();
            
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
    

    public function documents(Request $request)
    {
        try {
            $user = JWTAuth::parseToken()->authenticate();

            $documents = ClientDocument::where('user_id', $user->id)
                ->orderBy('created_at', 'desc')
                ->get()
                ->map(function ($document) {
                    return [
                        'id' => $document->id,
                        'title' => $document->name,
                        'type' => $document->filename,
                        'date' => $document->created_at->format('d/m/Y'),
                        'size' => $this->formatFileSize($document->size),
                    ];
                });

            return response()->json($documents);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue'], 500);
        }
    }

    public function documentsDownload($documentId)
        {
            try {
                $user = JWTAuth::parseToken()->authenticate();
                $file = ClientDocument::findOrFail($documentId);
                
                if ($file->user_id !== $user->id) {
                    return response()->json(['error' => 'Non autorisé'], 403);
                }

                $filePath = 'client-docs/' . $file->user_id . '/' . $file->hashname;
                
                if (!Storage::exists($filePath)) {
                    return response()->json(['error' => 'Document introuvable'], 404);
                }

                $mimeType = Storage::mimeType($filePath);
                $headers = [
                    'Content-Type' => $mimeType,
                    'Content-Disposition' => 'attachment; filename="' . $file->filename . '"',
                    'Cache-Control' => 'no-cache, no-store, must-revalidate',
                    'Pragma' => 'no-cache',
                    'Expires' => '0'
                ];

                // Log pour le débogage
                \Log::info('Download started', [
                    'file' => $file->filename,
                    'mime' => $mimeType,
                    'path' => $filePath
                ]);

                return Storage::download($filePath, $file->filename, $headers);
                
            } catch (\Exception $e) {
                \Log::error('Download error: ' . $e->getMessage());
                return response()->json([
                    'error' => 'Une erreur est survenue',
                    'message' => $e->getMessage()
                ], 500);
            }
        }
    private function formatFileSize($size)
    {
        $units = ['B', 'KB', 'MB', 'GB'];
        $power = floor(log($size, 1024));
        return round($size / pow(1024, $power), 2) . ' ' . $units[$power];
    }

    public function changePassword(Request $request) {
        try {
            $user = JWTAuth::parseToken()->authenticate();
            
            // Valider les données
            $validator = Validator::make($request->all(), [
                'currentPassword' => 'required',
                'newPassword' => 'required|min:8'
            ]);

            if ($validator->fails()) {
                return response()->json(['error' => $validator->errors()], 400);
            }

            // Vérifier l'ancien mot de passe
            if (!Hash::check($request->currentPassword, $user->password)) {
                return response()->json(['error' => 'Le mot de passe actuel est incorrect'], 400);
            }

            // Mettre à jour le mot de passe
            $user->password = Hash::make($request->newPassword);
            $user->save();

            return response()->json(['message' => 'Mot de passe modifié avec succès']);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Une erreur est survenue'], 500);
        }
    } 

     public function indexNotification()
    {
        try { 
            $user = JWTAuth::parseToken()->authenticate();

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
            $user = JWTAuth::parseToken()->authenticate();

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
            $user = JWTAuth::parseToken()->authenticate();

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
}
