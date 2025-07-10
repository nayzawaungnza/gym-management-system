<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClassRegistration extends Model
{
    use Uuids, SoftDeletes;

    protected $fillable = [
        'member_id',
        'class_id',
        'registration_date',
        'status'
    ];

    protected $casts = [
        'registration_date' => 'datetime'
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function gymClass(): BelongsTo
    {
        return $this->belongsTo(GymClass::class, 'class_id');
    }

    public function scopeRegistered($query)
    {
        return $query->where('status', 'Registered');
    }

    public function scopeAttended($query)
    {
        return $query->where('status', 'Attended');
    }

    public function scopeCancelled($query)
    {
        return $query->where('status', 'Cancelled');
    }
}
