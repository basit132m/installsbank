<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractChangeRequest extends Model
{
    protected $fillable = [
        'user_id', 'current_type', 'requested_type', 'reason',
        'status', 'admin_note', 'responded_at',
    ];

    protected $casts = [
        'responded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isPending(): bool
    {
        return $this->status === 'pending';
    }
}
