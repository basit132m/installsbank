<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContactFormSubmission extends Model
{
    protected $fillable = ['url', 'sender_name', 'sender_email', 'status', 'note'];
}
