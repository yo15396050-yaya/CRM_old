<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Registration extends Model
{
    use HasFactory;

    protected $fillable = [
        'transaction_id',
        'nom_complet',
        'email',
        'telephone',
        'nombre_tickets',
        'montant',
        'devise',
        'statut',
        'date_inscription',
        'reference_paiement',
        'methode_paiement',
        'date_paiement',
        'commentaire_admin',
        'ticket_number',
        'nom_diplome',
        'label_formation',
        'is_active',
    ];
    
    public function tickets()
    {
        return $this->hasMany(TicketForm::class, 'registration_id', 'id');
    }
}
