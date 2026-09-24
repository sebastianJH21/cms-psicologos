<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class ImagenSlotResolver
{
    private const ANCHOS_POR_SLOT = [
        'hero'      => 1920,
        'sobre-mi'  => 1200,
        'servicios' => 1600,
    ];

    private const ANCHO_POR_DEFECTO = 1600;

    private const SHARED_DIR = 'theme-overrides/shared';

    public function __construct(private ImagenOptimizer $optimizador)
    {
    }

    public function resolve(string $themeSlug, string $slot): ?string
    {
        $shared = $this->findSharedOverride($slot);
        if ($shared) {
            return $this->buildOverrideUrl($shared);
        }

        $legacy = $this->findLegacyOverride($themeSlug, $slot);
        if ($legacy) {
            return $this->buildOverrideUrl($legacy);
        }

        return $this->defaultAssetUrl($themeSlug, $slot);
    }

    public function hasOverride(string $themeSlug, string $slot): bool
    {
        return $this->findSharedOverride($slot) !== null
            || $this->findLegacyOverride($themeSlug, $slot) !== null;
    }

    public function storeOverride(string $themeSlug, string $slot, \Illuminate\Http\UploadedFile $file): string
    {
        // Borrar el shared anterior (si existe en cualquier extensión)
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $e) {
            $old = self::SHARED_DIR . "/{$slot}.{$e}";
            if (Storage::disk('public')->exists($old)) {
                Storage::disk('public')->delete($old);
            }
        }

        // Limpiar también cualquier override legacy por tema (datos antiguos)
        $this->deleteLegacyOverrides($slot);

        $maxWidth = self::ANCHOS_POR_SLOT[$slot] ?? self::ANCHO_POR_DEFECTO;

        return $this->optimizador->procesarYGuardar(
            $file,
            self::SHARED_DIR,
            $maxWidth,
            80,
            'public',
            $slot
        );
    }

    public function deleteOverride(string $themeSlug, string $slot): void
    {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $e) {
            $path = self::SHARED_DIR . "/{$slot}.{$e}";
            if (Storage::disk('public')->exists($path)) {
                Storage::disk('public')->delete($path);
            }
        }
        $this->deleteLegacyOverrides($slot);
    }

    private function findSharedOverride(string $slot): ?string
    {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            $path = self::SHARED_DIR . "/{$slot}.{$ext}";
            if (Storage::disk('public')->exists($path)) {
                return $path;
            }
        }
        return null;
    }

    private function findLegacyOverride(string $themeSlug, string $slot): ?string
    {
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            $path = "theme-overrides/{$themeSlug}/{$slot}.{$ext}";
            if (Storage::disk('public')->exists($path)) {
                return $path;
            }
        }
        // También probar cualquier otro tema (legacy compartido implícito)
        $directorios = Storage::disk('public')->directories('theme-overrides');
        foreach ($directorios as $dir) {
            $slug = basename($dir);
            if ($slug === 'shared' || $slug === $themeSlug) {
                continue;
            }
            foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                $path = "{$dir}/{$slot}.{$ext}";
                if (Storage::disk('public')->exists($path)) {
                    return $path;
                }
            }
        }
        return null;
    }

    private function deleteLegacyOverrides(string $slot): void
    {
        $directorios = Storage::disk('public')->directories('theme-overrides');
        foreach ($directorios as $dir) {
            if (basename($dir) === 'shared') {
                continue;
            }
            foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                $path = "{$dir}/{$slot}.{$ext}";
                if (Storage::disk('public')->exists($path)) {
                    Storage::disk('public')->delete($path);
                }
            }
        }
    }

    private function buildOverrideUrl(string $path): string
    {
        $url = Storage::disk('public')->url($path);
        $mtime = $this->overrideMtime($path);
        return $mtime ? $url . '?v=' . $mtime : $url;
    }

    private function overrideMtime(string $path): ?int
    {
        try {
            $time = Storage::disk('public')->lastModified($path);
            return $time ?: null;
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function defaultAssetUrl(string $themeSlug, string $slot): ?string
    {
        $base = base_path("themes/{$themeSlug}/assets/img");
        foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
            $file = "{$base}/{$slot}.{$ext}";
            if (file_exists($file)) {
                return route('theme.asset', [$themeSlug, "assets/img/{$slot}.{$ext}"]);
            }
        }

        // Fallback: buscar misma imagen en tema-base
        if ($themeSlug !== 'tema-base') {
            $base2 = base_path('themes/tema-base/assets/img');
            foreach (['jpg', 'jpeg', 'png', 'webp'] as $ext) {
                $file = "{$base2}/{$slot}.{$ext}";
                if (file_exists($file)) {
                    return route('theme.asset', ['tema-base', "assets/img/{$slot}.{$ext}"]);
                }
            }
        }

        return null;
    }
}
