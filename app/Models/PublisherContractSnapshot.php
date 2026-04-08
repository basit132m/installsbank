<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublisherContractSnapshot extends Model
{
    protected $fillable = [
        'user_id', 'contract_type', 'fixed_daily_rate',
        'total_clicks', 'total_earnings', 'changed_to', 'changed_at',
    ];

    protected $casts = [
        'changed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
