<?php

namespace App\Traits;

trait HasTranslations
{
    public function translate(string $attribute, ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $value = $this->attributes[$attribute] ?? null;

        if ($value === null) {
            return '';
        }

        if (is_array($value)) {
            $translations = $value;
        } else {
            $decoded = json_decode($value, true);
            $translations = is_array($decoded) ? $decoded : [];
        }

        if (empty($translations)) {
            return '';
        }

        if (isset($translations[$locale]) && is_string($translations[$locale])) {
            return $translations[$locale];
        }

        $fallback = config('app.fallback_locale', 'en');
        if (isset($translations[$fallback]) && is_string($translations[$fallback])) {
            return $translations[$fallback];
        }

        $first = array_values($translations)[0] ?? '';
        return is_string($first) ? $first : '';
    }

    public function getTranslation(string $attribute, ?string $locale = null): mixed
    {
        $locale = $locale ?: app()->getLocale();
        $value = $this->attributes[$attribute] ?? null;

        if ($value === null) {
            return null;
        }

        if (is_array($value)) {
            $translations = $value;
        } else {
            $decoded = json_decode($value, true);
            $translations = is_array($decoded) ? $decoded : [];
        }

        if (empty($translations)) {
            return null;
        }

        if (array_key_exists($locale, $translations)) {
            return $translations[$locale];
        }

        $fallback = config('app.fallback_locale', 'en');
        if (array_key_exists($fallback, $translations)) {
            return $translations[$fallback];
        }

        return array_values($translations)[0] ?? null;
    }
}
