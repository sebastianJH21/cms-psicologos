<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Services\ImagenOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LogoController extends Controller
{
    public function __construct(private ImagenOptimizer $optimizador)
    {
    }

    public const ICONOS_DISPONIBLES = [
        'fa-brain', 'fa-heart', 'fa-heart-pulse', 'fa-leaf', 'fa-seedling', 'fa-spa', 'fa-feather',
        'fa-hands-holding-circle', 'fa-hand-holding-heart', 'fa-hand-holding-medical', 'fa-dove',
        'fa-yin-yang', 'fa-handshake-angle', 'fa-mug-hot', 'fa-puzzle-piece', 'fa-head-side-virus',
        'fa-people-arrows', 'fa-cloud-sun', 'fa-tree', 'fa-moon', 'fa-sun',
        'fa-fire', 'fa-lightbulb', 'fa-stethoscope', 'fa-user-doctor', 'fa-notes-medical',
        'fa-comments', 'fa-comment-medical', 'fa-person-praying', 'fa-bookmark',
        'fa-book-open', 'fa-circle-nodes', 'fa-infinity', 'fa-ribbon', 'fa-shield-heart',
        'fa-people-group', 'fa-person-rays', 'fa-hand-holding', 'fa-hands-praying',
        'fa-house-medical', 'fa-bell', 'fa-star', 'fa-quote-left', 'fa-anchor',
        'fa-mountain-sun', 'fa-water', 'fa-wind', 'fa-feather-pointed', 'fa-snowflake',
        'fa-clover', 'fa-pagelines', 'fa-circle-half-stroke', 'fa-compass', 'fa-bullseye',
        // Salud, cuerpo y naturaleza
        'fa-lungs', 'fa-bone', 'fa-tooth', 'fa-dna', 'fa-microscope',
        'fa-syringe', 'fa-hospital', 'fa-hospital-user', 'fa-x-ray', 'fa-weight-scale',
        'fa-fire-flame-curved', 'fa-earth-americas', 'fa-paw', 'fa-fish', 'fa-temperature-half',
        'fa-vial', 'fa-flask', 'fa-capsules', 'fa-tablets', 'fa-wheelchair',
        'fa-person-running', 'fa-person-swimming', 'fa-person-biking', 'fa-heart-circle-check',
        'fa-hand-sparkles', 'fa-cloud-rain', 'fa-droplet', 'fa-eye', 'fa-ear-listen',
        // Emociones
        'fa-face-smile', 'fa-face-smile-beam', 'fa-face-grin', 'fa-face-grin-wide',
        'fa-face-grin-beam', 'fa-face-grin-hearts', 'fa-face-grin-wink', 'fa-face-laugh',
        'fa-face-laugh-beam', 'fa-face-laugh-wink', 'fa-face-meh', 'fa-face-meh-blank',
        'fa-face-rolling-eyes', 'fa-face-flushed', 'fa-face-surprise', 'fa-face-grimace',
        'fa-face-sad-tear', 'fa-face-sad-cry', 'fa-face-frown', 'fa-face-frown-open',
        'fa-face-angry', 'fa-face-tired', 'fa-face-dizzy', 'fa-face-kiss',
        // Insectos y naturaleza pequeña
        'fa-spider', 'fa-bug-slash',
    ];

    public function edit()
    {
        $logo = [
            'path' => Setting::get('branding.logo_path'),
            'icon' => Setting::get('branding.logo_icon'),
        ];
        $iconos = self::ICONOS_DISPONIBLES;
        return view('dashboard.logo.edit', compact('logo', 'iconos'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'logo_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp,svg,gif', 'max:2048'],
            'logo_icon'  => ['nullable', 'string', 'max:80'],
            'modo_logo'  => ['required', 'in:imagen,icono,ninguno'],
        ]);

        if ($request->modo_logo === 'imagen' && $request->hasFile('logo_image')) {
            $this->borrarImagenExistente();
            $path = $this->optimizador->procesarYGuardar(
                $request->file('logo_image'),
                'branding',
                400,
                90
            );
            Setting::set('branding.logo_path', $path, 'branding');
            Setting::set('branding.logo_icon', null, 'branding');
        } elseif ($request->modo_logo === 'icono') {
            $icon = $request->input('logo_icon');
            if (!$icon) {
                return back()->withErrors(['logo_icon' => 'Selecciona un icono.']);
            }
            $this->borrarImagenExistente();
            Setting::set('branding.logo_icon', $icon, 'branding');
            Setting::set('branding.logo_path', null, 'branding');
        } else {
            $this->borrarImagenExistente();
            Setting::set('branding.logo_path', null, 'branding');
            Setting::set('branding.logo_icon', null, 'branding');
        }

        return back()->with('success', 'Logo actualizado correctamente.');
    }

    private function borrarImagenExistente(): void
    {
        $existing = Setting::get('branding.logo_path');
        if ($existing && Storage::disk('public')->exists($existing)) {
            Storage::disk('public')->delete($existing);
        }
    }
}
