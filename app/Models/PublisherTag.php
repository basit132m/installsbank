<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PublisherTag extends Model
{
    protected $fillable = ['user_id', 'tag', 'color'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
