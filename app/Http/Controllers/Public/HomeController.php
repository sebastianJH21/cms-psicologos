<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Articulo;
use App\Models\Faq;
use App\Models\PlanPrecio;
use App\Models\Servicio;
use App\Models\Terapia;
use App\Services\InstallerService;
use App\Services\ThemeManager;

class HomeController extends Controller
{
    public function index(InstallerService $installer, ThemeManager $themes)
    {
        if (!$installer->isInstalled()) {
            return redirect('/instalacion');
        }

        $data = $this->datosBase();

        if ($themes->activeMode() === 'multipage') {
            return view('theme::multipage.home', $data);
        }

        return view('theme::landing', $data);
    }

    public static function datosBase(): array
    {
        return [
            'servicios' => Servicio::where('activo', true)->orderBy('orden')->get(),
            'terapias'  => Terapia::where('activo', true)->orderBy('orden')->get(),
            'planes'    => PlanPrecio::orderBy('orden')->get(),
            'articulos' => Articulo::publicados()->orderByDesc('published_at')->limit(3)->get(),
            'faqs'      => Faq::where('activa', true)->orderBy('orden')->get(),
        ];
    }
}
