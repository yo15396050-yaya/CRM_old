<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerRequest extends Model
{
    use HasFactory;

    protected $table = 'customer_requests';
    
    protected $fillable = [
        'project_id',
        'service_id',
        'name',
        'client_id',
        'name',
        'type_request',	
        'request_text',	
        'status',
        'created_by',
        'created_at',
        'updated_at',
    ];
}
