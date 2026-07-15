<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    /**
     * @var list<string>
     */
    protected $fillable = ['key', 'value'];

    public static function get(string $key, ?string $default = null): ?string
    {
        $value = Cache::rememberForever(
            self::cacheKey($key),
            fn () => self::query()->where('key', $key)->value('value'),
        );

        return $value ?? $default;
    }

    public static function getBool(string $key, bool $default = false): bool
    {
        $value = self::get($key);

        return $value === null ? $default : $value === '1';
    }

    public static function set(string $key, ?string $value): void
    {
        self::query()->updateOrCreate(['key' => $key], ['value' => $value]);

        Cache::forget(self::cacheKey($key));
    }

    /**
     * URL pública de un archivo del disco `public` guardado en un setting.
     * Usa asset() (esquema + dominio del request actual) en lugar de
     * Storage::url(), que depende de un APP_URL bien configurado.
     */
    public static function assetUrl(string $key): ?string
    {
        $path = self::get($key);

        return $path !== null && trim($path) !== ''
            ? asset('storage/'.ltrim($path, '/'))
            : null;
    }

    private static function cacheKey(string $key): string
    {
        return "settings.{$key}";
    }
}
