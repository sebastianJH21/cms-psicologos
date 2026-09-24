<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Profile;
use App\Models\Setting;
use App\Services\ImagenOptimizer;
use App\Services\ThemeManager;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TemaController extends Controller
{
    public function __construct(
        private ThemeManager $themeManager,
        private ImagenOptimizer $optimizador
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

    public function actualizarLogo(Request $request)
    {
        $request->validate([
            'logo_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg,gif', 'max:2048'],
            'logo_icon'  => ['nullable', 'string', 'max:80'],
            'modo_logo'  => ['required', 'in:imagen,icono,ninguno'],
        ]);

        if ($request->modo_logo === 'imagen' && $request->hasFile('logo_image')) {
            $existing = Setting::get('branding.logo_path');
            if ($existing && Storage::disk('public')->exists($existing)) {
                Storage::disk('public')->delete($existing);
            }
            $path = $this->optimizador->procesarYGuardar(
                $request->file('logo_image'),
                'branding',
                400,
                90
            );
            Setting::set('branding.logo_path', $path, 'branding');
            Setting::set('branding.logo_icon', null, 'branding');
        } elseif ($request->modo_logo === 'icono') {
            Setting::set('branding.logo_icon', $request->input('logo_icon'), 'branding');
            $existing = Setting::get('branding.logo_path');
            if ($existing && Storage::disk('public')->exists($existing)) {
                Storage::disk('public')->delete($existing);
            }
            Setting::set('branding.logo_path', null, 'branding');
        } else {
            $existing = Setting::get('branding.logo_path');
            if ($existing && Storage::disk('public')->exists($existing)) {
                Storage::disk('public')->delete($existing);
            }
            Setting::set('branding.logo_path', null, 'branding');
            Setting::set('branding.logo_icon', null, 'branding');
        }

        return back()->with('success', 'Logo actualizado correctamente.');
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

    public function preview(string $slug)
    {
        $theme = $this->themeManager->find($slug);
        if (!$theme) {
            abort(404);
        }

        $profile = Profile::first();

        return view('dashboard.temas.preview', compact('theme', 'profile'));
    }
}
