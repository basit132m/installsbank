<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SoftwareCampaign extends Model
{
    protected $fillable = [
        'user_id', 'name', 'download_name', 'code', 'status', 'enabled',
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

    /**
     * Clean, user-facing base name for the download file. No underscores
     * (that's the stub's token delimiter), no unsafe filename characters.
     * e.g. "Font-Manager-Setup" → served as "Font-Manager-Setup_<token>.exe".
     */
    public function downloadBaseName(): string
    {
        $base = $this->download_name ?: ($this->name ?: 'Setup');
        $base = preg_replace('/[^A-Za-z0-9\-]+/', '-', $base); // spaces/underscores/etc → hyphen
        $base = trim(preg_replace('/-+/', '-', $base), '-');    // collapse + trim hyphens
        return $base !== '' ? $base : 'Setup';
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
