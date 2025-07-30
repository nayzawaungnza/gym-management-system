<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MemberSubscription extends Model
{
    use HasFactory, SoftDeletes, Uuids;

    protected $fillable = [
        'member_id',
        'membership_type_id',
        'start_date',
        'end_date',
        'amount_paid',
        'status',
        'auto_renew',
        'renewal_date',
        'cancellation_reason',
        'cancelled_by',
        'previous_subscription_id',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'renewal_date' => 'date',
        'amount_paid' => 'decimal:2',
        'auto_renew' => 'boolean',
        //'status' => 'enum:active,expired,cancelled,suspended',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function membershipType()
    {
        return $this->belongsTo(MembershipType::class);
    }

    public function cancelledBy()
    {
        return $this->belongsTo(User::class, 'cancelled_by');
    }

    public function previousSubscription()
    {
        return $this->belongsTo(MemberSubscription::class, 'previous_subscription_id');
    }

    public function renewedInto()
    {
        return $this->belongsTo(MemberSubscription::class, 'renewed_into_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'member_subscription_id');
    }

    // Check if subscription is active
    public function isActive()
    {
        return $this->status === 'active' && $this->end_date->isFuture();
    }

    // Check if subscription is expired
    public function isExpired()
    {
        return $this->end_date->isPast();
    }

    // Check if subscription is expiring soon (within 7 days)
    public function isExpiringSoon()
    {
        return $this->end_date->isBetween(now(), now()->addDays(7));
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('end_date', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->where('status', 'expired')
                ->where('end_date', '<', now());
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'cancelled');
    }

    public function scopeSuspended($query)
    {
        return $query->where('status', 'suspended');
    }
}