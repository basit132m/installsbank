<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminNotification extends Model
{
    protected $fillable = ['type', 'title', 'body', 'link', 'read_at'];

    protected $casts = ['read_at' => 'datetime'];

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    /** Convenience creator used across the app. */
    public static function push(string $type, string $title, ?string $body = null, ?string $link = null): void
    {
        static::create(compact('type', 'title', 'body', 'link'));
    }
}
