<?php

namespace Tests\Unit\Services;

use App\Models\Cita;
use App\Services\CitaService;
use App\Services\PacienteService;
use App\Services\ReservaService;
use Carbon\Carbon;
use Tests\TestCase;

class ReservaServiceTimezoneTest extends TestCase
{
    public function test_el_enlace_de_google_calendar_usa_la_zona_horaria_configurada(): void
    {
        config(['app.timezone' => 'America/Bogota']);

        $cita = new Cita([
            'modalidad' => 'online',
            'fecha_inicio' => Carbon::create(2026, 10, 15, 9, 0, 0, 'America/Bogota'),
            'fecha_fin' => Carbon::create(2026, 10, 15, 9, 50, 0, 'America/Bogota'),
        ]);

        $servicio = new ReservaService(new CitaService(), new PacienteService());
        $enlace = $servicio->googleCalendarLink($cita, 'Ana Ejemplo');

        $this->assertStringContainsString('ctz=America%2FBogota', $enlace);
        $this->assertStringNotContainsString('Madrid', $enlace);
    }
}
