<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class FrasesController extends Controller
{
    public const DEFAULTS = [
        // Hero
        'phrases.hero_badge' => 'Psicología y bienestar',
        'phrases.hero_overline' => 'Bienestar y acompañamiento',
        'phrases.hero_cta' => 'Pide tu primera cita',
        'phrases.hero_frase' => 'Un espacio seguro para acompañarte en tu proceso de cambio y bienestar.',
        // Sobre mí
        'phrases.about_overline' => 'Sobre mí',
        'phrases.about_title' => 'Conoce a tu psicóloga',
        'phrases.about_pie_foto' => '+10 años acompañando',
        // Servicios
        'phrases.servicios_overline' => 'Servicios',
        'phrases.servicios_title' => 'Cómo puedo ayudarte',
        'phrases.servicios_description' => 'Acompañamiento profesional adaptado a tus necesidades, con un enfoque cálido y empático.',
        // Especialidades (terapias)
        'phrases.especialidades_overline' => 'Especialidades',
        'phrases.especialidades_title' => 'Áreas en las que te puedo ayudar',
        'phrases.especialidades_description' => 'Trabajo desde la psicología basada en evidencia para acompañarte en distintas áreas de tu vida.',
        // Planes
        'phrases.planes_overline' => 'Tarifas',
        'phrases.planes_title' => 'Planes y precios transparentes',
        'phrases.planes_description' => 'Elige la modalidad que mejor se adapte a ti.',
        // Blog
        'phrases.blog_overline' => 'Blog',
        'phrases.blog_title' => 'Artículos y reflexiones',
        'phrases.blog_description' => 'Recursos prácticos para cuidar tu salud mental.',
        // FAQ
        'phrases.faq_overline' => 'Preguntas frecuentes',
        'phrases.faq_title' => 'Resolvemos tus dudas',
        'phrases.faq_description' => 'Las preguntas más habituales antes de empezar.',
        // Cita
        'phrases.cita_overline' => 'Pide cita',
        'phrases.cita_title' => 'Reserva tu sesión',
        'phrases.cita_description' => 'Elige el día y la hora que mejor te convenga.',
    ];

    public function update(Request $request, string $seccion)
    {
        $allowed = [
            'hero' => ['phrases.hero_badge', 'phrases.hero_overline', 'phrases.hero_cta', 'phrases.hero_frase'],
            'about' => ['phrases.about_overline', 'phrases.about_title', 'phrases.about_pie_foto'],
            'servicios' => ['phrases.servicios_overline', 'phrases.servicios_title', 'phrases.servicios_description'],
            'especialidades' => ['phrases.especialidades_overline', 'phrases.especialidades_title', 'phrases.especialidades_description'],
            'planes' => ['phrases.planes_overline', 'phrases.planes_title', 'phrases.planes_description'],
            'blog' => ['phrases.blog_overline', 'phrases.blog_title', 'phrases.blog_description'],
            'faq' => ['phrases.faq_overline', 'phrases.faq_title', 'phrases.faq_description'],
            'cita' => ['phrases.cita_overline', 'phrases.cita_title', 'phrases.cita_description'],
        ];

        if (!isset($allowed[$seccion])) {
            return back()->with('error', 'Sección desconocida.');
        }

        $rules = [];
        foreach ($allowed[$seccion] as $key) {
            $shortKey = str_replace('phrases.', '', $key);
            $maxLen = str_ends_with($shortKey, '_overline') ? 28 : 300;
            $rules[$shortKey] = ['nullable', 'string', 'max:' . $maxLen];
        }
        $data = $request->validate($rules);

        foreach ($allowed[$seccion] as $key) {
            $shortKey = str_replace('phrases.', '', $key);
            $value = trim((string) ($data[$shortKey] ?? ''));
            Setting::set($key, $value !== '' ? $value : null, 'phrases');
        }

        return back()->with('success', 'Frases actualizadas correctamente.');
    }
}
