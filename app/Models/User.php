<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens, HasFactory, Notifiable, Uuids, HasRoles, LogsActivity;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'address',
        'date_of_birth',
        'gender',
        'emergency_contact',
        'emergency_phone',
        'profile_photo',
        'is_admin',
        'is_active',
        'email_verified_at',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'two_factor_confirmed_at',
        'remember_token',
        'last_login_at',
        'last_login_ip',
        'login_count',
        'failed_login_attempts',
        'locked_until',
        'email_verification_code', 
'email_verification_code_expires_at'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'email_verification_code', 
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'email_verification_code_expires_at' => 'datetime',
        'password' => 'hashed',
        'date_of_birth' => 'date',
        'is_active' => 'boolean',
        'two_factor_confirmed_at' => 'datetime',
        'last_login_at' => 'datetime',
        'locked_until' => 'datetime',
        'login_count' => 'integer',
        'failed_login_attempts' => 'integer',
    ];

    /**
     * Generate a new email verification code
     */
    public function generateEmailVerificationCode()
    {
        $this->email_verification_code = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        $this->email_verification_code_expires_at = Carbon::now()->addMinutes(10); // Code expires in 10 minutes
        $this->save();
        
        return $this->email_verification_code;
    }

    /**
     * Check if verification code is valid
     */
    public function isValidVerificationCode($code)
    {
        return $this->email_verification_code === $code 
            && $this->email_verification_code_expires_at 
            && Carbon::now()->isBefore($this->email_verification_code_expires_at);
    }

    /**
     * Clear verification code after successful verification
     */
    public function clearVerificationCode()
    {
        $this->email_verification_code = null;
        $this->email_verification_code_expires_at = null;
        $this->save();
    }
    

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['name', 'email', 'phone', 'is_active', 'is_admin'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the member profile associated with the user.
     */
    public function member()
{
    return $this->hasOne(Member::class);
}

    /**
     * Get the trainer profile associated with the user.
     */
    public function trainer()
    {
        return $this->hasOne(Trainer::class);
    }

    /**
     * Check if user is admin
     */
    public function isAdmin(): bool
    {
        return $this->is_admin === 1 || $this->hasRole('Admin');
    }

    /**
     * Check if user is trainer
     */
    public function isTrainer(): bool
    {
        return $this->is_admin === 2 || $this->hasRole('Trainer');
    }

    /**
     * Check if user is member
     */
    public function isMember(): bool
    {
        return $this->is_admin === 0 || $this->hasRole('Member');
    }

    /**
     * Get user's display name
     */
    public function getDisplayNameAttribute(): string
    {
        return $this->name;
    }

    /**
     * Get user's role name
     */
    public function getRoleNameAttribute(): string
    {
        return $this->roles->first()?->name ?? 'Member';
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for admin users
     */
    public function scopeAdmins($query)
    {
        return $query->where('is_admin', 1);
    }

    /**
     * Scope for trainer users
     */
    public function scopeTrainers($query)
    {
        return $query->where('is_admin', 2);
    }

    /**
     * Scope for member users
     */
    public function scopeMembers($query)
    {
        return $query->where('is_admin', 0);
    }
}