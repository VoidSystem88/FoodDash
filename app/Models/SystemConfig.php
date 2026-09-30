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
        'ai_icon_path',
        'ai_icon_type',
        'ai_icon_size',
    ];

    protected $casts = [
        'town_center_lat' => 'float',
        'town_center_lng' => 'float',
        'service_radius_km' => 'float',
        'default_delivery_fee' => 'float',
        'commission_rate' => 'float',
        'logo_height' => 'integer',
        'ai_icon_size' => 'integer',
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
            'ai_icon_size' => 56,
            'ai_icon_type' => 'default',
        ]);
    }

    // ============================================
    // LOGO ACCESSORS
    // ============================================

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

    public function getActiveLogoUrlAttribute(): ?string
    {
        if (request()->cookie('theme') === 'dark' || session('theme') === 'dark') {
            if ($this->hasDarkLogo()) {
                return $this->dark_logo_url;
            }
        }

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

    public function getCommissionDecimalAttribute(): float
    {
        return ($this->commission_rate ?? 0) / 100;
    }

    // ============================================
    // AI ICON ACCESSORS
    // ============================================

    public function getAiIconUrlAttribute(): ?string
    {
        if (!$this->ai_icon_path) return null;

        $path = storage_path('app/public/' . $this->ai_icon_path);
        if (!file_exists($path)) return null;

        return asset('storage/' . $this->ai_icon_path);
    }

    public function hasCustomAiIcon(): bool
    {
        return $this->ai_icon_type === 'custom' && $this->ai_icon_url !== null;
    }

    public function getPresetAiIconSvgAttribute(): ?string
    {
        return match ($this->ai_icon_type) {
            'sparkle' => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z"/></svg>',
            'bot'     => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"/></svg>',
            'chat'    => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>',
            'magic'   => '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M15 4V2m0 20v-2M8 9l-2-2m12 12l-2-2M4 15H2m20 0h-2m-3.42-8.42l1.42-1.42M5.42 18.42L4 20M9 21h6M12 8a4 4 0 00-4 4c0 1.5.5 2 1 3h6c.5-1 1-1.5 1-3a4 4 0 00-4-4z"/></svg>',
            default   => null,
        };
    }

    /**
     * Get the AI icon HTML — WALANG inline size styles.
     * Ang sizing ay hawak ng CSS classes sa ai-bubble.blade.php
     */
    public function getAiIconHtmlAttribute(): string
    {
        // Custom uploaded icon
        if ($this->hasCustomAiIcon()) {
            return '<img src="' . e($this->ai_icon_url) . '" alt="AI" class="ai-bubble-icon-img">';
        }

        // Preset SVG
        if ($this->preset_ai_icon_svg) {
            return '<span class="ai-bubble-icon-wrap">' . $this->preset_ai_icon_svg . '</span>';
        }

        // Fallback: default sparkle SVG
        return '<svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="ai-bubble-icon-svg"><path stroke-linecap="round" stroke-linejoin="round" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"/></svg>';
    }

    public function getAiIconSizePxAttribute(): int
    {
        return max(40, min(80, $this->ai_icon_size ?? 56));
    }
}