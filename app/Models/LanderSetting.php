<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanderSetting extends Model
{
    protected $fillable = [
        'mega_url',
        'archive_password',
        'color_scheme',
        'page_title',
        'download_count',
        'show_password',
        'show_checks',
        'active_lander_domain_id',
        'redirect_front_domain_id',
        'redirect_code',
    ];

    protected $casts = [
        'show_password'            => 'boolean',
        'show_checks'              => 'boolean',
        'download_count'           => 'integer',
        'active_lander_domain_id'  => 'integer',
        'redirect_front_domain_id' => 'integer',
    ];

    public function activeLanderDomain()
    {
        return $this->belongsTo(TrackingDomain::class, 'active_lander_domain_id');
    }

    public function redirectFrontDomain()
    {
        return $this->belongsTo(TrackingDomain::class, 'redirect_front_domain_id');
    }

    public static function current(): self
    {
        return static::firstOrCreate(['id' => 1], [
            'mega_url'         => '',
            'archive_password' => '',
            'color_scheme'     => 'dark-red',
            'page_title'       => 'Your File is Ready',
            'download_count'   => 12692,
            'show_password'    => true,
            'show_checks'      => true,
        ]);
    }
}
