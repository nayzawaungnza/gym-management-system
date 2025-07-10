<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class AttendanceVerification extends Model
{
    use Uuids, SoftDeletes;

    protected $fillable = [
        'attendance_id',
        'member_id',
        'verification_method',
        'verification_data',
        'verification_status',
        'location_lat',
        'location_lng',
        'ip_address',
        'device_info',
        'photo_path',
        'qr_token',
        'rfid_code',
        'biometric_hash',
        'confidence_score',
        'is_flagged',
        'flagged_by',
        'flagged_at',
        'flag_reason',
        'approved_by',
        'approved_at',
        'verification_notes'
    ];

    protected $casts = [
        'verification_data' => 'array',
        'location_lat' => 'decimal:8',
        'location_lng' => 'decimal:8',
        'confidence_score' => 'decimal:2',
        'is_flagged' => 'boolean',
        'flagged_at' => 'datetime',
        'approved_at' => 'datetime'
    ];

    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class);
    }

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function flaggedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'flagged_by');
    }

    public function approvedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function scopeFlagged($query)
    {
        return $query->where('is_flagged', true);
    }

    public function scopeApproved($query)
    {
        return $query->where('verification_status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('verification_status', 'rejected');
    }

    public function scopePending($query)
    {
        return $query->where('verification_status', 'pending');
    }
}
