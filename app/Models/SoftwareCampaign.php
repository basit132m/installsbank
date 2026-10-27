<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SoftwareCampaign extends Model
{
    protected $fillable = [
        'user_id', 'name', 'code', 'status', 'enabled',
        'payload_path', 'payload_original_name', 'payload_size',
        'install_mode', 'total_installs', 'admin_note',
    ];

    protected $casts = [
        'enabled'       => 'boolean',
        'payload_size'  => 'integer',
        'total_installs'=> 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function installs()
    {
        return $this->hasMany(SoftwareInstall::class);
    }

    /** Which timestamp counts as the "install" for this campaign's mode. */
    public function countColumn(): string
    {
        return $this->install_mode === 'open' ? 'opened_at' : 'installed_at';
    }

    public function hasPayload(): bool
    {
        return $this->payload_path && \Illuminate\Support\Facades\Storage::disk('local')->exists($this->payload_path);
    }

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            if (!$model->code) {
                do {
                    $code = Str::upper(Str::random(3)) . rand(1000, 9999) . Str::upper(Str::random(2));
                } while (static::where('code', $code)->exists());
                $model->code = $code;
            }
        });
    }
}
