<?php

namespace Tests\Feature\ProteccionDatos;

use App\Models\Paciente;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_descarga_la_plantilla_vacia_en_pdf(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/panel-psicologa/configuracion/proteccion-datos/descargar-vacio');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_descarga_el_pdf_relleno_con_los_datos_del_paciente(): void
    {
        $user = User::factory()->create();
        $paciente = Paciente::create([
            'nombre' => 'Ana',
            'apellidos' => 'Ejemplo',
            'telefono' => '600112233',
            'origen' => 'manual',
        ]);

        $response = $this->actingAs($user)
            ->get("/panel-psicologa/pacientes/{$paciente->id}/proteccion-datos.pdf");

        $response->assertOk();
        $response->assertHeader('content-type', 'application/pdf');
    }

    public function test_sin_sesion_no_se_puede_descargar_el_pdf(): void
    {
        $paciente = Paciente::create([
            'nombre' => 'Ana',
            'telefono' => '600112233',
            'origen' => 'manual',
        ]);

        $response = $this->get("/panel-psicologa/pacientes/{$paciente->id}/proteccion-datos.pdf");

        $response->assertRedirect('/acceso-psicologa');
    }
}
