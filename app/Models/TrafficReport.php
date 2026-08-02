<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrafficReport extends Model
{
    protected $fillable = [
        'generated_by', 'report_no', 'title', 'show_branding', 'prepared_for', 'target',
        'date_from', 'date_to', 'period_label', 'click_type', 'total',
        'os_data', 'geo_data', 'split_data', 'rate', 'payment_terms', 'tracking_code',
    ];

    protected $casts = [
        'date_from'     => 'date',
        'date_to'       => 'date',
        'os_data'       => 'array',
        'geo_data'      => 'array',
        'split_data'    => 'array',
        'total'         => 'integer',
        'show_branding' => 'boolean',
    ];

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
