<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class SoftwareCampaign extends Model
{
    protected $fillable = [
        'user_id', 'name', 'download_name', 'icon_path', 'icon_original_name', 'code', 'status', 'enabled',
        'payload_path', 'payload_original_name', 'payload_size',
        'install_mode', 'max_installs', 'offered_rate', 'admin_rate', 'final_rate',
        'total_installs', 'admin_note',
    ];

    protected $casts = [
        'enabled'        => 'boolean',
        'payload_size'   => 'integer',
        'total_installs' => 'integer',
        'max_installs'   => 'integer',
        'offered_rate'   => 'decimal:4',
        'admin_rate'     => 'decimal:4',
        'final_rate'     => 'decimal:4',
    ];

    // Workflow statuses
    public const S_PENDING  = 'pending_review'; // advertiser submitted, awaiting admin
    public const S_OFFERED  = 'rate_offered';   // admin countered, awaiting advertiser
    public const S_APPROVED = 'approved';       // rate agreed, awaiting admin to go live
    public const S_LIVE     = 'live';           // serving installs
    public const S_PAUSED   = 'paused';
    public const S_COMPLETED= 'completed';       // cap reached

    public function remainingInstalls(): int
    {
        if ($this->max_installs <= 0) {
            return PHP_INT_MAX; // no cap
        }
        return max(0, $this->max_installs - $this->total_installs);
    }

    public function capReached(): bool
    {
        return $this->max_installs > 0 && $this->total_installs >= $this->max_installs;
    }

    public function progressPercent(): int
    {
        if ($this->max_installs <= 0) {
            return 0;
        }
        return (int) min(100, round($this->total_installs / $this->max_installs * 100));
    }

    /** Per-install rate that applies (agreed rate, else the offer). */
    public function effectiveRate(): float
    {
        return (float) ($this->final_rate ?? $this->offered_rate);
    }

    public function statusLabel(): string
    {
        return match ($this->status) {
            self::S_PENDING  => 'Under review',
            self::S_OFFERED  => 'Rate offered — your approval needed',
            self::S_APPROVED => 'Approved — going live soon',
            self::S_LIVE     => 'Live',
            self::S_PAUSED   => 'Paused',
            self::S_COMPLETED=> 'Completed',
            default          => ucfirst(str_replace('_', ' ', (string) $this->status)),
        };
    }

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

    /** Storage path where this campaign's branded (icon-baked) stub lives. */
    public function brandedStubRelPath(): string
    {
        return 'software-stub/campaigns/' . $this->code . '.exe';
    }

    public function hasBrandedStub(): bool
    {
        return \Illuminate\Support\Facades\Storage::disk('local')->exists($this->brandedStubRelPath());
    }

    public function hasIcon(): bool
    {
        return $this->icon_path && \Illuminate\Support\Facades\Storage::disk('local')->exists($this->icon_path);
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
