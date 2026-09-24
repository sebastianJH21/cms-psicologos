<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ImagenOptimizer
{
    private const MIMES_PROCESABLES = [
        'image/jpeg',
        'image/jpg',
        'image/png',
        'image/webp',
    ];

    public function procesarYGuardar(
        UploadedFile $file,
        string $directorio,
        int $maxWidth = 1600,
        int $calidad = 82,
        string $disco = 'public',
        ?string $nombreFijo = null
    ): string {
        $mime = strtolower((string) $file->getMimeType());
        $ext = strtolower($file->extension() ?: $file->getClientOriginalExtension() ?: 'jpg');

        $nombreBase = $nombreFijo
            ? trim($nombreFijo, '/')
            : Str::random(24);

        $dir = trim($directorio, '/');
        $rutaRelativa = $dir . '/' . $nombreBase . '.' . $ext;

        $mimeProcesable = in_array($mime, self::MIMES_PROCESABLES, true);
        $hayProcesador = $this->extensionDisponible();

        if (!$mimeProcesable || !$hayProcesador) {
            return $file->storeAs($dir, $nombreBase . '.' . $ext, $disco);
        }

        try {
            $manager = $this->managerImagen();
            $imagen = $manager->read($file->getRealPath());

            if ($imagen->width() > $maxWidth) {
                $imagen->scaleDown(width: $maxWidth);
            }

            $codificada = match ($ext) {
                'png'  => $imagen->toPng(),
                'webp' => $imagen->toWebp($calidad),
                default => $imagen->toJpeg($calidad),
            };

            Storage::disk($disco)->put($rutaRelativa, (string) $codificada);

            return $rutaRelativa;
        } catch (\Throwable $e) {
            Log::warning('ImagenOptimizer falló, guardando original', [
                'error' => $e->getMessage(),
                'ruta'  => $rutaRelativa,
            ]);
            return $file->storeAs($dir, $nombreBase . '.' . $ext, $disco);
        }
    }

    private function extensionDisponible(): bool
    {
        return extension_loaded('gd') || extension_loaded('imagick');
    }

    private function managerImagen(): \Intervention\Image\ImageManager
    {
        if (extension_loaded('imagick')) {
            return new \Intervention\Image\ImageManager(
                new \Intervention\Image\Drivers\Imagick\Driver()
            );
        }

        return new \Intervention\Image\ImageManager(
            new \Intervention\Image\Drivers\Gd\Driver()
        );
    }
}
