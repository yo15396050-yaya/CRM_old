<?php
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController; 

// Route pour vider le cache (Crucial pour cPanel)
Route::get('/clear-cache', function() {
    try {
        Artisan::call('config:clear');
        Artisan::call('cache:clear');
        Artisan::call('route:clear');
        return response()->json(['message' => 'Cache vidé avec succès !']);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Cache déjà propre ou erreur ignorée', 'details' => $e->getMessage()]);
    }
});

// --- ROUTES PUBLIQUES ---
Route::post('/login', [AuthController::class, 'login']);

// --- ROUTES API MOBILE (Centralisées dans AuthController pour cPanel) ---
Route::get('/dashboard', [AuthController::class, 'dashboard']);
Route::get('/dashboard/honoraires/{id}', [AuthController::class, 'honoraires']);
Route::get('/user-infos', [AuthController::class, 'userInfos']);
Route::get('/notifications', [AuthController::class, 'indexNotification']);
Route::get('/notifications/unread-count', [AuthController::class, 'unreadCount']);
Route::get('/clients', [AuthController::class, 'getClients']);
Route::post('/clients', [AuthController::class, 'storeClient']);
Route::delete('/clients/{id}', [AuthController::class, 'destroyClient']);
Route::put('/clients/{id}', [AuthController::class, 'updateClient']);
Route::get('/tasks', [AuthController::class, 'getTasks']);
Route::get('/tasks/{id}', [AuthController::class, 'getTaskDetail']);
Route::put('/tasks/{id}', [AuthController::class, 'updateTask']);
Route::get('/projects/{id}', [AuthController::class, 'getProjectDetail']);
Route::get('/employees', [AuthController::class, 'getEmployees']);
Route::get('/clients/{id}', [AuthController::class, 'getClientDetails']);




// --- MODULE TRAVAIL ---
Route::get('/tasks', [AuthController::class, 'getTasks']);
Route::get('/getAlldiligences', [AuthController::class, 'getAlldiligences']);
Route::post('/customer-requests/storeRequest', [AuthController::class, 'storeRequest']);
Route::get('/contracts', [AuthController::class, 'getContracts']);
Route::get('/knowledge-base', [AuthController::class, 'getKnowledgeBase']);

// --- MODULE RH ---
Route::get('/employees', [AuthController::class, 'getEmployees']);
Route::post('/employees', [AuthController::class, 'storeEmployee']);
Route::get('/employees/{id}', [AuthController::class, 'getEmployeeDetail']);
Route::get('/employee-form-data', [AuthController::class, 'getFormData']);
Route::prefix('attendances')->group(function () {
    Route::get('/', [AuthController::class, 'getAttendances']);
    Route::post('/clock-in-out', [AuthController::class, 'clockInOut']);
});
Route::post('/clock-in-out', [AuthController::class, 'clockInOut']); // Alias pour mobile
Route::get('/permissions', [AuthController::class, 'getPermissions']);
Route::post('/permissions', [AuthController::class, 'storePermission']);
Route::prefix('rh')->group(function () {
    Route::get('/permissions', [AuthController::class, 'getPermissions']);
    Route::post('/permissions', [AuthController::class, 'storePermission']);
});

// --- MODULE FINANCE ---
Route::prefix('finance')->group(function () {
    Route::get('/expenses', [AuthController::class, 'getExpenses']);
    Route::get('/invoices', [AuthController::class, 'getInvoices']);
    Route::get('/financial-states', [AuthController::class, 'getFinancialStates']);
    Route::get('/treasury', [AuthController::class, 'getTreasury']);
});
// Compatibilité arrière
Route::get('/expenses', [AuthController::class, 'getExpenses']);
Route::get('/financial-states', [AuthController::class, 'getFinancialStates']);
Route::get('/treasury', [AuthController::class, 'getTreasury']);
Route::get('/services', [AuthController::class, 'services']);
Route::get('/services/{id}', [AuthController::class, 'getServiceDetail']);

// --- MODULE TAX & SOCIAL (SITUATIONS) ---
Route::prefix('situations-fiscal')->group(function () {
    Route::get('/', [AuthController::class, 'getFiscalSituation']);
    Route::get('/user', [AuthController::class, 'getFiscalSituation']);
});
Route::prefix('situations-social')->group(function () {
    Route::get('/', [AuthController::class, 'getSocialSituation']);
    Route::get('/user', [AuthController::class, 'getSocialSituation']);
});
Route::get('/dashboard/situationsFiscal/{id}', [AuthController::class, 'situationsFiscal']);
Route::get('/dashboard/situationsSocial/{id}', [AuthController::class, 'situationsSocial']);

// --- NOTIFICATIONS (Actions additionnelles) ---
Route::prefix('notifications')->group(function () {
    Route::get('/indexNotification', [AuthController::class, 'indexNotification']);
    Route::post('/mark-as-read', [AuthController::class, 'markAsRead']);
    Route::post('/mark-all-as-read', [AuthController::class, 'markAllAsRead']);
});

// --- MESSAGERIE ---
Route::get('/conversations', [AuthController::class, 'getConversations']);
Route::get('/messages/{id}', [AuthController::class, 'getMessages']);
Route::post('/messages', [AuthController::class, 'sendMessage']);
Route::get('/chat-contacts', [AuthController::class, 'getChatContacts']);
Route::get('/all-users', [AuthController::class, 'getAllUsers']);  // Récupérer TOUS les utilisateurs


// DOCUMENTS
Route::get('/documents', [AuthController::class, 'documents']);
Route::get('/documents/{id}/download', [AuthController::class, 'documentsDownload']);

// PROFIL
Route::post('/change-password', [AuthController::class, 'changePassword']);

// --- MODULE NOTES ---
Route::prefix('notes')->group(function () {
    Route::get('/', [AuthController::class, 'getNotes']);
    Route::post('/', [AuthController::class, 'createNote']);
    Route::get('/{id}', [AuthController::class, 'getNoteDetails']);
    Route::put('/{id}', [AuthController::class, 'updateNote']);
    Route::post('/{id}/sync-blocks', [AuthController::class, 'syncNoteBlocks']);
    Route::delete('/{id}', [AuthController::class, 'deleteNote']);
});


