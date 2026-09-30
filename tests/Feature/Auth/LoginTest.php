<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    private function crearPsicologa(): User
    {
        return User::factory()->create([
            'email' => 'psicologa@example.com',
            'telefono' => '600112233',
            'password' => bcrypt('ClaveSegura123'),
        ]);
    }

    public function test_exige_los_tres_campos(): void
    {
        $response = $this->post('/acceso-psicologa', []);

        $response->assertSessionHasErrors(['email', 'telefono', 'password']);
        $this->assertGuest();
    }

    public function test_login_correcto_con_email_telefono_y_password(): void
    {
        $this->crearPsicologa();

        $response = $this->post('/acceso-psicologa', [
            'email' => 'psicologa@example.com',
            'telefono' => '600 11 22 33',
            'password' => 'ClaveSegura123',
        ]);

        $response->assertRedirect('/panel-psicologa');
        $this->assertAuthenticated();
    }

    public function test_telefono_incorrecto_da_error_generico(): void
    {
        $this->crearPsicologa();

        $response = $this->post('/acceso-psicologa', [
            'email' => 'psicologa@example.com',
            'telefono' => '699999999',
            'password' => 'ClaveSegura123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_bloquea_tras_varios_intentos_fallidos(): void
    {
        $this->crearPsicologa();

        for ($i = 0; $i < 5; $i++) {
            $this->post('/acceso-psicologa', [
                'email' => 'psicologa@example.com',
                'telefono' => '600112233',
                'password' => 'clave-incorrecta',
            ]);
        }

        $response = $this->post('/acceso-psicologa', [
            'email' => 'psicologa@example.com',
            'telefono' => '600112233',
            'password' => 'ClaveSegura123',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
    }

    public function test_logout_invalida_la_sesion(): void
    {
        $user = $this->crearPsicologa();

        $response = $this->actingAs($user)->post('/logout');

        $response->assertRedirect('/acceso-psicologa');
        $this->assertGuest();
    }
}
