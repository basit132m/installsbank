<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SoftwareLink extends Model
{
    protected $fillable = [
        'software_campaign_id', 'code', 'name', 'fallback_url', 'is_active', 'served', 'rejected',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'served'    => 'integer',
        'rejected'  => 'integer',
    ];

    public function campaign()
    {
        return $this->belongsTo(SoftwareCampaign::class, 'software_campaign_id');
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->code) {
                do {
                    $code = Str::upper(Str::random(3)) . rand(1000, 9999) . Str::upper(Str::random(2));
                } while (static::where('code', $code)->exists());
                $model->code = $code;
            }
        });
    }
}
