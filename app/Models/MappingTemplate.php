<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MappingTemplate extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'config',
    ];

    protected $casts = [
        'config' => 'array',
    ];
}
