<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Route as RouteFacade;
use Tests\TestCase;

class RouteProtectionTest extends TestCase
{
    use RefreshDatabase;

    /** @return list<string> */
    private function rutasDelPanel(): array
    {
        $rutas = [];
        foreach (RouteFacade::getRoutes() as $route) {
            if (!str_starts_with((string) $route->getName(), 'dashboard.')) {
                continue;
            }
            if (!in_array('GET', $route->methods(), true)) {
                continue;
            }
            if (str_contains($route->uri(), '{')) {
                continue;
            }
            $rutas[] = '/' . ltrim($route->uri(), '/');
        }

        return array_values(array_unique($rutas));
    }

    public function test_todas_las_rutas_get_del_panel_redirigen_al_login_sin_sesion(): void
    {
        $rutas = $this->rutasDelPanel();
        $this->assertNotEmpty($rutas, 'No se ha encontrado ninguna ruta dashboard.* para comprobar.');

        foreach ($rutas as $uri) {
            $response = $this->get($uri);
            $response->assertRedirect('/acceso-psicologa');
        }
    }

    public function test_con_sesion_iniciada_el_inicio_del_panel_es_accesible(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/panel-psicologa');

        $response->assertOk();
    }

    public function test_las_acciones_de_escritura_tambien_exigen_sesion(): void
    {
        $response = $this->post('/panel-psicologa/citas', []);

        $response->assertRedirect('/acceso-psicologa');
    }
}
