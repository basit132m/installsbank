<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class EmailReply extends Model
{
    protected $fillable = [
        'message_uid',
        'from_email',
        'from_name',
        'subject',
        'body',
        'received_at',
        'is_read',
        'matched_log_id',
    ];

    protected $casts = [
        'received_at' => 'datetime',
        'is_read'     => 'boolean',
    ];

    public function matchedLog()
    {
        return $this->belongsTo(BroadcastEmailLog::class, 'matched_log_id');
    }
}
