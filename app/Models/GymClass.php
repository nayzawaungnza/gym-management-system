<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class GymClass extends Model
{
    use Uuids, SoftDeletes;


    protected $fillable = [
        'trainer_id',
'class_name',
'description',
'class_type',
'schedule_day',
'start_time',
'end_time',
'duration_minutes',
'max_capacity',
'current_capacity',
'price',
'room',
'equipment_needed',
'difficulty_level',
'is_active'
    ];

    protected $casts = [
        'schedule_day' => 'datetime',
        'duration_minutes' => 'integer',
        'max_capacity' => 'integer',
        'current_capacity' => 'integer',
        'is_active' => 'boolean'
    ];

    public function trainer(): BelongsTo
    {
        return $this->belongsTo(Trainer::class);
    }
    public function members(): BelongsToMany
    {
        return $this->belongsToMany(Member::class, 'class_registrations', 'class_id', 'member_id');
    }

    public function classRegistrations(): HasMany
    {
        return $this->hasMany(ClassRegistration::class, 'class_id');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('schedule_day', '>', now());
    }

    public function getAvailableSpotsAttribute(): int
    {
        return $this->max_capacity - $this->current_capacity;
    }

    public function isFull(): bool
    {
        return $this->current_capacity >= $this->max_capacity;
    }
}