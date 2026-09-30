<?php

namespace Tests\Unit\Services;

use App\Services\CitaService;
use PHPUnit\Framework\TestCase;

class CitaServiceSlotsTest extends TestCase
{
    public function test_ejemplo_online_con_descanso_claude_md(): void
    {
        // CLAUDE.md: citas online de 50 min + 10 min de descanso -> 9:00, 10:00, 11:00...
        $slots = (new CitaService())->generarSlots('09:00', '14:00', 50, 10);

        $this->assertSame(
            ['09:00', '10:00', '11:00', '12:00', '13:00'],
            array_column($slots, 'inicio')
        );
    }

    public function test_ejemplo_presencial_sin_descanso_claude_md(): void
    {
        // CLAUDE.md: citas presenciales de 50 min sin descanso -> 9:00, 9:50, 10:40...
        $slots = (new CitaService())->generarSlots('09:00', '14:00', 50, 0);

        $this->assertSame(
            ['09:00', '09:50', '10:40', '11:30', '12:20', '13:10'],
            array_column($slots, 'inicio')
        );
    }

    public function test_no_genera_huecos_si_la_duracion_no_cabe(): void
    {
        $slots = (new CitaService())->generarSlots('09:00', '09:30', 50, 0);

        $this->assertSame([], $slots);
    }

    public function test_sin_duracion_configurada_no_genera_huecos(): void
    {
        $slots = (new CitaService())->generarSlots('09:00', '14:00', 0, 10);

        $this->assertSame([], $slots);
    }
}
