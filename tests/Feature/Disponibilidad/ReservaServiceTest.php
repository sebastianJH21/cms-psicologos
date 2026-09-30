<?php

namespace Tests\Feature\Disponibilidad;

use App\Models\Cita;
use App\Models\Disponibilidad;
use App\Models\PeriodoVacaciones;
use App\Models\Setting;
use App\Services\PacienteService;
use App\Services\CitaService;
use App\Services\ReservaService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReservaServiceTest extends TestCase
{
    use RefreshDatabase;

    private function configurarDisponibilidadOnline(Carbon $fecha): void
    {
        Setting::set('disponibilidad.duracion_sesion_online_min', 50, 'disponibilidad');
        Setting::set('disponibilidad.descanso_activo_online', true, 'disponibilidad');
        Setting::set('disponibilidad.descanso_min_online', 10, 'disponibilidad');
        Setting::set('disponibilidad.hora_apertura_manana', '09:00', 'disponibilidad');
        Setting::set('disponibilidad.hora_cierre_manana', '14:00', 'disponibilidad');
        Setting::set('disponibilidad.modo_vacaciones', false, 'disponibilidad');

        // El panel guarda una fila por cada hueco marcado en la cuadrícula
        // (no un único rango horario), con hora_fin = hora_inicio + duración.
        foreach ((new CitaService())->generarSlots('09:00', '14:00', 50, 10) as $slot) {
            Disponibilidad::create([
                'modalidad' => 'online',
                'dia_semana' => (int) $fecha->dayOfWeek,
                'hora_inicio' => $slot['inicio'] . ':00',
                'hora_fin' => Carbon::createFromFormat('H:i', $slot['inicio'])->addMinutes(50)->format('H:i:s'),
                'activa' => true,
            ]);
        }
    }

    private function servicio(): ReservaService
    {
        return new ReservaService(new CitaService(), new PacienteService());
    }

    public function test_huecos_libres_respetan_duracion_y_descanso(): void
    {
        $fecha = Carbon::now()->addDays(7)->startOfDay();
        $this->configurarDisponibilidadOnline($fecha);

        $slots = $this->servicio()->slotsDisponibles('online', $fecha->toDateString());

        $this->assertSame(
            ['09:00', '10:00', '11:00', '12:00', '13:00'],
            array_column($slots, 'hora')
        );
    }

    public function test_una_cita_existente_bloquea_su_hueco(): void
    {
        $fecha = Carbon::now()->addDays(7)->startOfDay();
        $this->configurarDisponibilidadOnline($fecha);

        Cita::create([
            'nombre_provisional' => 'Paciente de prueba',
            'telefono_provisional' => '600000001',
            'modalidad' => 'online',
            'fecha_inicio' => $fecha->copy()->setTime(10, 0),
            'fecha_fin' => $fecha->copy()->setTime(10, 50),
            'estado' => 'pendiente',
            'origen' => 'manual',
        ]);

        $slots = $this->servicio()->slotsDisponibles('online', $fecha->toDateString());

        $this->assertSame(
            ['09:00', '11:00', '12:00', '13:00'],
            array_column($slots, 'hora')
        );
    }

    public function test_un_periodo_de_vacaciones_bloquea_el_dia_completo(): void
    {
        $fecha = Carbon::now()->addDays(7)->startOfDay();
        $this->configurarDisponibilidadOnline($fecha);

        PeriodoVacaciones::create([
            'fecha_inicio' => $fecha->copy()->subDay(),
            'fecha_fin' => $fecha->copy()->addDay(),
        ]);

        $slots = $this->servicio()->slotsDisponibles('online', $fecha->toDateString());

        $this->assertSame([], $slots);
    }

    public function test_el_modo_vacaciones_general_bloquea_todas_las_reservas(): void
    {
        $fecha = Carbon::now()->addDays(7)->startOfDay();
        $this->configurarDisponibilidadOnline($fecha);
        Setting::set('disponibilidad.modo_vacaciones', true, 'disponibilidad');

        $slots = $this->servicio()->slotsDisponibles('online', $fecha->toDateString());

        $this->assertSame([], $slots);
    }

    public function test_registrar_crea_paciente_y_cita_vinculados_por_telefono(): void
    {
        $fecha = Carbon::now()->addDays(7)->startOfDay();
        $this->configurarDisponibilidadOnline($fecha);

        $cita = $this->servicio()->registrar([
            'nombre' => 'Ana Ejemplo',
            'telefono' => ' 600 11 22 33 ',
            'modalidad' => 'online',
            'fecha_hora' => $fecha->copy()->setTime(9, 0)->toDateTimeString(),
            'motivo' => 'Primera consulta',
        ]);

        $this->assertSame('600112233', $cita->paciente->telefono);
        $this->assertSame('pendiente', $cita->estado);
    }

    public function test_no_se_puede_reservar_dos_veces_el_mismo_hueco(): void
    {
        $fecha = Carbon::now()->addDays(7)->startOfDay();
        $this->configurarDisponibilidadOnline($fecha);

        $datos = [
            'nombre' => 'Primer paciente',
            'telefono' => '600222222',
            'modalidad' => 'online',
            'fecha_hora' => $fecha->copy()->setTime(9, 0)->toDateTimeString(),
        ];
        $this->servicio()->registrar($datos);

        $this->expectException(\RuntimeException::class);
        $datos['telefono'] = '600333333';
        $this->servicio()->registrar($datos);
    }

    public function test_un_mismo_paciente_no_puede_reservar_dos_citas_el_mismo_dia(): void
    {
        $fecha = Carbon::now()->addDays(7)->startOfDay();
        $this->configurarDisponibilidadOnline($fecha);

        $this->servicio()->registrar([
            'nombre' => 'Ana Ejemplo',
            'telefono' => '600444444',
            'modalidad' => 'online',
            'fecha_hora' => $fecha->copy()->setTime(9, 0)->toDateTimeString(),
        ]);

        $this->expectException(\RuntimeException::class);
        $this->servicio()->registrar([
            'nombre' => 'Ana Ejemplo',
            'telefono' => '600444444',
            'modalidad' => 'online',
            'fecha_hora' => $fecha->copy()->setTime(11, 0)->toDateTimeString(),
        ]);
    }
}
