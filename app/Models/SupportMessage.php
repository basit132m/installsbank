<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SupportMessage extends Model
{
    protected $fillable = ['ticket_id', 'user_id', 'message', 'is_read', 'is_staff'];
    protected $casts = ['is_read' => 'boolean'];

    public function ticket()
    {
        return $this->belongsTo(SupportTicket::class);
    }

    public function sender()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
