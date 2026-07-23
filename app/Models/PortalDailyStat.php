<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PortalDailyStat extends Model
{
    protected $fillable = [
        'portal_account_id', 'date', 'shown_clicks', 'raw_base', 'cursor_at',
        'country_breakdown', 'finalized',
    ];

    protected $casts = [
        'date'              => 'date',
        'shown_clicks'      => 'integer',
        'raw_base'          => 'integer',
        'cursor_at'         => 'datetime',
        'country_breakdown' => 'array',
        'finalized'         => 'boolean',
    ];

    public function account()
    {
        return $this->belongsTo(PortalAccount::class, 'portal_account_id');
    }
}
