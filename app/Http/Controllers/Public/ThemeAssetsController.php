<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class ThemeAssetsController extends Controller
{
    private const MIME_TYPES = [
        'css'   => 'text/css; charset=utf-8',
        'js'    => 'application/javascript; charset=utf-8',
        'mjs'   => 'application/javascript; charset=utf-8',
        'json'  => 'application/json; charset=utf-8',
        'svg'   => 'image/svg+xml',
        'png'   => 'image/png',
        'jpg'   => 'image/jpeg',
        'jpeg'  => 'image/jpeg',
        'gif'   => 'image/gif',
        'webp'  => 'image/webp',
        'ico'   => 'image/x-icon',
        'woff'  => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf'   => 'font/ttf',
        'otf'   => 'font/otf',
        'eot'   => 'application/vnd.ms-fontobject',
        'mp4'   => 'video/mp4',
        'webm'  => 'video/webm',
        'pdf'   => 'application/pdf',
        'html'  => 'text/html; charset=utf-8',
        'txt'   => 'text/plain; charset=utf-8',
    ];

    public function serve(string $slug, string $path)
    {
        $basePath = base_path("themes/{$slug}");
        if (!realpath($basePath)) {
            abort(404, 'Tema no encontrado');
        }

        $fullPath = realpath($basePath . '/' . $path);
        if (!$fullPath || !str_starts_with($fullPath, realpath($basePath))) {
            abort(404, 'Archivo no encontrado o acceso denegado');
        }

        if (!file_exists($fullPath)) {
            abort(404, 'Archivo no encontrado');
        }

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mimeType = self::MIME_TYPES[$extension] ?? 'application/octet-stream';

        return response()->file($fullPath, [
            'Content-Type'  => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
