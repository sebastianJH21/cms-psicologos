<?php

namespace Tests\Feature\Public;

use App\Models\Disponibilidad;
use App\Models\Setting;
use App\Services\CitaService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Crypt;
use Tests\TestCase;

class ReservaPublicaTest extends TestCase
{
    use RefreshDatabase;

    private Carbon $fecha;

    protected function setUp(): void
    {
        parent::setUp();

        $this->fecha = Carbon::now()->addDays(7)->startOfDay();

        Setting::set('disponibilidad.duracion_sesion_online_min', 50, 'disponibilidad');
        Setting::set('disponibilidad.descanso_activo_online', true, 'disponibilidad');
        Setting::set('disponibilidad.descanso_min_online', 10, 'disponibilidad');
        Setting::set('disponibilidad.hora_apertura_manana', '09:00', 'disponibilidad');
        Setting::set('disponibilidad.hora_cierre_manana', '14:00', 'disponibilidad');
        Setting::set('disponibilidad.modo_vacaciones', false, 'disponibilidad');

        foreach ((new CitaService())->generarSlots('09:00', '14:00', 50, 10) as $slot) {
            Disponibilidad::create([
                'modalidad' => 'online',
                'dia_semana' => (int) $this->fecha->dayOfWeek,
                'hora_inicio' => $slot['inicio'] . ':00',
                'hora_fin' => Carbon::createFromFormat('H:i', $slot['inicio'])->addMinutes(50)->format('H:i:s'),
                'activa' => true,
            ]);
        }
    }

    /** @return array<string, mixed> */
    private function datosValidos(array $overrides = []): array
    {
        $token = Crypt::encryptString('7|' . time());

        return array_merge([
            'modalidad' => 'online',
            'fecha_hora' => $this->fecha->copy()->setTime(9, 0)->toDateTimeString(),
            'nombre' => 'Ana Ejemplo',
            'telefono' => '600 11 22 33',
            'motivo' => 'Primera consulta',
            'website' => '',
            'privacidad' => '1',
            'captcha_token' => $token,
            'captcha' => '7',
        ], $overrides);
    }

    public function test_el_campo_trampa_relleno_rechaza_el_envio(): void
    {
        $response = $this->postJson('/reservas/crear', $this->datosValidos(['website' => 'https://spam.example']));

        $response->assertStatus(422);
        $this->assertDatabaseCount('citas', 0);
    }

    public function test_la_respuesta_de_seguridad_incorrecta_rechaza_el_envio(): void
    {
        $response = $this->postJson('/reservas/crear', $this->datosValidos(['captcha' => '1']));

        $response->assertStatus(422);
        $this->assertDatabaseCount('citas', 0);
    }

    public function test_una_reserva_correcta_crea_paciente_y_cita_y_da_enlace_de_calendario(): void
    {
        $response = $this->postJson('/reservas/crear', $this->datosValidos());

        $response->assertOk();
        $response->assertJsonPath('ok', true);
        $response->assertJsonStructure(['cita', 'google_calendar']);
        $this->assertDatabaseCount('citas', 1);
        $this->assertDatabaseHas('pacientes', ['telefono' => '600112233']);
    }

    public function test_no_se_puede_reservar_un_hueco_ya_ocupado(): void
    {
        $primera = $this->postJson('/reservas/crear', $this->datosValidos());
        $primera->assertOk();

        $segunda = $this->postJson('/reservas/crear', $this->datosValidos([
            'telefono' => '699887766',
            'captcha_token' => Crypt::encryptString('7|' . time()),
        ]));

        $segunda->assertStatus(422);
        $this->assertDatabaseCount('citas', 1);
    }
}
