<?php

use App\Models\Setting;
use App\Models\User;
use Database\Seeders\RolesAndPermissionsSeeder;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    $this->seed(RolesAndPermissionsSeeder::class);

    $this->admin = User::factory()->create();
    $this->admin->assignRole('Administrador');

    $this->exhibidor = User::factory()->create();
    $this->exhibidor->assignRole('Exhibidor');
});

/**
 * PNG real de 1x1 px — evita depender de la extensión GD.
 */
function fakePng(string $name = 'logo.png'): UploadedFile
{
    $png = base64_decode(
        'iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='
    );

    return UploadedFile::fake()->createWithContent($name, $png);
}

test('users without the branding permission cannot open the branding page', function () {
    $this->actingAs($this->exhibidor)
        ->get(route('admin.settings.branding.edit'))
        ->assertForbidden();
});

test('admins can open the branding page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.settings.branding.edit'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('settings/Branding')
            ->has('brandingSettings'));
});

test('branding texts are saved', function () {
    $this->actingAs($this->admin)
        ->put(route('admin.settings.branding.update'), [
            'app_name' => 'ScanQR SMTVYS',
            'login_title' => 'Bienvenido al escáner',
            'login_subtitle' => 'Accede con tu cuenta',
            'hero_title' => "Escaneo QR\npara expos",
            'hero_subtitle' => 'Captura contactos y mándalos al CRM.',
        ])
        ->assertRedirect();

    expect(Setting::get('branding.app_name'))->toBe('ScanQR SMTVYS')
        ->and(Setting::get('branding.login_title'))->toBe('Bienvenido al escáner')
        ->and(Setting::get('branding.login_subtitle'))->toBe('Accede con tu cuenta')
        ->and(Setting::get('branding.hero_title'))->toBe("Escaneo QR\npara expos")
        ->and(Setting::get('branding.hero_subtitle'))->toBe('Captura contactos y mándalos al CRM.');
});

test('a logo can be uploaded and replaced', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)
        ->put(route('admin.settings.branding.update'), [
            'logo' => fakePng(),
        ])
        ->assertRedirect();

    $first = Setting::get('branding.logo');

    expect($first)->not->toBeNull();
    Storage::disk('public')->assertExists($first);

    $this->actingAs($this->admin)
        ->put(route('admin.settings.branding.update'), [
            'logo' => fakePng('logo-nuevo.png'),
        ])
        ->assertRedirect();

    $second = Setting::get('branding.logo');

    expect($second)->not->toBe($first);
    Storage::disk('public')->assertExists($second);
    Storage::disk('public')->assertMissing($first);
});

test('a favicon in png is accepted', function () {
    Storage::fake('public');

    $this->actingAs($this->admin)
        ->put(route('admin.settings.branding.update'), [
            'favicon' => fakePng('favicon.png'),
        ])
        ->assertRedirect()
        ->assertSessionHasNoErrors();

    Storage::disk('public')->assertExists(Setting::get('branding.favicon'));
});

test('a branding asset can be removed', function () {
    Storage::fake('public');

    $path = fakePng()->store('branding', 'public');
    Setting::set('branding.logo', $path);

    $this->actingAs($this->admin)
        ->delete(route('admin.settings.branding.destroy-asset', 'logo'))
        ->assertRedirect();

    expect(Setting::get('branding.logo'))->toBe('');
    Storage::disk('public')->assertMissing($path);
});

test('removing an unknown asset type returns 404', function () {
    $this->actingAs($this->admin)
        ->delete(route('admin.settings.branding.destroy-asset', 'logo2'))
        ->assertNotFound();
});

test('the login page shares the configured branding', function () {
    Setting::set('branding.app_name', 'Mi Escáner');
    Setting::set('branding.login_title', 'Hola SMTVYS');
    Setting::set('branding.login_subtitle', 'Entra con tus credenciales');

    $this->get(route('login'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('auth/Login')
            ->where('branding.appName', 'Mi Escáner')
            ->where('branding.loginTitle', 'Hola SMTVYS')
            ->where('branding.loginSubtitle', 'Entra con tus credenciales'));
});

test('branding falls back to the app name when unset', function () {
    $this->get(route('login'))
        ->assertOk()
        ->assertInertia(fn ($page) => $page
            ->where('branding.appName', config('app.name'))
            ->where('branding.loginTitle', null)
            ->where('branding.logoUrl', null));
});
