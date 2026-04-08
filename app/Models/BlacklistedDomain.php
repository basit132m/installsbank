<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BlacklistedDomain extends Model
{
    protected $fillable = ['domain', 'reason'];

    /** Normalise a domain string — strip www. and lowercase */
    public static function normalise(string $domain): string
    {
        $domain = strtolower(trim($domain));
        $domain = preg_replace('#^https?://#', '', $domain);
        $domain = preg_replace('#/.*$#', '', $domain);
        $domain = preg_replace('/^www\./', '', $domain);
        return $domain;
    }
}
