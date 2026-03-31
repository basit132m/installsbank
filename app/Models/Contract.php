<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contract extends Model
{
    protected $fillable = [
        'user_id', 'type', 'rate', 'status',
        'test_total_clicks', 'test_started_at', 'test_ended_at',
        'admin_note', 'offered_at', 'responded_at',
    ];

    protected $casts = [
        'test_started_at' => 'datetime',
        'test_ended_at' => 'datetime',
        'offered_at' => 'datetime',
        'responded_at' => 'datetime',
    ];

    public function publisher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function isPending(): bool { return $this->status === 'pending'; }
    public function isAccepted(): bool { return $this->status === 'accepted'; }
}
