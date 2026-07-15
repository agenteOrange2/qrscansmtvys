<?php

namespace App\Http\Controllers\Settings;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class BrandingController extends Controller
{
    private const TEXT_KEYS = ['app_name', 'login_title', 'login_subtitle', 'hero_title', 'hero_subtitle'];

    private const ASSET_KEYS = ['logo', 'icon', 'favicon'];

    public function edit(): Response
    {
        return Inertia::render('settings/Branding', [
            'brandingSettings' => [
                'app_name' => Setting::get('branding.app_name', ''),
                'login_title' => Setting::get('branding.login_title', ''),
                'login_subtitle' => Setting::get('branding.login_subtitle', ''),
                'hero_title' => Setting::get('branding.hero_title', ''),
                'hero_subtitle' => Setting::get('branding.hero_subtitle', ''),
                'logo_url' => $this->assetUrl('logo'),
                'icon_url' => $this->assetUrl('icon'),
                'favicon_url' => $this->assetUrl('favicon'),
            ],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'app_name' => ['nullable', 'string', 'max:100'],
            'login_title' => ['nullable', 'string', 'max:150'],
            'login_subtitle' => ['nullable', 'string', 'max:300'],
            'hero_title' => ['nullable', 'string', 'max:200'],
            'hero_subtitle' => ['nullable', 'string', 'max:500'],
            'logo' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:2048'],
            'icon' => ['nullable', 'image', 'mimes:png,jpg,jpeg,svg,webp', 'max:1024'],
            'favicon' => ['nullable', 'file', 'mimes:ico,png,svg', 'max:512'],
        ], [
            'logo.max' => 'El logo no debe pesar más de 2 MB.',
            'icon.max' => 'El ícono no debe pesar más de 1 MB.',
            'favicon.max' => 'El favicon no debe pesar más de 512 KB.',
            'favicon.mimes' => 'El favicon debe ser .ico, .png o .svg.',
        ]);

        foreach (self::TEXT_KEYS as $key) {
            Setting::set("branding.{$key}", trim((string) ($data[$key] ?? '')));
        }

        foreach (self::ASSET_KEYS as $key) {
            if (! $request->hasFile($key)) {
                continue;
            }

            $old = Setting::get("branding.{$key}");
            $path = $request->file($key)->store('branding', 'public');

            Setting::set("branding.{$key}", $path);

            if ($old !== null && $old !== '') {
                Storage::disk('public')->delete($old);
            }
        }

        return back()->with('success', 'Branding actualizado correctamente.');
    }

    /**
     * Quita un recurso (logo, ícono o favicon) y vuelve al valor por defecto.
     */
    public function destroyAsset(string $asset): RedirectResponse
    {
        abort_unless(in_array($asset, self::ASSET_KEYS, true), 404);

        $path = Setting::get("branding.{$asset}");

        if ($path !== null && $path !== '') {
            Storage::disk('public')->delete($path);
        }

        Setting::set("branding.{$asset}", '');

        return back()->with('success', 'Imagen eliminada; se usará la predeterminada.');
    }

    private function assetUrl(string $asset): ?string
    {
        return Setting::assetUrl("branding.{$asset}");
    }
}
