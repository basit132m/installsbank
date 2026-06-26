<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanderHop extends Model
{
    protected $fillable = ['code', 'domain_id', 'position'];

    public function domain()
    {
        return $this->belongsTo(TrackingDomain::class, 'domain_id');
    }

    public static function generateCode(): string
    {
        do {
            $code = strtoupper(
                substr(str_replace(['+', '/', '='], '', base64_encode(random_bytes(8))), 0, 10)
            );
        } while (static::where('code', $code)->exists());

        return $code;
    }
}
