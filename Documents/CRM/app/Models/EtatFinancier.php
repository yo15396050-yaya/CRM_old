<?php

namespace App\Models;

use App\Traits\HasCompany;
use App\Traits\CustomFieldsTrait;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Purchase\Entities\PurchaseStockAdjustment;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Model;

/**
 * App\Models\EtatFinancier
 *
 * @property int $id
 * @property string|null $company_id
 * @property string|null $user_id
 * @property string|null $info
 * @property string|null $etat_301
 * @property string|null $etat_302
 * @property string|null $tee_rme
 * @property string|null $balance
 * @property string|null $bilan
 * @property string|null $pv
 * @property string|null $rapport
 * @property string|null $facture
 * @property string|null $valid_client
 * @property string|null $visa
 * @property string|null $depot_ligne
 * @property string|null $depot_physique
 * @property string|null $said
 * @property \Illuminate\Support\Carbon|null $changed_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder|EtatFinancier newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EtatFinancier newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder|EtatFinancier query()
 * @method static \Illuminate\Database\Eloquent\Builder|EtatFinancier whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EtatFinancier whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EtatFinancier whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder|EtatFinancier whereUpdatedAt($value)
 * @mixin \Eloquent
*/

class EtatFinancier extends BaseModel
{
    use HasCompany;
    use HasFactory, CustomFieldsTrait;

    protected $table = 'etatfinanciers';
    const FILE_PATH = 'etatfinanciers';

    protected $fillable = [
        'company_id',  
        'user_id',
        'chiffre',
        'etat',
        'info',
        'etat_301',
        'etat_302',
        'tee_rme',
        'balance',
        'bilan',
        'pv',
        'rapport',
        'facture',
        'valid_client',
        'visa',
        'depot_ligne',
        'depot_physique',
        'said',
        'changed_at',
    ];

    const CUSTOM_FIELD_MODEL = 'App\Models\EtatFinancier';
}
