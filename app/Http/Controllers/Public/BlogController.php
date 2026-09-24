<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\Articulo;
use App\Models\CategoriaBlog;
use App\Models\Setting;
use App\Services\ThemeManager;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    public function index(Request $request, ThemeManager $themes)
    {
        if (!Setting::get('features.blog_enabled', true)) {
            abort(404);
        }

        $query = Articulo::publicados()->orderByDesc('published_at');

        if ($categoriaSlug = $request->query('categoria')) {
            $query->whereHas('categoria', fn ($qb) => $qb->where('slug', $categoriaSlug));
        }

        $articulos = $query->paginate(9)->appends($request->query());

        $data = [
            'articulos'       => $articulos,
            'categorias'      => CategoriaBlog::orderBy('orden')->get(),
            'categoriaActual' => $categoriaSlug,
        ];

        if (($request->ajax() || $request->wantsJson() || $request->boolean('fragment')) && view()->exists('theme::multipage.blog-fragment')) {
            return view('theme::multipage.blog-fragment', $data);
        }

        return view('theme::multipage.blog', $data);
    }

    public function rss()
    {
        if (!Setting::get('features.blog_enabled', true)) {
            abort(404);
        }

        $articulos = Articulo::publicados()->orderByDesc('published_at')->limit(20)->get();
        $user = \App\Models\User::first();
        $profile = \App\Models\Profile::first();
        $tituloSitio = trim(($user?->nombre ?? '') . ' ' . ($user?->apellidos ?? '')) ?: 'Blog';

        return response()->view('feeds.blog-rss', [
            'articulos'   => $articulos,
            'tituloSitio' => $tituloSitio,
            'descripcion' => $profile?->slogan ?: 'Artículos de psicología',
        ], 200, ['Content-Type' => 'application/rss+xml; charset=utf-8']);
    }

    public function show(string $slug, ThemeManager $themes)
    {
        if (!Setting::get('features.blog_enabled', true)) {
            abort(404);
        }

        $articulo = Articulo::publicados()->where('slug', $slug)->firstOrFail();

        $relacionados = Articulo::publicados()
            ->where('id', '!=', $articulo->id)
            ->when($articulo->categoria_id, fn ($q) => $q->where('categoria_id', $articulo->categoria_id))
            ->orderByDesc('published_at')
            ->limit(3)
            ->get();

        return view('theme::multipage.blog-articulo', [
            'articulo'     => $articulo,
            'relacionados' => $relacionados,
        ]);
    }
}
