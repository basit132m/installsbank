<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrafficReport extends Model
{
    protected $fillable = [
        'generated_by', 'report_no', 'title', 'prepared_for', 'target',
        'date_from', 'date_to', 'click_type', 'total',
        'os_data', 'geo_data', 'rate', 'payment_terms', 'tracking_code',
    ];

    protected $casts = [
        'date_from' => 'date',
        'date_to'   => 'date',
        'os_data'   => 'array',
        'geo_data'  => 'array',
        'total'     => 'integer',
    ];

    public function generatedBy()
    {
        return $this->belongsTo(User::class, 'generated_by');
    }
}
