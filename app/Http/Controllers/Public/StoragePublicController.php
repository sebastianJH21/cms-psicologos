<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;

class StoragePublicController extends Controller
{
    private const MIME_TYPES = [
        'svg'  => 'image/svg+xml',
        'png'  => 'image/png',
        'jpg'  => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif'  => 'image/gif',
        'webp' => 'image/webp',
        'ico'  => 'image/x-icon',
        'pdf'  => 'application/pdf',
    ];

    public function serve(string $path)
    {
        $basePath = realpath(storage_path('app/public'));
        if (!$basePath) {
            abort(404);
        }

        $fullPath = realpath($basePath . DIRECTORY_SEPARATOR . $path);
        if (!$fullPath || !str_starts_with($fullPath, $basePath) || !is_file($fullPath)) {
            abort(404);
        }

        $extension = strtolower(pathinfo($fullPath, PATHINFO_EXTENSION));
        $mimeType = self::MIME_TYPES[$extension] ?? 'application/octet-stream';

        return response()->file($fullPath, [
            'Content-Type'  => $mimeType,
            'Cache-Control' => 'public, max-age=31536000',
        ]);
    }
}
