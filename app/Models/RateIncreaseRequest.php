<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RateIncreaseRequest extends Model
{
    protected $fillable = [
        'user_id',
        'current_rate',
        'requested_rate',
        'justification',
        'stats_screenshots',
        'status',
        'approved_rate',
        'effective_from',
        'admin_note',
        'responded_at',
    ];

    protected $casts = [
        'stats_screenshots' => 'array',
        'effective_from'    => 'date',
        'responded_at'      => 'datetime',
        'current_rate'      => 'decimal:4',
        'requested_rate'    => 'decimal:4',
        'approved_rate'     => 'decimal:4',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
