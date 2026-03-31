<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdButton extends Model
{
    protected $fillable = ['user_id', 'tracking_link_id', 'ad_preset_id', 'custom_text', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trackingLink()
    {
        return $this->belongsTo(TrackingLink::class);
    }

    public function preset()
    {
        return $this->belongsTo(AdPreset::class, 'ad_preset_id');
    }

    public function getEmbedCode(): string
    {
        $preset = $this->preset;
        $trackingUrl = $this->trackingLink->tracking_url;
        $text = $this->custom_text ?: $preset->button_text;
        $color = $preset->button_color;
        $textColor = $preset->button_text_color;

        $sizeMap = ['small' => '12px 24px', 'medium' => '14px 32px', 'large' => '16px 40px'];
        $padding = $sizeMap[$preset->button_size] ?? '14px 32px';

        $radiusMap = ['rounded' => '8px', 'square' => '0px', 'pill' => '50px'];
        $radius = $radiusMap[$preset->button_style] ?? '8px';

        return '<a href="' . $trackingUrl . '" target="_blank" rel="noopener" '
            . 'style="display:inline-block;background:' . $color . ';color:' . $textColor . ';'
            . 'padding:' . $padding . ';border-radius:' . $radius . ';font-family:Arial,sans-serif;'
            . 'font-size:' . ($preset->button_size === 'small' ? '13px' : ($preset->button_size === 'large' ? '16px' : '14px')) . ';'
            . 'font-weight:600;text-decoration:none;cursor:pointer;" '
            . 'data-ib-btn="1">' . htmlspecialchars($text) . '</a>';
    }
}
