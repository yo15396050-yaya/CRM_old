<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Entreprise extends BaseModel
{
    use HasFactory;

    protected $table = 'entreprises';

    protected $fillable = [
        'forme_juridique',
        'denomination_sociale',
        'capital_social',
        'nombre_associes',
        'objet_social',
        'siege_social',
        'ville',
        'duree',
        'nom',
        'nationalite',
        'date_naissance',
        'lieu_naissance',
        'adresse',
        'telephone',
        'email',
        'piece_identite',
        'justificatif_domicile',
        'autres_documents',
        'user_id',
        'statut',
        'date_demande'
    ];

    protected $dates = [
        'date_naissance',
        'date_demande',
        'created_at',
        'updated_at'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
