<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdvertiserProfile extends Model
{
    protected $fillable = [
        'user_id', 'company_name', 'website', 'telegram', 'whatsapp',
        'balance', 'total_spent', 'admin_note',
    ];

    protected function casts(): array
    {
        return [
            'balance'     => 'decimal:4',
            'total_spent' => 'decimal:4',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
