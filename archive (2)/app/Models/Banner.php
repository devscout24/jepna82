<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    protected $fillable = [
        'title',
        'description',
        'button_text',
        'icon',
        'icon_title',
    ];

    protected $casts = [
        'icon' => 'array',
        'icon_title' => 'array',
    ];
}
