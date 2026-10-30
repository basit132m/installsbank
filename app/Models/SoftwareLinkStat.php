<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwareLinkStat extends Model
{
    protected $fillable = [
        'software_link_id', 'software_campaign_id',
        'country_code', 'country_name', 'device_type', 'served', 'fallback',
    ];
}
