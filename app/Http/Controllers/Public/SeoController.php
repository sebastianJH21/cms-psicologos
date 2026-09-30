<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Articulo;
use App\Models\Setting;
use App\Services\InstallerService;
use App\Services\ThemeManager;
use Illuminate\Http\Response;

class SeoController extends Controller
{
    public function robots(): Response
    {
        $lineas = [
            'User-agent: *',
            'Disallow: /panel-psicologa',
            'Disallow: /acceso-psicologa',
            'Disallow: /instalacion',
            'Disallow: /recuperar-pwd',
            '',
            'Sitemap: ' . url('/sitemap.xml'),
        ];

        return response(implode("\n", $lineas), 200)
            ->header('Content-Type', 'text/plain; charset=UTF-8');
    }

    public function sitemap(ThemeManager $themes, InstallerService $installer): Response
    {
        $urls = [url('/')];

        if (!$installer->isInstalled()) {
            $xml = view('feeds.sitemap', ['urls' => $urls])->render();

            return response($xml, 200)->header('Content-Type', 'application/xml; charset=UTF-8');
        }

        if ($themes->activeMode() === 'multipage') {
            if (Setting::get('features.sobre_mi_enabled', true)) {
                $urls[] = url('/sobre-mi');
            }
            if (Setting::get('features.servicios_enabled', true)) {
                $urls[] = url('/servicios');
            }
            if (Setting::get('features.faq_enabled', true)) {
                $urls[] = url('/preguntas-frecuentes');
            }
            if (Setting::get('features.reservas_enabled', true)) {
                $urls[] = url('/pide-cita');
            }
        }

        $urls[] = url('/politica-de-privacidad');

        if (Setting::get('features.blog_enabled', true)) {
            $urls[] = url('/blog');

            Articulo::publicados()
                ->orderByDesc('published_at')
                ->pluck('slug')
                ->each(function (string $slug) use (&$urls) {
                    $urls[] = url('/blog/' . $slug);
                });
        }

        $xml = view('feeds.sitemap', ['urls' => $urls])->render();

        return response($xml, 200)
            ->header('Content-Type', 'application/xml; charset=UTF-8');
    }
}
