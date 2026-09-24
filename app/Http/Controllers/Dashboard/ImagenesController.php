<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Services\ImagenSlotResolver;
use App\Services\ThemeManager;
use Illuminate\Http\Request;

class ImagenesController extends Controller
{
    public function __construct(
        private ThemeManager $themeManager,
        private ImagenSlotResolver $resolver
    ) {}

    public function index()
    {
        $theme = $this->themeManager->activeTheme();
        $slug = $this->themeManager->activeSlug();
        $slots = $theme['image_slots'] ?? ['hero', 'sobre-mi', 'blog-bg'];
        // servicios-bg es un elemento decorativo del tema y no debe ser configurable
        $slots = array_values(array_diff($slots, ['servicios-bg']));

        $slotLabels = [
            'hero'         => 'Imagen principal (Hero)',
            'sobre-mi'     => 'Foto "Sobre mí"',
            'blog-bg'      => 'Fondo sección blog',
        ];

        $slotHints = [
            'hero'         => 'Imagen principal visible en el inicio. Recomendado: foto sin fondo o con fondo claro.',
            'sobre-mi'     => 'Tu foto en la sección "Sobre mí". Recomendado: foto cuadrada o vertical.',
            'blog-bg'      => 'Imagen de cabecera para la sección del blog.',
        ];

        $items = [];
        foreach ($slots as $slot) {
            $items[] = [
                'slot'        => $slot,
                'label'       => $slotLabels[$slot] ?? ucfirst(str_replace('-', ' ', $slot)),
                'hint'        => $slotHints[$slot] ?? '',
                'url'         => $this->resolver->resolve($slug, $slot),
                'has_override' => $this->resolver->hasOverride($slug, $slot),
            ];
        }

        return view('dashboard.imagenes.index', compact('theme', 'slug', 'items'));
    }

    public function update(Request $request, string $slot)
    {
        $request->validate([
            'imagen' => 'required|image|mimes:jpg,jpeg,png,webp|max:4096',
        ]);

        $slug = $this->themeManager->activeSlug();
        $this->resolver->storeOverride($slug, $slot, $request->file('imagen'));

        return back()->with('success', 'Imagen actualizada correctamente.');
    }

    public function restore(string $slot)
    {
        $slug = $this->themeManager->activeSlug();
        $this->resolver->deleteOverride($slug, $slot);

        return back()->with('success', 'Imagen restaurada al original del tema.');
    }
}
