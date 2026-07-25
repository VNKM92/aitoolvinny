<?php

if (!function_exists('lroute')) {
    /**
     * Build a localized route URL (uses prefixed route only for non-default locales).
     *
     * @param string $baseName Base route name without ".locale" suffix (e.g. 'tenant.post')
     * @param array $params Route parameters (without locale)
     * @param string|null $locale Target locale (defaults to current app locale)
     * @return string
     */
    function lroute(string $baseName, array $params = [], ?string $locale = null): string
    {
        $locale = $locale ?: app()->getLocale();
        $default = \App\Services\SiteSettings::get('default_locale', config('app.locale', 'en'));

        if ($locale === $default) {
            return route($baseName, $params);
        }

        $localeParams = array_merge(['locale' => $locale], $params);
        return route($baseName . '.locale', $localeParams);
    }
}
