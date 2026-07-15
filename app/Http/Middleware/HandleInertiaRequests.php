<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    /**
     * The root template that's loaded on the first page visit.
     *
     * @see https://inertiajs.com/server-side-setup#root-template
     *
     * @var string
     */
    protected $rootView = 'app';

    /**
     * Determines the current asset version.
     *
     * @see https://inertiajs.com/asset-versioning
     */
    public function version(Request $request): ?string
    {
        return parent::version($request);
    }

    /**
     * Define the props that are shared by default.
     *
     * @see https://inertiajs.com/shared-data
     *
     * @return array<string, mixed>
     */
    public function share(Request $request): array
    {
        $user = $request->user();

        $assetUrl = function (string $key): ?string {
            $path = Setting::get("branding.{$key}");

            return $path !== null && $path !== '' ? Storage::url($path) : null;
        };

        $branding = [
            'appName' => Setting::get('branding.app_name') ?: config('app.name'),
            'loginTitle' => Setting::get('branding.login_title') ?: null,
            'loginSubtitle' => Setting::get('branding.login_subtitle') ?: null,
            'heroTitle' => Setting::get('branding.hero_title') ?: null,
            'heroSubtitle' => Setting::get('branding.hero_subtitle') ?: null,
            'logoUrl' => $assetUrl('logo'),
            'iconUrl' => $assetUrl('icon'),
            'faviconUrl' => $assetUrl('favicon'),
        ];

        return [
            ...parent::share($request),
            'name' => $branding['appName'],
            'branding' => $branding,
            'auth' => [
                'user' => $user,
                'roles' => $user?->getRoleNames() ?? [],
                'permissions' => $user?->getAllPermissions()->pluck('name') ?? [],
            ],
            'scanCount' => $user ? $user->qrScans()->count() : 0,
            'flash' => [
                'success' => $request->session()->get('success'),
                'error' => $request->session()->get('error'),
            ],
            'ziggy' => fn () => [
                ...(new \Tighten\Ziggy\Ziggy)->toArray(),
                'location' => $request->url(),
            ],
            'sidebarOpen' => ! $request->hasCookie('sidebar_state') || $request->cookie('sidebar_state') === 'true',
        ];
    }
}
