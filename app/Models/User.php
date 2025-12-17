<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Illuminate\Database\Eloquent\Model;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, TwoFactorAuthenticatable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'user_type',
        'profile_completed', // ✅ Fixed: Added missing comma
        'is_verified',       // ✅ Already here
        'email_verified_at', // ✅ Added: This was missing!
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'two_factor_secret',
        'two_factor_recovery_codes',
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
            'password' => 'hashed',
            'two_factor_confirmed_at' => 'datetime',
            'profile_completed' => 'boolean', // ✅ Added: Cast to boolean
        ];
    }
    
    /**
     * Get the client profile associated with the user.
     */
    public function client()
    {
        return $this->hasOne(Client::class, 'clients_id', 'id');
    }

    /**
     * Get the operator profile associated with the user.
     */
    public function operator()
    {
        return $this->hasOne(Operator::class, 'operators_id', 'id');
    }

    /**
     * Get the location associated with the user.
     */
    public function location()
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    /**
     * Check if user email is verified
     */
    public function hasVerifiedEmail()
    {
        return !is_null($this->email_verified_at);
    }

    /**
     * Mark the user's email as verified
     */
    public function markEmailAsVerified()
    {
        return $this->update([
            'is_verified' => true,
            'email_verified_at' => now(),
        ]);
    }
}