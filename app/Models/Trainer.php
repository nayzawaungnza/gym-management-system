<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trainer extends Model
{
    use Uuids, SoftDeletes;

    protected $fillable = [
        'user_id',
'trainer_id',
'first_name',
'last_name',
'email',
'phone',
'specialization',
'certifications',
'hire_date',
'hourly_rate',
'bio',
'profile_photo',
'is_active'
    ];

    protected $casts = [
        'hire_date' => 'date',
        'is_active' => 'boolean',
        'certifications' => 'array', // Assuming certifications is stored as a JSON array
    ];

    public function classes(): HasMany
    {
        return $this->hasMany(GymClass::class);
    }
    


    public function getFullNameAttribute(): string
    {
        return $this->first_name . ' ' . $this->last_name;
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeInactive($query)
    {
        return $query->where('is_active', false);
    }
    public function scopeBySpecialization($query, $specialization)
    {
        return $query->where('specialization', 'like', "%{$specialization}%");
    }
    public function scopeByName($query, $name)
    {
        return $query->where(function ($q) use ($name) {
            $q->where('first_name', 'like', "%{$name}%")
              ->orWhere('last_name', 'like', "%{$name}%");
        });
    }
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', 'like', "%{$email}%");
    }
    public function scopeByPhone($query, $phone)
    {
        return $query->where('phone', 'like', "%{$phone}%");
    }
    public function scopeByHireDate($query, $date)
    {
        return $query->whereDate('hire_date', $date);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}