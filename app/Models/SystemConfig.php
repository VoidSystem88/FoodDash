<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class SystemConfig extends Model
{
    use HasFactory;

    protected $fillable = [
        'town_address',
        'town_center_lat',
        'town_center_lng',
        'service_radius_km',
        'default_delivery_fee',
        'commission_rate',
        'logo_path',
        'light_logo_path', 
        'dark_logo_path',
        'logo_height',
    ];

    protected $casts = [
        'town_center_lat' => 'float',
        'town_center_lng' => 'float',
        'service_radius_km' => 'float',
        'default_delivery_fee' => 'float',
        'commission_rate' => 'float',
        'logo_height' => 'integer',
    ];

    public static function current(): self
    {
        return static::first() ?? static::create([
            'town_address' => 'Cagayan de Oro City',
            'town_center_lat' => 8.4822,
            'town_center_lng' => 124.6472,
            'service_radius_km' => 10,
            'default_delivery_fee' => 60,
            'commission_rate' => 10,
            'logo_height' => 40,
        ]);
    }
public function getLightLogoUrlAttribute(): ?string
{
    if (!$this->light_logo_path) return null;

    $path = storage_path('app/public/' . $this->light_logo_path);
    if (!file_exists($path)) return null;

    return asset('storage/' . $this->light_logo_path);
}

public function hasLightLogo(): bool
{
    return $this->light_logo_url !== null;
}

public function getDarkLogoUrlAttribute(): ?string
{
    if (!$this->dark_logo_path) return null;

    $path = storage_path('app/public/' . $this->dark_logo_path);
    if (!file_exists($path)) return null;

    return asset('storage/' . $this->dark_logo_path);
}

public function hasDarkLogo(): bool
{
    return $this->dark_logo_url !== null;
}

/**
 * Kunin ang tamang logo URL base sa current theme.
 */
public function getActiveLogoUrlAttribute(): ?string
{
    // Dark mode
    if (request()->cookie('theme') === 'dark' || session('theme') === 'dark') {
        if ($this->hasDarkLogo()) {
            return $this->dark_logo_url;
        }
    }

    // Light mode o fallback
    if ($this->hasLightLogo()) {
        return $this->light_logo_url;
    }

    return $this->logo_url;
}
    public function getLogoUrlAttribute(): ?string
    {
        if (!$this->logo_path) {
            return null;
        }

        $path = storage_path('app/public/' . $this->logo_path);

        if (!file_exists($path)) {
            return null;
        }

        return asset('storage/' . $this->logo_path);
    }

    public function hasLogo(): bool
    {
        return $this->logo_url !== null;
    }

    public function getLogoHeightPxAttribute(): int
    {
        $height = $this->logo_height ?? 40;
        return max(24, min(60, $height));
    }

    /**
     * Kunin ang commission rate as decimal (e.g. 10 → 0.10)
     */
    public function getCommissionDecimalAttribute(): float
    {
        return ($this->commission_rate ?? 0) / 100;
    }
}