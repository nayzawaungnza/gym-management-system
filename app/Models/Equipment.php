<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Carbon\Carbon;

class Equipment extends Model
{
    use Uuids, SoftDeletes, LogsActivity;

    protected $table = 'equipment';

    protected $fillable = [
        'equipment_name',
        'brand',
        'model',
        'serial_number',
        'category',
        'status',
        'purchase_date',
        'purchase_price',
        'warranty_expiry_date',
        'last_maintenance_date',
        'next_maintenance_date',
        'maintenance_interval_days',
        'location',
        'specifications',
        'notes',
        'is_active'
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'warranty_expiry_date' => 'date',
        'last_maintenance_date' => 'date',
        'next_maintenance_date' => 'date',
        'purchase_price' => 'decimal:2',
        'specifications' => 'array',
        'is_active' => 'boolean'
    ];

    protected $dates = [
        'purchase_date',
        'warranty_expiry_date',
        'last_maintenance_date',
        'next_maintenance_date'
    ];

    // Constants for categories and statuses
    const CATEGORIES = [
        'Cardio' => 'Cardio',
        'Strength' => 'Strength',
        'Free Weights' => 'Free Weights',
        'Functional' => 'Functional',
        'Other' => 'Other'
    ];

    const STATUSES = [
        'Operational' => 'Operational',
        'Under Maintenance' => 'Under Maintenance',
        'Out of Service' => 'Out of Service'
    ];

    /**
     * Activity log options
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'equipment_name', 'brand', 'model', 'serial_number', 
                'category', 'status', 'purchase_date', 'purchase_price',
                'location', 'notes'
            ])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * Relationships
     */
    public function maintenanceRecords(): HasMany
    {
        return $this->hasMany(EquipmentMaintenance::class);
    }

    /**
     * Scopes
     */
    public function scopeOperational($query)
    {
        return $query->where('status', 'Operational');
    }

    public function scopeUnderMaintenance($query)
    {
        return $query->where('status', 'Under Maintenance');
    }

    public function scopeOutOfService($query)
    {
        return $query->where('status', 'Out of Service');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeByCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function scopeMaintenanceDue($query)
    {
        return $query->where('next_maintenance_date', '<=', now());
    }

    public function scopeMaintenanceOverdue($query)
    {
        return $query->where('next_maintenance_date', '<', now());
    }

    public function scopeWarrantyExpiring($query, $days = 30)
    {
        return $query->whereBetween('warranty_expiry_date', [
            now(),
            now()->addDays($days)
        ]);
    }

    /**
     * Accessors & Mutators
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'Operational' => 'success',
            'Under Maintenance' => 'warning',
            'Out of Service' => 'danger',
            default => 'secondary'
        };
    }

    public function getCategoryIconAttribute(): string
    {
        return match($this->category) {
            'Cardio' => 'fas fa-heartbeat',
            'Strength' => 'fas fa-dumbbell',
            'Free Weights' => 'fas fa-weight-hanging',
            'Functional' => 'fas fa-running',
            default => 'fas fa-cog'
        };
    }

    public function getFormattedPurchasePriceAttribute(): string
    {
        return $this->purchase_price ? '$' . number_format($this->purchase_price, 2) : 'N/A';
    }

    public function getAgeInYearsAttribute(): ?int
    {
        return $this->purchase_date ? $this->purchase_date->diffInYears(now()) : null;
    }

    public function getDaysUntilMaintenanceAttribute(): ?int
    {
        if (!$this->next_maintenance_date) {
            return null;
        }

        return now()->diffInDays($this->next_maintenance_date, false);
    }

    public function getWarrantyStatusAttribute(): string
    {
        if (!$this->warranty_expiry_date) {
            return 'No Warranty';
        }

        if ($this->warranty_expiry_date->isPast()) {
            return 'Expired';
        }

        $daysLeft = now()->diffInDays($this->warranty_expiry_date);
        
        if ($daysLeft <= 30) {
            return 'Expiring Soon';
        }

        return 'Active';
    }

    /**
     * Helper methods
     */
    public function needsMaintenance(): bool
    {
        return $this->next_maintenance_date && $this->next_maintenance_date->isPast();
    }

    public function isOperational(): bool
    {
        return $this->status === 'Operational';
    }

    public function isUnderMaintenance(): bool
    {
        return $this->status === 'Under Maintenance';
    }

    public function isOutOfService(): bool
    {
        return $this->status === 'Out of Service';
    }

    public function hasWarranty(): bool
    {
        return $this->warranty_expiry_date && $this->warranty_expiry_date->isFuture();
    }

    public function getSpecification(string $key): mixed
    {
        return $this->specifications[$key] ?? null;
    }

    public function setSpecification(string $key, mixed $value): void
    {
        $specifications = $this->specifications ?? [];
        $specifications[$key] = $value;
        $this->specifications = $specifications;
    }

    /**
     * Calculate next maintenance date based on interval
     */
    public function calculateNextMaintenanceDate(): ?Carbon
    {
        if (!$this->maintenance_interval_days || !$this->last_maintenance_date) {
            return null;
        }

        return $this->last_maintenance_date->addDays($this->maintenance_interval_days);
    }

    /**
     * Update maintenance schedule
     */
    public function updateMaintenanceSchedule(Carbon $maintenanceDate): void
    {
        $this->update([
            'last_maintenance_date' => $maintenanceDate,
            'next_maintenance_date' => $this->calculateNextMaintenanceDate(),
            'status' => 'Operational'
        ]);
    }
}