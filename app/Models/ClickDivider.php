<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClickDivider extends Model
{
    protected $fillable = ['user_id', 'divider_value', 'is_enabled'];
    protected $casts = ['is_enabled' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
