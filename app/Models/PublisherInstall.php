<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublisherInstall extends Model
{
    protected $fillable = [
        'user_id',
        'country_code',
        'country_name',
        'install_count',
        'earnings',
        'date',
    ];

    protected $casts = [
        'date'     => 'date',
        'earnings' => 'decimal:6',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
