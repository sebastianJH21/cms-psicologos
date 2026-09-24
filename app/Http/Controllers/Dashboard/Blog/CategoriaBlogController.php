<?php

namespace App\Http\Controllers\Dashboard\Blog;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Blog\CategoriaRequest;
use App\Models\CategoriaBlog;
use App\Services\SlugService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class CategoriaBlogController extends Controller
{
    public function __construct(private SlugService $slugService)
    {
    }

    public function index(): View
    {
        $categorias = CategoriaBlog::query()
            ->withCount('articulos')
            ->orderBy('orden')
            ->orderBy('nombre')
            ->paginate(20);

        return view('dashboard.blog.categorias.index', compact('categorias'));
    }

    public function create(): View
    {
        return view('dashboard.blog.categorias.create');
    }

    public function store(CategoriaRequest $request): RedirectResponse
    {
        $datos = $request->validated();
        $datos['slug'] = $datos['slug'] ?: $this->slugService->uniqueSlug($datos['nombre'], CategoriaBlog::class);
        $datos['orden'] = $datos['orden'] ?? (CategoriaBlog::max('orden') + 1);

        CategoriaBlog::create($datos);

        return redirect()
            ->route('dashboard.blog.categorias.index')
            ->with('success', 'Categoría creada correctamente.');
    }

    public function edit(CategoriaBlog $categoria): View
    {
        return view('dashboard.blog.categorias.edit', compact('categoria'));
    }

    public function update(CategoriaRequest $request, CategoriaBlog $categoria): RedirectResponse
    {
        $datos = $request->validated();
        $datos['slug'] = $datos['slug'] ?: $this->slugService->uniqueSlug($datos['nombre'], CategoriaBlog::class, 'slug', $categoria->id);

        $categoria->update($datos);

        return redirect()
            ->route('dashboard.blog.categorias.index')
            ->with('success', 'Categoría actualizada.');
    }

    public function destroy(CategoriaBlog $categoria): RedirectResponse
    {
        $categoria->delete();

        return redirect()
            ->route('dashboard.blog.categorias.index')
            ->with('success', 'Categoría eliminada. Los artículos asociados quedan sin categoría.');
    }
}
