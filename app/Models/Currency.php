<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Currency extends Model
{
    use HasFactory;

    protected $table = 'currencies';

    protected $fillable = [
        'code',
        'value',
    ];

    protected $casts = [
        'value' => 'float',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
