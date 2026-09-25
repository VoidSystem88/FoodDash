<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use NotificationChannels\WebPush\HasPushSubscriptions;

class User extends Authenticatable
{
        use HasFactory, Notifiable, HasPushSubscriptions;


    protected $fillable = [
        'name',
        'email',
        'email_verified_at',
        'password',
        'role',
        'status',
        'phone',
        'otp_code',
        'otp_expires_at',
        'otp_verified_at',
        'otp_attempts',
        'password_reset_otp',
        'password_reset_otp_expires_at',
        'password_reset_otp_verified_at',
        'password_reset_otp_attempts',
    ];

    protected $hidden = [
        'password',
        'remember_token',
        'otp_code',
        'password_reset_otp',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'otp_expires_at' => 'datetime',
            'otp_verified_at' => 'datetime',
            'password_reset_otp_expires_at' => 'datetime',
            'password_reset_otp_verified_at' => 'datetime',
            'last_seen_at' => 'datetime',
        ];
    }

    public function restaurant()
    {
        return $this->hasOne(Restaurant::class);
    }

    public function rider()
    {
        return $this->hasOne(Rider::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }
	public function favorites()
{
    return $this->hasMany(Favorite::class);
}

public function favoriteRestaurants()
{
    return $this->belongsToMany(Restaurant::class, 'favorites')->withTimestamps();
}

public function hasFavorited(Restaurant $restaurant): bool
{
    return $this->favorites()->where('restaurant_id', $restaurant->id)->exists();
}

    public function isCustomer(): bool
    {
        return $this->role === 'customer';
    }

    public function isRestaurant(): bool
    {
        return $this->role === 'restaurant';
    }

    public function isRider(): bool
    {
        return $this->role === 'rider';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // ========================================
    // EMAIL VERIFICATION OTP
    // ========================================

    public function generateOtp(): string
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->update([
            'otp_code' => $otp,
            'otp_expires_at' => now()->addMinutes(10),
            'otp_attempts' => 0,
        ]);

        return $otp;
    }

    public function hasValidOtp(): bool
    {
        return $this->otp_code
            && $this->otp_expires_at
            && $this->otp_expires_at->isFuture();
    }

    public function verifyOtp(string $input): bool
    {
        if (!$this->hasValidOtp()) {
            return false;
        }

        if ($this->otp_attempts >= 5) {
            return false;
        }

        if ($this->otp_code !== $input) {
            $this->increment('otp_attempts');
            return false;
        }

        $this->update([
            'otp_verified_at' => now(),
            'otp_code' => null,
            'otp_expires_at' => null,
            'otp_attempts' => 0,
            'email_verified_at' => now(),
        ]);

        return true;
    }

    public function hasVerifiedEmail(): bool
    {
        return !is_null($this->email_verified_at);
    }

    // ========================================
    // PASSWORD RESET OTP
    // ========================================

    public function generatePasswordResetOtp(): string
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $this->update([
            'password_reset_otp' => $otp,
            'password_reset_otp_expires_at' => now()->addMinutes(10),
            'password_reset_otp_attempts' => 0,
            'password_reset_otp_verified_at' => null,
        ]);

        return $otp;
    }

    public function hasValidPasswordResetOtp(): bool
    {
        return $this->password_reset_otp
            && $this->password_reset_otp_expires_at
            && $this->password_reset_otp_expires_at->isFuture();
    }

    public function verifyPasswordResetOtp(string $input): bool
    {
        if (!$this->hasValidPasswordResetOtp()) {
            return false;
        }

        if ($this->password_reset_otp_attempts >= 5) {
            return false;
        }

        if ($this->password_reset_otp !== $input) {
            $this->increment('password_reset_otp_attempts');
            return false;
        }

        $this->update([
            'password_reset_otp_verified_at' => now(),
            'password_reset_otp_attempts' => 0,
        ]);

        return true;
    }

    public function clearPasswordResetOtp(): void
    {
        $this->update([
            'password_reset_otp' => null,
            'password_reset_otp_expires_at' => null,
            'password_reset_otp_verified_at' => null,
            'password_reset_otp_attempts' => 0,
        ]);
    }

    public function hasVerifiedPasswordResetOtp(): bool
    {
        return !is_null($this->password_reset_otp_verified_at)
            && $this->password_reset_otp_verified_at->diffInMinutes(now()) <= 15;
    }
    public function getAvatarUrlAttribute(): string
{
    if (!$this->avatar) {
        return '';
    }

    $path = storage_path('app/public/' . $this->avatar);

    if (!file_exists($path)) {
        return '';
    }

    return asset('storage/' . $this->avatar);
}

    public function getInitialsAttribute(): string
    {
        $name = trim($this->name);
        $parts = explode(' ', $name);
        $initials = '';

        foreach ($parts as $part) {
            if (!empty($part)) {
                $initials .= strtoupper($part[0]);
                if (strlen($initials) >= 2) break;
            }
        }

        return $initials ?: 'U';
    }

    public function getAvatarColorAttribute(): string
    {
        $colors = [
            'bg-red-500', 'bg-orange-500', 'bg-amber-500',
            'bg-yellow-500', 'bg-lime-500', 'bg-green-500',
            'bg-emerald-500', 'bg-teal-500', 'bg-cyan-500',
            'bg-sky-500', 'bg-blue-500', 'bg-indigo-500',
            'bg-violet-500', 'bg-purple-500', 'bg-fuchsia-500',
            'bg-pink-500', 'bg-rose-500',
        ];

        return $colors[$this->id % count($colors)];
    }

    public function isOnline(): bool
{
    if (!$this->last_seen_at) {
        return false;
    }

    // Ensure na Carbon instance
    $lastSeen = $this->last_seen_at instanceof \Carbon\Carbon
        ? $this->last_seen_at
        : \Carbon\Carbon::parse($this->last_seen_at);

    return $lastSeen->diffInMinutes(now()) < 5;
}
}