<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TestPeriod extends Model
{
    protected $fillable = [
        'user_id', 'started_at', 'ended_at',
        'admin_entered_clicks', 'status', 'admin_notes',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function publisher()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
