<?php

namespace App\Http\Controllers\Dashboard\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Blog\ArticuloRequest;
use App\Models\Articulo;
use App\Models\CategoriaBlog;
use App\Services\ImagenOptimizer;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Mews\Purifier\Facades\Purifier;

class ArticuloController extends Controller
{
    public function __construct(
        private SlugService $slugService,
        private ImagenOptimizer $optimizador
    ) {
    }

    public function index(Request $request)
    {
        $query = Articulo::query()
            ->with('categoria')
            ->orderByDesc('created_at');

        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        if ($categoria = $request->input('categoria_id')) {
            $query->where('categoria_id', $categoria);
        }

        if ($buscar = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($buscar) {
                $q->where('titulo', 'like', "%{$buscar}%")
                    ->orWhere('extracto', 'like', "%{$buscar}%");
            });
        }

        $articulos = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('dashboard.blog.articulos.partials.tabla', compact('articulos'))->render(),
            ]);
        }

        return view('dashboard.blog.articulos.index', [
            'articulos' => $articulos,
            'categorias' => CategoriaBlog::orderBy('nombre')->get(),
            'estados' => Articulo::ESTADOS,
            'filtros' => [
                'estado' => $request->input('estado', ''),
                'categoria_id' => $request->input('categoria_id', ''),
                'q' => $request->input('q', ''),
            ],
        ]);
    }

    public function create(): View
    {
        return view('dashboard.blog.articulos.create', [
            'categorias' => CategoriaBlog::orderBy('nombre')->get(),
            'estados' => Articulo::ESTADOS,
        ]);
    }

    public function store(ArticuloRequest $request): RedirectResponse
    {
        $datos = $this->prepararDatos($request);

        if ($request->hasFile('imagen')) {
            $datos['imagen_path'] = $this->optimizador->procesarYGuardar(
                $request->file('imagen'),
                'blog',
                1200,
                82
            );
        }

        $articulo = Articulo::create($datos);

        return redirect()
            ->route('dashboard.blog.articulos.edit', $articulo)
            ->with('success', 'Artículo creado correctamente.');
    }

    public function show(Articulo $articulo): View
    {
        $articulo->load('categoria');
        return view('dashboard.blog.articulos.show', compact('articulo'));
    }

    public function edit(Articulo $articulo): View
    {
        return view('dashboard.blog.articulos.edit', [
            'articulo' => $articulo,
            'categorias' => CategoriaBlog::orderBy('nombre')->get(),
            'estados' => Articulo::ESTADOS,
        ]);
    }

    public function update(ArticuloRequest $request, Articulo $articulo): RedirectResponse
    {
        $datos = $this->prepararDatos($request, $articulo);

        if ($request->boolean('eliminar_imagen') && $articulo->imagen_path) {
            Storage::disk('public')->delete($articulo->imagen_path);
            $datos['imagen_path'] = null;
        }

        if ($request->hasFile('imagen')) {
            if ($articulo->imagen_path) {
                Storage::disk('public')->delete($articulo->imagen_path);
            }
            $datos['imagen_path'] = $this->optimizador->procesarYGuardar(
                $request->file('imagen'),
                'blog',
                1200,
                82
            );
        }

        $articulo->update($datos);

        return redirect()
            ->route('dashboard.blog.articulos.edit', $articulo)
            ->with('success', 'Artículo actualizado.');
    }

    public function destroy(Articulo $articulo): RedirectResponse
    {
        $articulo->delete();

        return redirect()
            ->route('dashboard.blog.articulos.index')
            ->with('success', 'Artículo enviado a la papelera.');
    }

    private function prepararDatos(ArticuloRequest $request, ?Articulo $articulo = null): array
    {
        $datos = $request->validated();

        $datos['slug'] = $datos['slug']
            ?: $this->slugService->uniqueSlug($datos['titulo'], Articulo::class, 'slug', $articulo?->id);

        $datos['contenido'] = Purifier::clean($datos['contenido'], 'blog');

        if ($datos['estado'] === 'publicado' && !empty($datos['published_at'])) {
            $datos['published_at'] = $datos['published_at'];
        } elseif ($datos['estado'] !== 'publicado') {
            $datos['published_at'] = $datos['published_at'] ?? null;
        }

        unset($datos['imagen'], $datos['eliminar_imagen']);

        return $datos;
    }
}
