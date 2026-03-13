<?php

namespace App\Http\Controllers;

use App\Models\Formulaire;
use Illuminate\Http\Request;
use App\DataTables\ContractsDataTable;
use App\Events\ContractSignedEvent;
use App\Helper\Files;
use App\Helper\Reply;
use App\Http\Requests\Admin\Contract\StoreRequest;
use App\Http\Requests\Admin\Contract\UpdateRequest;
use App\Http\Requests\ClientContracts\SignRequest;
use App\Models\BaseModel;
use App\Models\Contract;
use App\Models\ContractSign;
use App\Models\ContractTemplate;
use App\Models\ContractType;
use App\Models\Currency;
use App\Models\Project;
use App\Models\Company;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\File;
use App\Models\ProjectStatusSetting;
use App\Models\Role; 
use App\Models\Invoice;
use App\Models\Payment; 
use App\Models\ClientNote;
use App\Scopes\ActiveScope;
use App\Traits\ImportExcel;
use App\Models\Notification;
use App\Imports\ClientImport;
use App\Jobs\ImportClientJob;
use App\Models\ClientDetails;
use App\Models\ClientCategory;
use App\Models\PurposeConsent;
use App\Models\LanguageSetting;
use App\Models\UniversalSearch;
use App\Models\TicketForm;
use App\Models\ClientSubCategory;
use App\Models\PurposeConsentUser;
use App\DataTables\TicketDataTable;
use App\DataTables\ClientsDataTable;
use App\DataTables\InvoicesDataTable;
use App\DataTables\PaymentsDataTable;
use App\DataTables\ProjectsDataTable;
use App\DataTables\EstimatesDataTable;
use App\DataTables\ClientGDPRDataTable;
use App\DataTables\ClientNotesDataTable;
use App\DataTables\CreditNotesDataTable;
use App\DataTables\ClientContactsDataTable;
use App\DataTables\OrdersDataTable;
use App\Enums\Salutation;
use App\Http\Requests\Admin\Employee\ImportRequest;
use App\Http\Requests\Admin\Client\StoreClientRequest;
use App\Http\Requests\Gdpr\SaveConsentUserDataRequest;
use App\Http\Requests\Admin\Client\UpdateClientRequest;
use App\Http\Requests\Admin\Employee\ImportProcessRequest;
use App\Models\Lead;
use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;
use App\Traits\EmployeeActivityTrait;
use Illuminate\Support\Facades\Mail;
use PDF;
use App\Mail\ConfirmationTicket;
use App\Models\Registration;
use Illuminate\Support\Facades\Storage;
use ZipArchive;


class FormController extends Controller
{   
    public function index()
    {
        return view('form.index');
    }

    public function create()
    {
        return view('form.afterpay');
    }

    public function store(Request $request)
    {
        // Validation des données
        $request->validate([
            'nom' => 'required|string|max:255',
            'email' => 'nullable|email',
            'numero' => 'nullable|string',
            'total_tickets' => 'required|integer|min:1|max:10',
            'paiement' => 'required|integer|min:1',
            'formations' => 'required|array',
            'formations.*' => 'string',
            'formation_tickets' => 'required|array',
            'commentaire' => 'nullable|string',
            'nomdiplome' => 'required|array',
            'nomdiplome.*' => 'required|string|max:255',
        ]);
        
        $transactNumber = 'TRANSACTION-' . strtoupper(substr(md5(uniqid()), 0, 8)) . '-' . now()->timestamp;
        
        $nomdip = json_encode($request->nomdiplome);
        
        // Récupérer les formations sélectionnées et leurs tickets
        $formations = $request->formations;
        $formation_tickets = $request->formation_tickets;
        
        // Construire les détails des formations avec leurs nombres de places
        $formationDetails = [];
        $dates_formations = [];
        
        // Configuration des dates pour chaque formation
        $formationDates = [
            'BTP - Réussir la planification d\'un chantier' => 'Jeudi 18 décembre | 9h - 12h',
            'Informatique/IA - L\'IA au service de la performance' => 'Mardi 16 décembre | 9h - 12h',
            'Gestion - FNE pratique et conseils fiscaux' => 'Jeudi 11 décembre | 9h - 12h',
            'Ressources Humaines - Digitalisation des processus RH' => 'Jeudi 11 décembre | 15h - 18h',
            'Transport & Logistique - Nouveau code douanier' => 'Jeudi 18 décembre | 15h - 18h',
        ];
        
        // Construire les détails des formations
        foreach ($formations as $formation) {
            $formationId = $this->getFormationId($formation);
            $nbTickets = isset($formation_tickets[$formationId]) ? intval($formation_tickets[$formationId]) : 1;
            
            $formationDetails[] = [
                'nom' => $formation,
                'nombre_places' => $nbTickets,
                'date' => $formationDates[$formation] ?? 'À confirmer',
            ];
            
            $dates_formations[] = $formationDates[$formation] ?? 'À confirmer';
        }
        
        // Convertir en JSON
        $formations_json = json_encode($formationDetails);
        $dates_json = json_encode($dates_formations);
        
        // Création d'un nouvel enregistrement dans la table formulaire
        $validated = Formulaire::create([
            'transaction_id' => $transactNumber,
            'nom' => $request->nom,
            'email' => $request->email,
            'numero' => $request->numero,
            'critere' => $request->name_form,
            'nom_diplome' => $nomdip,
            'type_operation' => 'GTbank',
            'paiement' => $request->paiement,
            'date_inscription' => $dates_json,
            'label_formation' => $formations_json,
            'is_active' => true,
            'statut' => 'en_attente_paiement',
            'commentaire' => $request->commentaire,
        ]);

        // Création de l'enregistrement pour le ticket
        $registration = Registration::create([
            'transaction_id' => $transactNumber,
            'nom_complet' => $request->nom,
            'email' => $request->email,
            'telephone' => $request->numero,
            'nombre_tickets' => $request->total_tickets,
            'nom_diplome' => $nomdip,
            'montant' => $request->paiement,
            'devise' => 'XOF',
            'statut' => 'en_attente_paiement',
            'commentaire_admin' => $request->name_form,
            'date_inscription' => $dates_json,
            'label_formation' => $formations_json,
            'is_active' => true,
        ]);

        // Rediriger vers la page de paiement
        return redirect()->route('form.payment', $registration->id)->with([
            'success' => 'Inscription enregistrée ! Procédez au paiement pour finaliser.',
        ]);
    }
    
    /**
     * Récupère l'ID de la formation à partir de son nom
     */
    private function getFormationId($formationName)
    {
        $map = [
            'BTP - Réussir la planification d\'un chantier' => 'btp',
            'Informatique/IA - L\'IA au service de la performance' => 'ia',
            'Gestion - FNE pratique et conseils fiscaux' => 'gestion',
            'Ressources Humaines - Digitalisation des processus RH' => 'rh',
            'Transport & Logistique - Nouveau code douanier' => 'transport',
        ];
        
        return $map[$formationName] ?? 'unknown';
    }

    public function payment(Request $request, $registrationId)
    {
        $registration = Registration::findOrFail($registrationId);
        
        // Générer le QR code pour le paiement
        $paymentInfo = [
            'montant' => $registration->montant,
            'reference' => $registration->transaction_id,
            'nom' => $registration->nom_complet,
            'description' => $registration->commentaire_admin,
        ];

        return view('form.payment', compact('registration', 'paymentInfo'));
    }
    
    public function confirmPayment(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|int',
            'payment_reference' => 'required|string',
            'payment_method' => 'required|string',
        ]);

        $registration = Registration::findOrFail($request->registration_id);
        
        // Mettre à jour le statut du paiement
        $registration->update([
            'statut' => 'paiement_confirme',
            'reference_paiement' => $request->payment_reference,
            'methode_paiement' => $request->payment_method,
            'date_paiement' => now(),
        ]);

        // Mettre à jour aussi dans la table formulaire
        $formulaire = Formulaire::where('transaction_id', $registration->transaction_id)->first();
        if ($formulaire) {
            $formulaire->update(['statut' => 'paiement_confirme']);
        }

        // Générer le ticket
        return $this->generateTicket($registration, $formulaire);
    }
    
    /*
    private function generateTicket($registration, $validated)
    {
        // Générer un numéro de ticket unique
        $ticketNumber = 'TICKET-' . strtoupper(substr(md5(uniqid()), 0, 8)) . '-' . $registration->id;
        
        // Mettre à jour l'enregistrement avec le numéro de ticket
        $registration->update([
            'ticket_number' => $ticketNumber,
            'statut' => 'payé'
        ]);

        if ($validated) {
            $validated->update(['statut' => 'payé']);
        }

        $qrCodeContent = route('ticket.verify', ['code' => $ticketNumber]);

        // Créez une instance de QrCode
        $qrCode = QrCode::create($qrCodeContent);

        // Générez le QR Code en PNG
        $writer = new PngWriter();
        $result = $writer->write($qrCode);

        // Obtenez le QR Code en base64
        $qr_code_base64 = $result->getDataUri();

        // Données pour le ticket
        $ticketData = [
            'ticket_number' => $ticketNumber,
            'nom_complet' => $registration->nom_complet,
            'email' => $registration->email,
            'telephone' => $registration->telephone,
            'nombre_tickets' => $registration->nombre_tickets,
            'montant_total' => $registration->montant . ' FCFA',
            'date_formation' => 'Samedi 30 Août 2025',
            'heure_formation' => '8h - 12h',
            'lieu_formation' => 'Cocody, Abidjan',
            'qr_code_base64' => $qr_code_base64,
            'transaction_id' => $registration->transaction_id,
            'title_form' => $registration->name_form,
            'date_emission' => now()->format('d/m/Y H:i'),            
        ];

        // Générer le PDF du ticket
        $pdf = PDF::loadView('emails.ticket_pdf', $ticketData);

        // Envoyer l'email avec le ticket en pièce jointe
        if ($registration->email) {
            try {
                Mail::to($registration->email)->send(new ConfirmationTicket($ticketData, $pdf));
            } catch (\Exception $e) {
                // Log l'erreur mais continue le processus
                \Log::error('Erreur envoi email: ' . $e->getMessage());
            }
        }

        // Stocker le PDF pour téléchargement
        if (!Storage::disk('public')->exists('tickets')) {
            Storage::disk('public')->makeDirectory('tickets');
        }
        
        $pdfPath = storage_path('app/public/tickets/' . $ticketNumber . '.pdf');
        $pdf->save($pdfPath);

        // Redirection avec un message de succès
        return redirect()->route('form.success', $ticketNumber)->with([
            'success' => 'Paiement confirmé ! Votre ticket a été généré et envoyé par email.',
            'ticket_pdf' => $pdfPath,
            'ticket_number' => $ticketNumber,
        ]);
    }
    */
    
    private function generateTicket($registration, $validated) 
    {
        // Décoder les participants (JSON vers tableau)
        $participants = json_decode($registration->nom_diplome, true);
    
        if (!$participants || !is_array($participants)) {
            $participants = [$registration->nom_complet]; // fallback
        }
    
        // Marquer la registration globale comme payée
        $registration->update([
            'statut' => 'payé'
        ]);
    
        if ($validated) {
            $validated->update(['statut' => 'payé']);
        }
    
        $ticketsGenerated = [];
    
        foreach ($participants as $key => $participantName) {
            // Générer un numéro unique par ticket
            $ticketNumber = 'TICKET-' . strtoupper(substr(md5(uniqid()), 0, 8)) . '-' . $registration->id . '-' . $key;
    
            // QR Code unique par ticket
            $qrCodeContent = route('ticket.verify', ['code' => $ticketNumber]);
            $qrCode = QrCode::create($qrCodeContent);
            $writer = new PngWriter();
            $result = $writer->write($qrCode);
            $qr_code_base64 = $result->getDataUri();
    
            // Données spécifiques au participant
            $ticketData = [
                'ticket_number' => $ticketNumber,
                'nom_complet' => $participantName, // nom individuel
                'email' => $registration->email,
                'telephone' => $registration->telephone,
                'nombre_tickets' => 1 . '/' . $registration->nombre_tickets, // car c'est un ticket individuel
                'montant_total' => number_format($registration->montant, 0,'.',' ') . ' FCFA',
                'date_formation' => $registration->date_inscription,
                'heure_formation' => '8h - 12h',
                'lieu_formation' => 'Cocody, Abidjan',
                'qr_code_base64' => $qr_code_base64,
                'transaction_id' => $registration->transaction_id,
                'title_form' => $registration->commentaire_admin,
                'date_emission' => now()->format('d/m/Y H:i'),  
                'label_formation' => $registration->label_formation,
            ];
    
            // Générer le PDF
            $pdf = PDF::loadView('emails.ticket_pdf', $ticketData);
    
            // Envoi email
            if ($registration->email) {
                try {
                    Mail::to($registration->email)->send(new ConfirmationTicket($ticketData, $pdf));
                } catch (\Exception $e) {
                    \Log::error('Erreur envoi email: ' . $e->getMessage());
                }
            }
    
            // Stockage local
            if (!Storage::disk('public')->exists('tickets')) {
                Storage::disk('public')->makeDirectory('tickets');
            }
    
            $pdfPath = storage_path('app/public/tickets/' . $ticketNumber . '.pdf');
            $pdf->save($pdfPath);
            
            $ticket = TicketForm::create([
                'registration_id' => $registration->id,
                'label_formation' => $registration->label_formation,
                'participant_name' => $participantName,
                'ticket_number' => $ticketNumber,
                'pdf_path' => storage_path('app/public/tickets/' . $ticketNumber . '.pdf'),
                'statut' => 'payé',
            ]);
    
            $ticketsGenerated[] = [
                'ticket_number' => $ticketNumber,
                'pdf' => $pdfPath,
                'participant' => $participantName
            ];
        }
    
        // Redirection avec tous les tickets générés
        return redirect()->route('form.success', $ticketNumber)->with([
            'success' => 'Paiement confirmé ! ' . count($ticketsGenerated) . ' tickets ont été générés et envoyés par email.',
            'tickets' => $ticketsGenerated
        ]);
    }

    public function validatePayment(Request $request, Registration $registration)
    {
        $formulaire = Formulaire::where('transaction_id', $registration->transaction_id)->first();
    
        // Générer le ticket après validation admin
        return response()->json([
            'success' => true,
            'ticket'  => $this->generateTicket($registration, $formulaire),
        ]);
    }
    
    public function rejectPayment(Request $request, Registration $registration)
    {
        $registration->update([
            'statut' => 'paiement_rejete',
            'commentaire_admin' => $request->commentaire ?? 'Paiement rejeté'
        ]);
    
        $formulaire = Formulaire::where('transaction_id', $registration->transaction_id)->first();
        if ($formulaire) {
            $formulaire->update(['statut' => 'paiement_rejete']);
        }
    
        return response()->json([
            'success' => true,
            'message' => 'Paiement rejeté avec succès'
        ]);
    }

    public function success($ticketNumber)  
    {
        // Récupérer le ticket de référence
        $ticket = TicketForm::where('ticket_number', $ticketNumber)->first();
    
        if (!$ticket) {
            return redirect()->route('form.index')->with('error', 'Ticket non trouvé.');
        }
    
        // Récupérer l'inscription liée
        $registration = Registration::find($ticket->registration_id);
    
        if (!$registration) {
            return redirect()->route('form.index')->with('error', 'Inscription non trouvée.');
        }
    
        // Récupérer tous les tickets associés à cette inscription
        $tickets = TicketForm::where('registration_id', $registration->id)->get();
    
        return view('form.afterpay', compact('tickets', 'registration'));
    }


    public function download($ticketNumber)
    {
        $ticket = TicketForm::where('ticket_number', $ticketNumber)->first();
        if ($ticket && $ticket->pdf_path && Storage::disk('public')->exists('tickets/' . basename($ticket->pdf_path))) {
            return Storage::disk('public')->download('tickets/' . basename($ticket->pdf_path));
        }
    
        return redirect()->back()->with('error', 'Le fichier n\'existe pas.');
    }
    
    public function downloadAll($registrationId)
    {
        $registration = Registration::findOrFail($registrationId);
        $tickets = TicketForm::where('registration_id', $registration->id)->get();
    
        if ($tickets->isEmpty()) {
            return redirect()->back()->with('error', 'Aucun ticket trouvé pour cette inscription.');
        }
    
        // Nom du fichier ZIP
        $zipFileName = 'tickets_' . $registration->id . '.zip';
        $zipPath = storage_path('app/public/tickets/' . $zipFileName);
    
        $zip = new ZipArchive;
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            foreach ($tickets as $ticket) {
                if ($ticket->pdf_path && file_exists($ticket->pdf_path)) {
                    $zip->addFile($ticket->pdf_path, basename($ticket->pdf_path));
                }
            }
            $zip->close();
        } else {
            return redirect()->back()->with('error', 'Impossible de créer l’archive ZIP.');
        }
    
        return response()->download($zipPath)->deleteFileAfterSend(true);
    }
    
    public function verify($code)
    {
        $ticket = TicketForm::where('ticket_number', $code)->first();
    
        if ($ticket && $ticket->statut === 'payé') {
            return view('form.verify_success', compact('ticket'));
        }
    
        return view('form.verify_fail', ['code' => $code]);
    }

    // Nouvelle méthode pour la validation manuelle des paiements (admin)
    public function adminPayments()
    {
        $pendingPayments = Registration::where('statut', 'paiement_confirme')
                                     ->orderBy('created_at', 'desc')
                                     ->get();
        
        return view('admin.payments', compact('pendingPayments'));
    }



    public function testTickets()
    {
        // Lister tous les fichiers dans tickets/
        $files = Storage::disk('public')->files('tickets');

        dd($files);
    }
    
    
    public function formcontract()
    {

        $contractId = request('id');
        $contract = null;

        if ($contractId != '') {
            $contractTemplate = Contract::findOrFail($contractId);
        }

        $templates = ContractTemplate::all();
        $contractTypes = ContractType::all();
        $currencies = Currency::all();
        $projects = Project::all();
    
        $lastContract = Contract::lastContractNumber() + 1;
        $invoiceSetting = invoice_setting();
        $zero = '';
        $contract = new Contract();
    
        return view('form.contract.index', compact('contractTypes', 'contract', 'currencies', 'projects', 'lastContract', 'invoiceSetting', 'zero'));
    }

    public function storecontract(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email', // Assurez-vous que l'e-mail est unique
            'password' => 'required|string|min:6', // Ajoutez une contrainte de longueur
            'company_name' => 'required|string',
            'contract_number' => 'required|integer',
            'contract_type' => 'required|exists:contract_types,id',
            'amount' => 'required|numeric|min:0',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);
    
        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput(); // Rediriger avec les erreurs
        }
    
        DB::beginTransaction(); // Démarrer la transaction
    
        try {
            // Créer l'utilisateur
            $user = new User();
            $user->name = $request->name;
            $user->email = $request->email;
            $user->password = bcrypt($request->password);
            $user->email_notifications = $request->sendMail === 'yes' ? 1 : 0;
    
            $user->save(); // Sauvegarder l'utilisateur
    
            // Créer les détails du client
            $clientData = $request->only([
                'company_name', 'numadh', 'website', 'tax_name', 'gst_number', 'numrccm',
                'formjurid', 'regime', 'imp_centre', 'acti_prin', 'section',
                'parcelle', 'codeacti', 'montcapit', 'office', 'city', 
                'state', 'postal_code', 'address'
            ]);
    
            // Gérer le téléchargement de fichiers (si applicable)
            if ($request->hasFile('file')) {
                $filePath = $request->file('file')->store('contracts'); // Ajustez le chemin de stockage
                $clientData['file_path'] = $filePath;
            }
    
            $user->clientDetails()->create($clientData); // Créer les détails du client
    
            // Gérer les notes
            if ($request->note) {
                $clientNote = new ClientNote();
                $clientNote->title = 'Note';
                $clientNote->client_id = $user->id;
                $clientNote->details = trim_editor($request->note);
                $clientNote->save();
            }
    
            // Gestion des champs personnalisés
            if ($request->custom_fields_data) {
                $user->clientDetails->updateCustomFieldData($request->custom_fields_data);
            }
    
            // Gestion des rôles
            $role = Role::where('name', 'client')->first();
            if ($role) {
                $user->attachRole($role->id);
                $user->assignUserRolePermission($role->id);
            }
    
            // Créer un nouveau contrat
            $contract = new Contract();
            $contract->contract_number = $request->contract_number;
            $contract->contract_type_id = $request->contract_type;
            $contract->subject = $request->subject; // Assurez-vous que ce champ existe dans le formulaire
            $contract->amount = $request->amount;
            $contract->start_date = $request->start_date;
            $contract->end_date = $request->end_date;
            $contract->original_start_date = $request->start_date;
            $contract->original_end_date = $request->end_date;
            $contract->currency_id = 1;
            $contract->client_id = $user->id;
    
            $contract->save(); // Sauvegarder le contrat
    
            DB::commit(); // Valider la transaction
    
            return redirect()->route('form.contract.view', $contract->id)->with('success', __('messages.recordSaved'));

        } catch (\Exception $e) {
            DB::rollBack(); // Annuler la transaction en cas d'erreur
            return Reply::error($e->getMessage()); // Gérer l'erreur
        }
    }

    public function contractview($id)
    {
        // Récupérer le contrat avec les relations nécessaires
        $contract = Contract::with([
            'signature',
            'client',
            'client.clientDetails',
            'files',
            'renewHistory',
            'renewHistory.renewedBy',
            'discussion.user'
        ])->findOrFail($id);

        $contracts = Contract::findOrFail($id);

        return view('form.contract.show', compact('contract', 'contracts')); 
    }

    public function storeee(StoreRequest $request)
    {
        $contract = new Contract();
        $this->storeUpdate($request, $contract);

        return Reply::redirect(route('contracts.index'), __('messages.recordSaved'));
    }

    private function storeUpdate($request, $contract)
    {
        $contract->client_id = $request->client_id;
        $contract->project_id = $request->project_id;
        $contract->subject = $request->subject;
        $contract->amount = $request->amount;
        $contract->currency_id = $request->currency_id;
        $contract->original_amount = $request->amount;
        $contract->contract_name = $request->contract_name;
        $contract->alternate_address = $request->alternate_address;
        $contract->contract_note = $request->note;
        $contract->cell = $request->cell;
        $contract->office = $request->office;
        $contract->city = $request->city;
        $contract->state = $request->state;
        $contract->country = $request->country;
        $contract->postal_code = $request->postal_code;
        $contract->contract_type_id = $request->contract_type;
        $contract->contract_number = $request->contract_number;
        $contract->start_date = companyToYmd($request->start_date);
        $contract->original_start_date = companyToYmd($request->start_date);
        $contract->end_date = $request->end_date == null ? $request->end_date : companyToYmd($request->end_date);
        $contract->original_end_date = $request->end_date == null ? $request->end_date : companyToYmd($request->end_date);
        $contract->description = trim_editor($request->description);
        $contract->contract_detail = trim_editor($request->description);
        $contract->save();

        // To add custom fields data
        if ($request->custom_fields_data) {
            $contract->updateCustomFieldData($request->custom_fields_data);
        }

        return $contract;
    }
    
    public function showrenew($id)
    {
        $viewPermission = user()->permission('view_contract');
        $this->addContractPermission = user()->permission('add_contract');
        $this->editContractPermission = user()->permission('edit_contract');
        $this->deleteContractPermission = user()->permission('delete_contract');
        $this->viewDiscussionPermission = $viewDiscussionPermission = user()->permission('view_contract_discussion');
        $this->viewContractFilesPermission = $viewContractFilesPermission = user()->permission('view_contract_files');

        $this->contract = Contract::with(['signature', 'client', 'client.clientDetails', 'files' => function ($q) use ($viewContractFilesPermission) {
            if ($viewContractFilesPermission == 'added') {
                $q->where('added_by', user()->id);
            }
        }, 'renewHistory', 'renewHistory.renewedBy',
            'discussion' => function ($q) use ($viewDiscussionPermission) {
                if ($viewDiscussionPermission == 'added') {
                    $q->where('contract_discussions.added_by', user()->id);
                }
            }, 'discussion.user'])->findOrFail($id)->withCustomFields();
        abort_403(!(
            $viewPermission == 'all'
            || ($viewPermission == 'added' && user()->id == $this->contract->added_by)
            || ($viewPermission == 'owned' && user()->id == $this->contract->client_id)
            || ($viewPermission == 'both' && (user()->id == $this->contract->client_id || user()->id == $this->contract->added_by))
        ));

        $contract = new contract();

        $this->contracts = Contract::findOrFail($id);

        $getCustomFieldGroupsWithFields = $contract->getCustomFieldGroupsWithFields();

        if ($getCustomFieldGroupsWithFields) {
            $this->fields = $getCustomFieldGroupsWithFields->fields;
        }

        $this->pageTitle = $this->contract->contract_number;

        $tab = request('tab');

        $this->view = match ($tab) {
            'discussion' => 'contracts.ajax.discussion',
            'files' => 'contracts.ajax.files',
            'renew' => 'contracts.ajax.renew',
            default => 'contracts.ajax.summary',
        };


        if (request()->ajax()) {
            return $this->returnAjax($this->view);
        }

        $this->activeTab = $tab ?: 'profile';

        return view('contracts.show', $this->data);

    }

    public function downloadContract($id)
    {
        $this->contract = Contract::findOrFail($id);
        $viewPermission = user()->permission('view_contract');
        $this->contract = Contract::with('signature', 'client', 'client.clientDetails', 'files')->findOrFail($id)->withCustomFields();

        $getCustomFieldGroupsWithFields = $this->contract->getCustomFieldGroupsWithFields();

        if ($getCustomFieldGroupsWithFields) {
            $this->fields = $getCustomFieldGroupsWithFields->fields;
        }

        abort_403(!(
            $viewPermission == 'all'
            || ($viewPermission == 'added' && user()->id == $this->contract->added_by)
            || ($viewPermission == 'owned' && user()->id == $this->contract->client_id)
            || ($viewPermission == 'both' && (user()->id == $this->contract->client_id || user()->id == $this->contract->added_by))
        ));


        $pdf = app('dompdf.wrapper');

        $this->company = $this->settings = company();

        $this->invoiceSetting = invoice_setting();

        $pdf->setOptions([
            'defaultFont' => 'Helvetica', // Utiliser une police standard
            'isRemoteEnabled' => true,
        ]);

        //dd($this->data);

        App::setLocale($this->invoiceSetting->locale ?? 'en');
        Carbon::setLocale($this->invoiceSetting->locale ?? 'en');
        $pdf->loadView('contracts.contract-pdf', $this->data);

        $filename = 'contract-' . $this->contract->id;

        return $pdf->download($filename . '.pdf');

    }

    public function downloadView($id)
    {
        $this->contract = Contract::findOrFail($id)->withCustomFields();
        $pdf = app('dompdf.wrapper');

        $this->company = $this->settings = Company::findOrFail($this->contract->company_id);

        $this->invoiceSetting = invoice_setting();

        $getCustomFieldGroupsWithFields = $this->contract->getCustomFieldGroupsWithFields();

        if ($getCustomFieldGroupsWithFields) {
            $this->fields = $getCustomFieldGroupsWithFields->fields;
        }

        $pdf->setOption('enable_php', true);
        $pdf->setOption('isHtml5ParserEnabled', true);
        $pdf->setOption('isRemoteEnabled', true);

        App::setLocale($this->invoiceSetting->locale ?? 'en');
        Carbon::setLocale($this->invoiceSetting->locale ?? 'en');
        $pdf->loadView('contracts.contract-pdf', $this->data);

        $filename = 'contract-' . $this->contract->id;

        return [
            'pdf' => $pdf,
            'fileName' => $filename
        ];
    }

    public function storecontrachhgt(StoreClientRequest $request, StoreRequest $contractRequest)
    {
        DB::beginTransaction(); // Démarrer la transaction
    
        try {
            // Enregistrement du client
            $clientData = $request->validated(); // Utilisez validated() pour obtenir les données validées
            unset($clientData['country']); // Si vous ne voulez pas conserver ce champ
            $clientData['password'] = bcrypt($request->password);
            $clientData['country_id'] = $request->country;
            $clientData['email_notifications'] = $request->sendMail === 'yes' ? 1 : 0;
            $clientData['gender'] = $request->gender ?? null;
            $clientData['locale'] = $request->locale ?? 'fr';
    
            // Gestion des fichiers uploadés
            if ($request->hasFile('image')) {
                $clientData['image'] = Files::uploadLocalOrS3($request->image, 'avatar', 300);
            }
    
            if ($request->hasFile('company_logo')) {
                $clientData['company_logo'] = Files::uploadLocalOrS3($request->company_logo, 'client-logo', 300);
            }
    
            // Créer l'utilisateur (client)
            $user = User::create($clientData);
            $user->clientDetails()->create($clientData);
    
            // Enregistrement du contrat
            $contractData = $contractRequest->validated(); // Utilisez validated() pour obtenir les données validées
            $contract = new Contract($contractData);
            $contract->client_id = $user->id; // Lier le contrat au client créé
            $contract->save(); // Sauvegarder le contrat
    
            // Ajout de notes, champs personnalisés, rôles, etc.
            $this->handleAdditionalClientData($request, $user);
    
            DB::commit(); // Valider la transaction
    
            return Reply::successWithData(__('messages.recordSaved'), ['redirectUrl' => route('form.contract.view')]);
        } catch (\Exception $e) {
            DB::rollBack(); // Annuler la transaction en cas d'erreur
            return Reply::error($e->getMessage()); // Gérer l'erreur
        }
    }

    private function handleAdditionalClientData($request, $user)
    {
        // Gérer les notes
        if ($request->note) {
            $clientNote = new ClientNote();
            $clientNote->title = 'Note';
            $clientNote->client_id = $user->id;
            $clientNote->details = trim_editor($request->note);
            $clientNote->save();
        }
    
        // Gestion des champs personnalisés
        if ($request->custom_fields_data) {
            $user->clientDetails->updateCustomFieldData($request->custom_fields_data);
        }
    
        // Gestion des rôles
        $role = Role::where('name', 'client')->first();
        if ($role) {
            $user->attachRole($role->id);
            $user->assignUserRolePermission($role->id);
        }
    }
}