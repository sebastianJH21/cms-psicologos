<?php

if (!function_exists('theme_asset')) {
    function theme_asset(string $path, ?string $slug = null): string
    {
        $slug = $slug ?: app(\App\Services\ThemeManager::class)->activeSlug();
        $path = ltrim($path, '/');
        $fullPath = base_path("themes/{$slug}/{$path}");
        $version = is_file($fullPath) ? filemtime($fullPath) : '';
        $url = url("/theme-assets/{$slug}/{$path}");
        return $version ? "{$url}?v={$version}" : $url;
    }
}

if (!function_exists('theme_image')) {
    function theme_image(string $slot, ?string $fallback = null): ?string
    {
        try {
            $resolver = app(\App\Services\ImagenSlotResolver::class);
            $slug = app(\App\Services\ThemeManager::class)->activeSlug();
            $url = $resolver->resolve($slug, $slot);
            if ($url) {
                return $url;
            }
        } catch (\Throwable $e) {
            // ignore and use fallback
        }

        return $fallback;
    }
}

if (!function_exists('phrase')) {
    function phrase(string $key, ?string $fallback = null): ?string
    {
        $fullKey = 'phrases.' . $key;
        $value = \App\Models\Setting::get($fullKey);
        if (is_string($value) && trim($value) !== '') {
            return $value;
        }
        $defaults = \App\Http\Controllers\Dashboard\FrasesController::DEFAULTS;
        if (isset($defaults[$fullKey]) && trim($defaults[$fullKey]) !== '') {
            return $defaults[$fullKey];
        }
        return $fallback;
    }
}

if (!function_exists('whatsapp_confirmacion_url')) {
    function whatsapp_confirmacion_url(\App\Models\Cita $cita): ?string
    {
        return app(\App\Services\WhatsappService::class)->urlConfirmacion($cita);
    }
}

if (!function_exists('whatsapp_recordatorio_pago_url')) {
    function whatsapp_recordatorio_pago_url(\App\Models\Cita $cita): ?string
    {
        return app(\App\Services\WhatsappService::class)->urlRecordatorioPago($cita);
    }
}

if (!function_exists('logo_data')) {
    function logo_data(): array
    {
        $path = \App\Models\Setting::get('branding.logo_path');
        $icon = \App\Models\Setting::get('branding.logo_icon');
        return [
            'path' => $path,
            'icon' => $icon,
            'url' => $path ? asset('storage/' . $path) : null,
            'has' => (bool) ($path || $icon),
        ];
    }
}
