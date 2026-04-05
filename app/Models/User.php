<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    protected $fillable = [
        'name', 'email', 'password', 'role', 'status',
        'phone', 'telegram', 'website', 'country_code', 'last_login_at', 'avatar', 'fcm_token',
        'stat_screenshots',
    ];

    protected $hidden = ['password', 'remember_token'];

    protected function casts(): array
    {
        return [
            'email_verified_at'  => 'datetime',
            'last_login_at'      => 'datetime',
            'password'           => 'hashed',
            'stat_screenshots'   => 'array',
        ];
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isManager(): bool
    {
        return $this->role === 'manager';
    }

    public function isPublisher(): bool
    {
        return $this->role === 'publisher';
    }

    public function isAdminOrManager(): bool
    {
        return in_array($this->role, ['admin', 'manager']);
    }

    public function isAdvertiser(): bool
    {
        return $this->role === 'advertiser';
    }

    public function advertiserProfile()
    {
        return $this->hasOne(AdvertiserProfile::class);
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class);
    }

    public function publisherProfile()
    {
        return $this->hasOne(PublisherProfile::class);
    }

    public function managerPermissions()
    {
        return $this->hasOne(ManagerPermission::class);
    }

    public function trackingLinks()
    {
        return $this->hasMany(TrackingLink::class);
    }

    public function publisherWebsites()
    {
        return $this->hasMany(PublisherWebsite::class);
    }

    public function clicks()
    {
        return $this->hasMany(Click::class);
    }

    public function clickDivider()
    {
        return $this->hasOne(ClickDivider::class);
    }

    public function contracts()
    {
        return $this->hasMany(Contract::class);
    }

    public function activeContract()
    {
        return $this->hasOne(Contract::class)->where('status', 'accepted')->latest();
    }

    public function dailyEarnings()
    {
        return $this->hasMany(DailyEarning::class);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function supportTickets()
    {
        return $this->hasMany(SupportTicket::class);
    }

    public function fraudAlerts()
    {
        return $this->hasMany(FraudAlert::class);
    }

    public function publisherTags()
    {
        return $this->hasMany(PublisherTag::class);
    }

    public function testPeriods()
    {
        return $this->hasMany(TestPeriod::class);
    }

    public function adButtons()
    {
        return $this->hasMany(AdButton::class);
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->isAdmin()) return true;
        if (!$this->isManager()) return false;
        $perms = $this->managerPermissions;
        return $perms && $perms->$permission ?? false;
    }
}
