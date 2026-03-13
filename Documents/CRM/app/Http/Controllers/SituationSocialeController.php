<?php

namespace App\Http\Controllers;

use App\Models\SituationSociale;
use App\Models\ProjectStatusSetting;
use App\Models\Role;
use App\Models\User;
use App\Helper\Files;
use App\Helper\Reply;
use App\Models\Invoice;
use App\Models\Payment; 
use App\Models\Project;
use App\Models\BaseModel;
use App\Models\ClientNote;
use App\Scopes\ActiveScope;
use App\Traits\ImportExcel;
use App\Models\Notification;
use App\Models\ContractType;
use Illuminate\Http\Request;
use App\Imports\ClientImport;
use App\Jobs\ImportClientJob;
use App\Models\ClientDetails;
use App\Models\ClientCategory;
use App\Models\PurposeConsent;
use App\Models\LanguageSetting;
use App\Models\UniversalSearch;
use App\Models\ClientSubCategory;
use App\Models\PurposeConsentUser;
use Illuminate\Support\Facades\DB;
use App\DataTables\TicketDataTable;
use App\DataTables\ClientsDataTable;
use App\DataTables\FiscaleDataTable;
use App\DataTables\SocialeDatatable;
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
use App\Traits\EmployeeActivityTrait;

class SituationSocialeController extends Controller
{
    use ImportExcel;
    use EmployeeActivityTrait;

    public function __construct()
    {
        parent::__construct();
        $this->pageTitle = 'app.menu.clients';
        $this->middleware(function ($request, $next) {
            abort_403(!in_array('clients', $this->user->modules));

            return $next($request);
        });
    }

    /**
     * client list
     *
     * @return \Illuminate\Http\Response
     */
    public function clientSociale(SocialeDatatable $dataTable){
        $viewPermission = user()->permission('view_clients');
        $this->addClientPermission = user()->permission('add_clients');

        abort_403(!in_array($viewPermission, ['all', 'added', 'both']));

        if (!request()->ajax()) {
            $this->clients = User::allClients(active:false);
            $this->subcategories = ClientSubCategory::all();
            $this->categories = ClientCategory::all();
            $this->projects = Project::all();
            $this->contracts = ContractType::all();
            $this->fiscales = SituationSociale::all();
            $this->countries = countries();
            $this->totalClients = count($this->clients);
            $this->usersWithContracts = DB::table('users')
                ->join('contracts', 'users.id', '=', 'contracts.client_id')
                ->select('users.*', 'contracts.*')
                ->first();
        }

        return $dataTable->render('clients.ajax.sociale', $this->data);

    }
}
