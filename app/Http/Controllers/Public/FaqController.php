<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use App\Models\Setting;
use App\Services\ThemeManager;

class FaqController extends Controller
{
    public function index(ThemeManager $themes)
    {
        if ($themes->activeMode() !== 'multipage') {
            return redirect('/#faq');
        }

        if (!Setting::get('features.faq_enabled', true)) {
            abort(404);
        }

        return view('theme::multipage.faq', [
            'faqs' => Faq::where('activa', true)->orderBy('orden')->get(),
        ]);
    }
}
