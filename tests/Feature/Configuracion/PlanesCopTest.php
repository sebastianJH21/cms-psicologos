<?php

namespace Tests\Feature\Configuracion;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PlanesCopTest extends TestCase
{
    use RefreshDatabase;

    public function test_admite_un_precio_de_siete_cifras_en_pesos_colombianos(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('dashboard.configuracion.planes.store'), [
            'tipo' => 'online',
            'nombre' => 'Sesión individual',
            'precio' => 1200000,
            'duracion_min' => 50,
        ]);

        $response->assertSessionDoesntHaveErrors('precio');
        $this->assertDatabaseHas('planes_precios', [
            'nombre' => 'Sesión individual',
            'precio' => 1200000,
        ]);
    }

    public function test_rechaza_un_precio_negativo(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('dashboard.configuracion.planes.store'), [
            'tipo' => 'online',
            'nombre' => 'Sesión individual',
            'precio' => -1,
            'duracion_min' => 50,
        ]);

        $response->assertSessionHasErrors('precio');
    }
}
