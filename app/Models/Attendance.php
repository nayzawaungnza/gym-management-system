<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Attendance extends Model
{
    use Uuids, SoftDeletes;

    protected $table = 'attendances';
    protected $fillable = [
        'member_id',
'check_in_time',
'check_out_time',
'duration_minutes',
'check_in_method',
'location',
'notes'
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime'
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function getDurationAttribute(): ?int
    {
        if ($this->check_out_time) {
            return $this->check_in_time->diffInMinutes($this->check_out_time);
        }
        return null;
    }

    public function isCheckedOut(): bool
    {
        return !is_null($this->check_out_time);
    }
}