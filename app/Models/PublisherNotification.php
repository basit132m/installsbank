<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublisherNotification extends Model
{
    protected $fillable = ['user_id', 'type', 'message', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function isUnread(): bool
    {
        return $this->read_at === null;
    }
}
