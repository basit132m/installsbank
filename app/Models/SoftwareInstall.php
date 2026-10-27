<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareInstall extends Model
{
    protected $fillable = [
        'software_campaign_id', 'token', 'ip_address',
        'country_code', 'country_name', 'device_type', 'os', 'os_version',
        'opened_at', 'installed_at',
    ];

    protected $casts = [
        'opened_at'    => 'datetime',
        'installed_at' => 'datetime',
    ];

    public function campaign()
    {
        return $this->belongsTo(SoftwareCampaign::class, 'software_campaign_id');
    }
}
