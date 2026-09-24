<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\Terapia;
use App\Services\ThemeManager;

class SobreMiController extends Controller
{
    public function index(ThemeManager $themes)
    {
        if ($themes->activeMode() !== 'multipage') {
            return redirect('/#sobre-mi');
        }

        if (!Setting::get('features.sobre_mi_enabled', true)) {
            abort(404);
        }

        return view('theme::multipage.sobre-mi', [
            'terapias' => Terapia::where('activo', true)->orderBy('orden')->get(),
        ]);
    }
}
