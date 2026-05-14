<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserSubscription extends Model
{
    protected $fillable = [
        'user_id',
        'package_id',
        'stripe_subscription_id',
        'stripe_customer_id',
        'stripe_invoice_id',
        'amount',
        'currency',
        'start_date',
        'end_date',
        'next_billing_date',
        'trial_ends_at',
        'credits_allocated',
        'credits_used',
        'credits_remaining',
        'credits_reset_at',
        'payment_status',
        'subscription_status',
        'cancel_at_period_end',
        'cancelled_at',
        'cancellation_reason',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function package()
    {
        return $this->belongsTo(PackageAndSubscription::class, 'package_id');
    }
}
