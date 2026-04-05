<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CampaignPayment extends Model
{
    protected $fillable = [
        'campaign_id', 'user_id', 'type', 'amount',
        'status', 'payment_method', 'transaction_id',
        'notes', 'confirmed_by', 'confirmed_at',
    ];

    protected function casts(): array
    {
        return [
            'confirmed_at' => 'datetime',
            'amount'       => 'decimal:4',
        ];
    }

    public function campaign()
    {
        return $this->belongsTo(Campaign::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function confirmedBy()
    {
        return $this->belongsTo(User::class, 'confirmed_by');
    }
}
