<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Formulaire extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'nom',
        'email',
        'numero',
        'critere',
        'nom_diplome',
        'type_operation',
        'paiement',
        'date_inscription',
        'label_formation',
        'is_active',
        'statut',
        'commentaire',  
    ];
}
