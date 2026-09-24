<?php

namespace Database\Seeders;

use App\Models\CategoriaBlog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CategoriasBlogSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = [
            ['nombre' => 'Ansiedad', 'descripcion' => 'Recursos para entender y gestionar la ansiedad.'],
            ['nombre' => 'Depresión', 'descripcion' => 'Información y herramientas frente a la depresión.'],
            ['nombre' => 'Autoestima', 'descripcion' => 'Claves para fortalecer tu autoestima.'],
            ['nombre' => 'Relaciones', 'descripcion' => 'Vínculos sanos y resolución de conflictos.'],
            ['nombre' => 'Mindfulness', 'descripcion' => 'Atención plena y prácticas de meditación.'],
            ['nombre' => 'Trauma', 'descripcion' => 'Procesos de superación y trauma terapéutico.'],
        ];

        foreach ($categorias as $i => $cat) {
            CategoriaBlog::firstOrCreate(
                ['slug' => Str::slug($cat['nombre'])],
                [
                    'nombre' => $cat['nombre'],
                    'descripcion' => $cat['descripcion'],
                    'orden' => $i + 1,
                ]
            );
        }
    }
}
