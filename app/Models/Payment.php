<?php

namespace App\Models;

use App\Traits\Uuids;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Payment extends Model
{
    use Uuids, SoftDeletes;

    protected $table = 'payments';
    protected $fillable = [
        'member_id',
        'membership_type_id',
        'class_registration_id',
        'amount',
        'payment_date',
        'payment_method_id',
        'transaction_id',
        'status',
        'description',
        'receipt_number',
        'processed_by',
        'notes'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'datetime'
    ];

    public function member(): BelongsTo
    {
        return $this->belongsTo(Member::class);
    }

    public function membershipType(): BelongsTo
    {
        return $this->belongsTo(MembershipType::class);
    }
    public function classRegistration(): BelongsTo
    {
        return $this->belongsTo(ClassRegistration::class);
    }

    public function paymentMethod(): BelongsTo
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    
    // public function scopeCompleted($query)
    // {
    //     return $query->where('status', 'completed');
    // }

    // public function scopePending($query)
    // {
    //     return $query->where('status', 'pending');
    // }

    // public function scopeFailed($query)
    // {
    //     return $query->where('status', 'failed');
    // }
    // public function scopeActive($query)
    // {
    //     return $query->where('status', 'active');
    // }
    // public function scopeRefunded($query)
    // {
    //     return $query->where('status', 'refunded');
    // }
    public function getStatusBadgeAttribute(): string
    {
        $colors = [
            'pending' => 'warning',
            'completed' => 'success',
            'failed' => 'danger',
            'refunded' => 'info',
        ];
        $color = $colors[$this->status] ?? 'secondary';

        return '<span class="badge bg-' . $color . '">' . ucfirst($this->status) . '</span>';
    }

}