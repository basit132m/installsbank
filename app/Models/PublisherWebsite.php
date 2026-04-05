<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublisherWebsite extends Model
{
    protected $fillable = [
        'user_id', 'website_url', 'domain', 'status',
        'tracking_link_id', 'stat_screenshots',
        'rejection_reason', 'reviewed_by', 'reviewed_at',
    ];

    protected $casts = [
        'stat_screenshots' => 'array',
        'reviewed_at'      => 'datetime',
    ];

    public function user()       { return $this->belongsTo(User::class); }
    public function trackingLink(){ return $this->belongsTo(TrackingLink::class); }
    public function reviewer()   { return $this->belongsTo(User::class, 'reviewed_by'); }

    public function isPending()  { return $this->status === 'pending'; }
    public function isApproved() { return $this->status === 'approved'; }
    public function isRejected() { return $this->status === 'rejected'; }
}
