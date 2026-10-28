<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SoftwarePayment extends Model
{
    protected $fillable = ['software_campaign_id', 'amount', 'note'];

    protected $casts = ['amount' => 'decimal:2'];

    public function campaign()
    {
        return $this->belongsTo(SoftwareCampaign::class, 'software_campaign_id');
    }
}
