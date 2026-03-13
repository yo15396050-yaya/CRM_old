<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SituationSociale extends Model
{
    use HasFactory;

    protected $table = 'situations_sociale';
    
    protected $fillable = [
        'client_id',
        'type_sociale',
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
