<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Member extends Model
{
    use Uuids, SoftDeletes, LogsActivity;

    protected $fillable = [
        'user_id',
        'membership_type_id',
        'member_id',
        'first_name',
        'last_name',
        'email',
        'phone',
        'date_of_birth',
        'gender',
        'address',
        'emergency_contact_name',
        'emergency_contact_phone',
        'join_date',
        'membership_start_date',
        'membership_end_date',
        'status',
        'profile_photo',
        'medical_conditions',
        'fitness_goals',
        'preferred_workout_time',
        'referral_source',
        //'is_active'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'join_date' => 'date',
        'membership_start_date' => 'date',
        'membership_end_date' => 'date',
        'medical_conditions' => 'array',
        'fitness_goals' => 'array'
    ];

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['first_name', 'last_name', 'email', 'phone', 'status', 'membership_type_id'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Get the user that owns the member profile.
     */
   public function user(): BelongsTo
{
    return $this->belongsTo(User::class);
}
    /**
     * Get the membership type for the member.
     */
    public function membershipType(): BelongsTo
    {
        return $this->belongsTo(MembershipType::class);
    }

    /**
     * Get the payments for the member.
     */
    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Get the class registrations for the member.
     */
    public function classRegistrations(): HasMany
    {
        return $this->hasMany(ClassRegistration::class);
    }

    /**
     * Get the attendance records for the member.
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }

    /**
     * Get the member's full name.
     */
    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    /**
     * Check if membership is active
     */
    public function getIsActiveAttribute(): bool
    {
        return $this->status === 'active' && 
               $this->membership_end_date && 
               $this->membership_end_date->isFuture();
    }

    /**
     * Get membership status badge color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'active' => 'success',
            'inactive' => 'secondary',
            'suspended' => 'warning',
            'expired' => 'danger',
            default => 'dark'
        };
    }

    /**
     * Scope for active members
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for expired memberships
     */
    public function scopeExpired($query)
    {
        return $query->where('membership_end_date', '<', now());
    }

    /**
     * Scope for expiring soon (within 30 days)
     */
    public function scopeExpiringSoon($query)
    {
        return $query->whereBetween('membership_end_date', [now(), now()->addDays(30)]);
    }
}