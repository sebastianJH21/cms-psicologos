<?php

namespace App\Http\Controllers\Dashboard\Configuracion;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class ConfiguracionGeneralController extends Controller
{
    public function edit()
    {
        $features = [
            'blog'      => Setting::get('features.blog_enabled', true),
            'reservas'  => Setting::get('features.reservas_enabled', true),
            'faq'       => Setting::get('features.faq_enabled', true),
            'servicios' => Setting::get('features.servicios_enabled', true),
            'sobre_mi'  => Setting::get('features.sobre_mi_enabled', true),
        ];

        return view('dashboard.configuracion.general.edit', compact('features'));
    }

    public function update(Request $request)
    {
        $map = [
            'features.blog_enabled'      => (bool) $request->boolean('blog'),
            'features.reservas_enabled'  => (bool) $request->boolean('reservas'),
            'features.faq_enabled'       => (bool) $request->boolean('faq'),
            'features.servicios_enabled' => (bool) $request->boolean('servicios'),
            'features.sobre_mi_enabled'  => (bool) $request->boolean('sobre_mi'),
        ];

        foreach ($map as $key => $value) {
            Setting::set($key, $value, 'features');
        }

        return back()->with('success', 'Configuración de funcionalidades actualizada.');
    }

    public function toggleFeature(Request $request)
    {
        $request->validate([
            'feature' => 'required|in:blog,reservas,faq,servicios,sobre_mi',
            'value'   => 'required|in:0,1',
        ]);

        $key = 'features.' . $request->feature . '_enabled';
        Setting::set($key, (bool)(int) $request->value, 'features');

        return response()->json(['ok' => true]);
    }

    public function guardarTema(Request $request)
    {
        $request->validate([
            'theme_mode'    => 'required|in:light,dark',
            'primary_color' => 'required|regex:/^#[0-9a-fA-F]{6}$/',
        ]);

        $user = auth()->user();
        $user->theme_preference = $request->theme_mode;
        $user->primary_color = $request->primary_color;
        $user->save();

        return response()->json(['ok' => true]);
    }
}
