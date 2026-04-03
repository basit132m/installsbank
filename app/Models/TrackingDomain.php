<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingDomain extends Model
{
    protected $fillable = ['domain', 'label', 'is_active'];

    protected $casts = ['is_active' => 'boolean'];

    public function trackingLinks()
    {
        return $this->hasMany(TrackingLink::class);
    }

    /** Returns the domain with https:// for URL building */
    public function getBaseUrlAttribute(): string
    {
        return 'https://' . rtrim($this->domain, '/');
    }
}
