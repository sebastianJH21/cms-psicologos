<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\PlanPrecio;
use App\Models\Servicio;
use App\Models\Setting;
use App\Models\Terapia;
use App\Services\ThemeManager;

class ServiciosController extends Controller
{
    public function index(ThemeManager $themes)
    {
        if ($themes->activeMode() !== 'multipage') {
            return redirect('/#servicios');
        }

        if (!Setting::get('features.servicios_enabled', true)) {
            abort(404);
        }

        return view('theme::multipage.servicios', [
            'servicios' => Servicio::where('activo', true)->orderBy('orden')->get(),
            'terapias'  => Terapia::where('activo', true)->orderBy('orden')->get(),
            'planes'    => PlanPrecio::orderBy('orden')->get(),
        ]);
    }
}
