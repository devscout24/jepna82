<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubscribePlan extends Model
{
    protected $fillable = [
        'title',
        'title_text',
        'price',
        'price_text',
        'items',
    ];

    protected $casts = [
        'items' => 'array',
    ];
}
