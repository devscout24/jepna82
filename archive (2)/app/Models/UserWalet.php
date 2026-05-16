<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserWalet extends Model
{
    public $timestamps = false;

    protected $fillable = [
        'user_id',
        'package_id',
        'subscription_id',
        'credit_pack_id',
        'payment_id',
        'type',
        'source',
        'credits',
        'previous_balance',
        'current_balance',
        'refundable_credit',
        'refund_status',
        'stripe_payment_id',
        'note',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
