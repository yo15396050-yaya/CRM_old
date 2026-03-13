<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SituationFiscale extends Model
{
    use HasFactory;

    protected $table = 'situations_fiscale';
    
    protected $fillable = [
        'client_id',
        'type_impot',
        'regime',
        'montant',
        'periode',
        'date_paiement',
        'file',
        'status',
        'created_by',
        'created_at',
        'updated_at',
    ];
}
