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
        'logo_path',
        'logo_height',
    ];

    protected $casts = [
        'town_center_lat' => 'float',
        'town_center_lng' => 'float',
        'service_radius_km' => 'float',
        'default_delivery_fee' => 'float',
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
            'logo_height' => 40,
        ]);
    }

    /**
     * Kunin ang public URL ng logo.
     */
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

    /**
     * Check kung may custom logo.
     */
    public function hasLogo(): bool
    {
        return $this->logo_url !== null;
    }

    /**
     * Kunin ang logo height (clamped between 24-60px).
     */
    public function getLogoHeightPxAttribute(): int
    {
        $height = $this->logo_height ?? 40;

        // Clamp between 24 and 60
        return max(24, min(60, $height));
    }
}