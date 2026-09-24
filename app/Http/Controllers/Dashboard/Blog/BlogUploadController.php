<?php

namespace App\Http\Controllers\Dashboard\Blog;

use App\Http\Controllers\Controller;
use App\Services\ImagenOptimizer;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BlogUploadController extends Controller
{
    public function __construct(private ImagenOptimizer $optimizador)
    {
    }

    public function imagen(Request $request): JsonResponse
    {
        $request->validate([
            'files.*' => ['required', 'image', 'mimes:jpg,jpeg,png,webp,gif', 'max:4096'],
            'files' => ['required', 'array'],
        ]);

        $resultado = [];

        foreach ($request->file('files', []) as $archivo) {
            $path = $this->optimizador->procesarYGuardar(
                $archivo,
                'blog/inline',
                1200,
                82
            );
            $resultado[] = [
                'name' => basename($path),
                'url' => asset('storage/' . $path),
            ];
        }

        return response()->json([
            'success' => true,
            'files' => $resultado,
        ]);
    }
}
