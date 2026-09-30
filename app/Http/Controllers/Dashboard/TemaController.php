<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ThemeManager;
use Illuminate\Http\Request;

class TemaController extends Controller
{
    public function __construct(
        private ThemeManager $themeManager
    ) {}

    public function index()
    {
        $themes = $this->themeManager->all();
        $activeSlug = $this->themeManager->activeSlug();
        $activeMode = $this->themeManager->activeMode();
        $logo = [
            'path' => Setting::get('branding.logo_path'),
            'icon' => Setting::get('branding.logo_icon'),
        ];
        $iconosLogo = [
            'fa-brain', 'fa-heart', 'fa-leaf', 'fa-seedling', 'fa-spa', 'fa-feather',
            'fa-hands-holding-circle', 'fa-hand-holding-heart', 'fa-dove', 'fa-yin-yang',
            'fa-lotus', 'fa-handshake-angle', 'fa-mug-hot', 'fa-puzzle-piece',
            'fa-head-side-virus', 'fa-people-arrows', 'fa-cloud-sun', 'fa-tree',
            'fa-moon', 'fa-sun', 'fa-fire',
        ];

        return view('dashboard.temas.index', compact('themes', 'activeSlug', 'activeMode', 'logo', 'iconosLogo'));
    }

    public function activar(Request $request, string $slug)
    {
        $request->validate([
            'mode' => 'required|in:landing,multipage',
        ]);

        $theme = $this->themeManager->find($slug);
        if (!$theme) {
            return back()->with('error', 'Tema no encontrado.');
        }

        if (!in_array($request->mode, $theme['supports'])) {
            return back()->with('error', 'Este tema no soporta el modo seleccionado.');
        }

        $this->themeManager->activate($slug, $request->mode);

        return back()->with('success', "Tema «{$theme['name']}» activado en modo " . ($request->mode === 'landing' ? 'landing' : 'multipágina') . '.');
    }
}
