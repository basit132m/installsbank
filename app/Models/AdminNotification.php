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

    /** Convenience creator used across the app. Never throws (best-effort). */
    public static function push(string $type, string $title, ?string $body = null, ?string $link = null): void
    {
        try {
            static::create(compact('type', 'title', 'body', 'link'));
        } catch (\Throwable $e) {
            // Notifications are secondary — never break the primary action over one.
        }
    }
}
