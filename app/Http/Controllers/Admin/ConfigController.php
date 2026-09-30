<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SystemConfig;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ConfigController extends Controller
{
    /**
     * Update general system configuration.
     */
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

        $config = SystemConfig::current();
        $config->update($data);

        return back()->with('success', 'System configuration updated.');
    }

    /**
     * Upload custom logo.
     */
    public function uploadLogo(Request $request)
    {
        $request->validate([
            'logo' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
        ]);

        $config = SystemConfig::current();

        // Delete old logo
        if ($config->logo_path) {
            $oldPath = storage_path('app/public/' . $config->logo_path);
            if (file_exists($oldPath)) {
                @unlink($oldPath);
            }
        }

        // Ensure directory
        $dir = storage_path('app/public/branding');
        if (!file_exists($dir)) {
            mkdir($dir, 0755, true);
        }

        // Save new logo
        $file = $request->file('logo');
        $extension = $file->getClientOriginalExtension();
        $filename = 'branding/logo_' . uniqid() . '_' . time() . '.' . $extension;

        $file->move($dir, basename($filename));

        $config->update(['logo_path' => $filename]);

        return back()->with('success', 'Logo uploaded successfully.');
    }

    /**
     * Remove custom logo.
     */
        /**
     * Remove custom logo.
     */
    public function uploadLightLogo(Request $request)
{
    $request->validate([
        'logo' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
    ]);

    $config = SystemConfig::current();

    // Delete old
    if ($config->light_logo_path) {
        $oldPath = storage_path('app/public/' . $config->light_logo_path);
        if (file_exists($oldPath)) @unlink($oldPath);
    }

    // Ensure directory
    $dir = storage_path('app/public/branding');
    if (!file_exists($dir)) mkdir($dir, 0755, true);

    // Save new
    $file = $request->file('logo');
    $extension = $file->getClientOriginalExtension();
    $filename = 'branding/light_logo_' . uniqid() . '_' . time() . '.' . $extension;

    $file->move($dir, basename($filename));

    $config->update(['light_logo_path' => $filename]);

    return back()->with('success', 'Light mode logo uploaded.');
}

public function removeLightLogo()
{
    $config = SystemConfig::current();

    if ($config->light_logo_path) {
        $path = storage_path('app/public/' . $config->light_logo_path);
        if (file_exists($path)) @unlink($path);
    }

    $config->update(['light_logo_path' => null]);

    return back()->with('success', 'Light mode logo removed.');
}

public function uploadDarkLogo(Request $request)
{
    $request->validate([
        'logo' => 'required|image|mimes:png,jpg,jpeg,svg,webp|max:2048',
    ]);

    $config = SystemConfig::current();

    if ($config->dark_logo_path) {
        $oldPath = storage_path('app/public/' . $config->dark_logo_path);
        if (file_exists($oldPath)) @unlink($oldPath);
    }

    $dir = storage_path('app/public/branding');
    if (!file_exists($dir)) mkdir($dir, 0755, true);

    $file = $request->file('logo');
    $extension = $file->getClientOriginalExtension();
    $filename = 'branding/dark_logo_' . uniqid() . '_' . time() . '.' . $extension;

    $file->move($dir, basename($filename));

    $config->update(['dark_logo_path' => $filename]);

    return back()->with('success', 'Dark mode logo uploaded.');
}

public function removeDarkLogo()
{
    $config = SystemConfig::current();

    if ($config->dark_logo_path) {
        $path = storage_path('app/public/' . $config->dark_logo_path);
        if (file_exists($path)) @unlink($path);
    }

    $config->update(['dark_logo_path' => null]);

    return back()->with('success', 'Dark mode logo removed.');
}
    public function removeLogo()
    {
        $config = SystemConfig::current();

        if ($config->logo_path) {
            $path = storage_path('app/public/' . $config->logo_path);
            if (file_exists($path)) {
                @unlink($path);
            }
        }

        $config->update(['logo_path' => null]);

        return back()->with('success', 'Logo removed. Default branding will be used.');
    }

    /**
     * Update logo height.
     */
    public function updateLogoSize(Request $request)
    {
        $data = $request->validate([
            'logo_height' => 'required|integer|min:24|max:60',
        ]);

        $config = SystemConfig::current();
        $config->update($data);

        return back()->with('success', 'Logo size updated.');
    }
}