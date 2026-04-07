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
        $map = [
            'usdt_trc20'  => 'USDT (TRC20)',
            'trc20'       => 'USDT (TRC20)',
            'usdt_bep20'  => 'USDT (BEP20)',
            'bep20'       => 'USDT (BEP20)',
            'usdt_erc20'  => 'USDT (ERC20)',
            'erc20'       => 'USDT (ERC20)',
            'usdt_ton'    => 'USDT (TON)',
            'btc'         => 'Bitcoin (BTC)',
            'eth'         => 'Ethereum (ETH)',
            'bnb'         => 'BNB (BEP20)',
            'trx'         => 'TRON (TRX)',
            'sol'         => 'Solana (SOL)',
            'ltc'         => 'Litecoin (LTC)',
            'xrp'         => 'XRP (Ripple)',
            'usdc_erc20'  => 'USDC (ERC20)',
            'usdc_bep20'  => 'USDC (BEP20)',
            'usdc_sol'    => 'USDC (Solana)',
        ];
        $key = $this->network ?? $this->method ?? '';
        return $map[$key] ?? strtoupper($key) ?: '—';
    }
}
