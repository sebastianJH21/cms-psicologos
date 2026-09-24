<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\File;

class ThemeManager
{
    private string $themesPath;

    public function __construct()
    {
        $this->themesPath = base_path('themes');
    }

    public function all(): array
    {
        if (!File::isDirectory($this->themesPath)) {
            return [];
        }

        $themes = [];
        foreach (File::directories($this->themesPath) as $dir) {
            $jsonPath = $dir . DIRECTORY_SEPARATOR . 'theme.json';
            if (!File::exists($jsonPath)) {
                continue;
            }
            $data = json_decode(File::get($jsonPath), true);
            if (!$this->isValidTheme($data)) {
                continue;
            }
            $themes[$data['slug']] = $data;
        }

        ksort($themes);
        return $themes;
    }

    public function find(string $slug): ?array
    {
        $jsonPath = $this->themesPath . DIRECTORY_SEPARATOR . $slug . DIRECTORY_SEPARATOR . 'theme.json';
        if (!File::exists($jsonPath)) {
            return null;
        }
        $data = json_decode(File::get($jsonPath), true);
        return $this->isValidTheme($data) ? $data : null;
    }

    public function activate(string $slug, string $mode = 'landing'): bool
    {
        $theme = $this->find($slug);
        if (!$theme) {
            return false;
        }
        if (!in_array($mode, $theme['supports'] ?? ['landing', 'multipage'])) {
            return false;
        }
        Setting::set('active_theme', $slug, 'theme');
        Setting::set('theme_mode', $mode, 'theme');
        return true;
    }

    public function activeSlug(): string
    {
        $preview = $this->previewSlug();
        if ($preview !== null) {
            return $preview;
        }
        return Setting::get('active_theme', 'tema-base');
    }

    public function activeMode(): string
    {
        $preview = $this->previewMode();
        if ($preview !== null) {
            return $preview;
        }
        return Setting::get('theme_mode', 'landing');
    }

    public function previewSlug(): ?string
    {
        $req = request();
        if (!$req) {
            return null;
        }
        $slug = $req->query('preview_theme');
        if (!$slug || !is_string($slug)) {
            return null;
        }
        return $this->find($slug) ? $slug : null;
    }

    public function previewMode(): ?string
    {
        $req = request();
        if (!$req) {
            return null;
        }
        $mode = $req->query('preview_mode');
        if (in_array($mode, ['landing', 'multipage'], true)) {
            return $mode;
        }
        return null;
    }

    public function activeTheme(): ?array
    {
        return $this->find($this->activeSlug());
    }

    public function themePath(string $slug): string
    {
        return $this->themesPath . DIRECTORY_SEPARATOR . $slug;
    }

    public function viewsPath(string $slug): string
    {
        return $this->themePath($slug) . DIRECTORY_SEPARATOR . 'views';
    }

    private function isValidTheme(?array $data): bool
    {
        return $data !== null
            && isset($data['slug'], $data['name'], $data['supports'])
            && is_array($data['supports'])
            && count($data['supports']) > 0;
    }
}
