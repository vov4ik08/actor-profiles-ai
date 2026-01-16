<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Actor extends Model
{
    protected $fillable = [
        'email',
        'description',
        'description_hash',
        'first_name',
        'last_name',
        'address',
        'height_cm',
        'weight_kg',
        'gender',
        'age',
    ];

    protected $casts = [
        'height_cm' => 'integer',
        'weight_kg' => 'integer',
        'age' => 'integer',
    ];
}

