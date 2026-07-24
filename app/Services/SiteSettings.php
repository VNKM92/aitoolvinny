<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;

class SiteSettings
{
    /**
     * Get a setting value by key.
     */
    public static function get(string $key, $default = null)
    {
        $settings = self::getAll();

        if (array_key_exists($key, $settings)) {
            $value = $settings[$key];
            
            // Auto decode JSON strings if applicable
            if (is_string($value) && (str_starts_with($value, '{') || str_starts_with($value, '['))) {
                $decoded = json_decode($value, true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    return $decoded;
                }
            }
            return $value;
        }

        return $default;
    }

    /**
     * Set/update a setting.
     */
    public static function set(string $key, $value): void
    {
        if (is_array($value) || is_object($value)) {
            $value = json_encode($value);
        }

        try {
            Setting::updateOrCreate(['key' => $key], ['value' => $value]);
            self::clearCache();
        } catch (\Throwable $e) {
            // Silently absorb database issues during installations or seeding
        }
    }

    /**
     * Get all settings from database/cache with a defensive fallback if table does not exist.
     */
    public static function getAll(): array
    {
        try {
            return Cache::rememberForever('site_settings_map', function () {
                if (!Schema::hasTable('settings')) {
                    return [];
                }
                return Setting::pluck('value', 'key')->toArray();
            });
        } catch (\Throwable $e) {
            return [];
        }
    }

    /**
     * Clear the cached settings map.
     */
    public static function clearCache(): void
    {
        Cache::forget('site_settings_map');
    }
}
