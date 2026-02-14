<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bus extends Model
{
    use HasFactory;

    protected $fillable = [
        'bus_code',
        'bus_name',
        'seat_capacity',
        'class',
    ];

    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }
}
