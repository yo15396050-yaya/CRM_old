<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TicketForm extends Model
{
    use HasFactory;

    protected $fillable = ['registration_id', 'participant_name', 'ticket_number', 'pdf_path', 'statut'];
}
