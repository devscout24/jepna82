<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PackageAndSubscription extends Model
{
    protected $fillable = [
        'title',
        'slug',
        'sub_title',
        'description',
        'price',
        'discount_percentage',
        'final_price',
        'currency',
        'page_limit',
        'extra_page_rate',
        'features',
        'package_type',
        'billing_cycle',
        'stripe_product_id',
        'stripe_price_id',
        'stripe_plan_id',
        'trial_days',
        'is_popular',
        'badge_text',
        'status',
        'sort_order',
    ];

    protected $casts = [
        'features' => 'array',
        'price' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'final_price' => 'decimal:2',
        'extra_page_rate' => 'decimal:4',
        'is_popular' => 'boolean',
        'status' => 'integer',
        'sort_order' => 'integer',
        'page_limit' => 'integer',
        'trial_days' => 'integer',
    ];
}
