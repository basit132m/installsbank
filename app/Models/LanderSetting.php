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
    ];

    protected $casts = [
        'show_password' => 'boolean',
        'show_checks'   => 'boolean',
        'download_count' => 'integer',
    ];

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
