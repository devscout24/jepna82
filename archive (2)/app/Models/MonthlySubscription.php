<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MonthlySubscription extends Model
{
    protected $fillable = [
        'title',
        'title_text',
        'price',
        'duration',
        'stripe_price_id',
        'plan_id',
    ];
}
