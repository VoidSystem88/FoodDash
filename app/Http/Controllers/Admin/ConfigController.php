<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemConfig;
use Illuminate\Http\Request;

class ConfigController extends Controller
{
    private const BRANDING_DIR = 'branding';

    // ============================================
    // SYSTEM CONFIGURATION
    // ============================================

    public function update(Request $request)
    {
        $data = $request->validate([
            'town_address' => 'required|string|max:255',
            'town_center_lat' => 'required|numeric',
            'town_center_lng' => 'required|numeric',
            'service_radius_km' => 'required|numeric|min:1',
            'default_delivery_fee' => 'required|numeric|min:0',
            'commission_rate' => 'required|numeric|min:0|max:50',
        ]);

        SystemConfig::current()->update($data);

        return back()->with('success', 'System configuration updated.');
    }

    // ============================================
    // MAIN LOGO (fallback)
    // ============================================

    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        $config = SystemConfig::current();
        $this->deleteBrandingFile($config->logo_path);

        $filename = $this->uploadBrandingFile($request->file('logo'), 'logo');

        $config->update(['logo_path' => $filename]);

        return back()->with('success', 'Logo uploaded successfully.');
    }

    public function removeLogo()
    {
        $config = SystemConfig::current();
        $this->deleteBrandingFile($config->logo_path);
        $config->update(['logo_path' => null]);

        return back()->with('success', 'Logo removed. Default branding will be used.');
    }

    public function updateLogoSize(Request $request)
    {
        $data = $request->validate([
            'logo_height' => 'required|integer|min:24|max:60',
        ]);

        SystemConfig::current()->update($data);

        return back()->with('success', 'Logo size updated.');
    }

    // ============================================
    // LIGHT MODE LOGO
    // ============================================

    public function uploadLightLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        $config = SystemConfig::current();
        $this->deleteBrandingFile($config->light_logo_path);

        $filename = $this->uploadBrandingFile($request->file('logo'), 'light_logo');

        $config->update(['light_logo_path' => $filename]);

        return back()->with('success', 'Light mode logo uploaded.');
    }

    public function removeLightLogo()
    {
        $config = SystemConfig::current();
        $this->deleteBrandingFile($config->light_logo_path);
        $config->update(['light_logo_path' => null]);

        return back()->with('success', 'Light mode logo removed.');
    }

    // ============================================
    // DARK MODE LOGO
    // ============================================

    public function uploadDarkLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        $config = SystemConfig::current();
        $this->deleteBrandingFile($config->dark_logo_path);

        $filename = $this->uploadBrandingFile($request->file('logo'), 'dark_logo');

        $config->update(['dark_logo_path' => $filename]);

        return back()->with('success', 'Dark mode logo uploaded.');
    }

    public function removeDarkLogo()
    {
        $config = SystemConfig::current();
        $this->deleteBrandingFile($config->dark_logo_path);
        $config->update(['dark_logo_path' => null]);

        return back()->with('success', 'Dark mode logo removed.');
    }

    // ============================================
    // AI ICON
    // ============================================

    public function uploadAiIcon(Request $request)
    {
        $request->validate([
            'ai_icon' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:1024',
        ]);

        $config = SystemConfig::current();
        $this->deleteBrandingFile($config->ai_icon_path);

        $filename = $this->uploadBrandingFile($request->file('ai_icon'), 'ai_icon');

        $config->update([
            'ai_icon_path' => $filename,
            'ai_icon_type' => 'custom',
        ]);

        return back()->with('success', 'AI icon uploaded.');
    }

    public function removeAiIcon()
    {
        $config = SystemConfig::current();
        $this->deleteBrandingFile($config->ai_icon_path);

        $config->update([
            'ai_icon_path' => null,
            'ai_icon_type' => 'default',
        ]);

        return back()->with('success', 'AI icon reset to default.');
    }

    public function setAiIconPreset(Request $request)
    {
        $data = $request->validate([
            'preset' => 'required|in:default,sparkle,bot,chat,magic',
        ]);

        $config = SystemConfig::current();
        $this->deleteBrandingFile($config->ai_icon_path);

        $config->update([
            'ai_icon_path' => null,
            'ai_icon_type' => $data['preset'],
        ]);

        return back()->with('success', 'AI icon preset updated.');
    }

    public function updateAiIconSize(Request $request)
    {
        $data = $request->validate([
            'ai_icon_size' => 'required|integer|min:40|max:80',
        ]);

        SystemConfig::current()->update($data);

        return back()->with('success', 'AI icon size updated.');
    }

    // ============================================
    // HELPERS
    // ============================================

    /**
     * Upload a branding file (logo, ai_icon, etc.) and return the relative path.
     */
    private function uploadBrandingFile($file, string $prefix): string
    {
        $dir = storage_path('app/public/' . self::BRANDING_DIR);
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        $extension = $file->getClientOriginalExtension();
        $filename = self::BRANDING_DIR . '/' . $prefix . '_' . uniqid() . '_' . time() . '.' . $extension;

        $file->move($dir, basename($filename));

        return $filename;
    }

    /**
     * Delete a branding file by relative path.
     */
    private function deleteBrandingFile(?string $relativePath): void
    {
        if (!$relativePath) return;

        $fullPath = storage_path('app/public/' . $relativePath);
        if (file_exists($fullPath)) {
            @unlink($fullPath);
        }
    }
}