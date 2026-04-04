<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Withdrawal extends Model
{
    protected $fillable = [
        'user_id', 'amount', 'method', 'wallet_address', 'network',
        'status', 'admin_note', 'transaction_hash',
        'receipt_note', 'receipt_hash',
        'requested_at', 'processed_at',
    ];

    protected $casts = [
        'requested_at' => 'datetime',
        'processed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function networkLabel(): string
    {
        return match($this->network ?? $this->method ?? '') {
            'trc20', 'usdt_trc20' => 'USDT (TRC20)',
            'bep20', 'usdt_bep20' => 'USDT (BEP20)',
            default => strtoupper($this->network ?? $this->method ?? '—'),
        };
    }
}
