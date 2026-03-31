<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdPreset extends Model
{
    protected $fillable = [
        'name', 'button_text', 'button_color', 'button_text_color',
        'button_size', 'button_style', 'custom_css',
        'show_icon', 'icon_type', 'is_active',
    ];

    protected $casts = [
        'show_icon' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function adButtons()
    {
        return $this->hasMany(AdButton::class);
    }
}
