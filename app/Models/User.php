<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Cashier\Billable;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    use Billable, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'username',
        'email',
        'phone',
        'password',
        'avatar',
        'role',
        'status',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'role' => 'integer',
            'status' => 'integer',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all of the subscriptions for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function subscriptions()
    {
        return $this->hasMany(UserSubscription::class);
    }

    /**
     * Get all of the wallets for the User
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function wallets()
    {
        return $this->hasMany(UserWalet::class);
    }

    /**
     * Update user credit balance and log in wallet
     */
    public function updateBalance($credits, $source, $packageId = null, $subscriptionId = null)
    {
        $lastWallet = UserWalet::where('user_id', $this->id)->latest()->first();
        $prevBalance = $lastWallet ? $lastWallet->current_balance : 0;

        return UserWalet::create([
            'user_id' => $this->id,
            'package_id' => $packageId,
            'subscription_id' => $subscriptionId,
            'type' => 'credit',
            'source' => $source,
            'credits' => $credits,
            'previous_balance' => $prevBalance,
            'current_balance' => $prevBalance + $credits,
            'note' => "Credits updated via " . str_replace('_', ' ', $source),
        ]);
    }

    // JWT Methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
}
