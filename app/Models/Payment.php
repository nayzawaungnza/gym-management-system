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

    public function scopeCompleted($query)
    {
        return $query->where('status', 'Completed');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeFailed($query)
    {
        return $query->where('status', 'Failed');
    }
}