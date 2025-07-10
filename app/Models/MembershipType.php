<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class MembershipType extends Model
{
    use Uuids, SoftDeletes;

    protected $fillable = [
        'type_name',
        'duration_months',
        'price',
        'description',
        'is_active'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'duration_months' => 'integer'
    ];

    public function members(): HasMany
    {
        return $this->hasMany(Member::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
