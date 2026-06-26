<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BroadcastEmailLog extends Model
{
    protected $fillable = [
        'sent_by',
        'batch_id',
        'from_email',
        'recipient_email',
        'subject',
        'status',
        'error_message',
    ];

    public function sender()
    {
        return $this->belongsTo(User::class, 'sent_by');
    }
}
