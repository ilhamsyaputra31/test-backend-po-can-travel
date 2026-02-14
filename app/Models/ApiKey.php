<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ApiKey extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_name',
        'api_key',
        'status',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];
}
