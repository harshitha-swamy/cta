<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
     use HasFactory;
     protected $table = 'tasks';

    protected $fillable = [
        'ticket_link',
        'ticket_description',
        'dealer_code',
        'project_name',
        'website_link',
        'created_by',
        'status',
    ];
}
