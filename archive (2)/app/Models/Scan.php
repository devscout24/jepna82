<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Scan extends Model
{
    protected $fillable = [
        'title',
        'description',
        'price',
        'items',
    ];

    protected $casts = [
        'items' => 'array',
    ];
}
