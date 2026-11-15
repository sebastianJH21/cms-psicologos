<?php

namespace Tests\Feature\Facturacion;

use App\Models\Cita;
use App\Models\Gasto;
use App\Models\IngresoManual;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FacturacionTest extends TestCase
{
    use RefreshDatabase;

    private function citaConfirmada(array $overrides = []): Cita
    {
        return Cita::create(array_merge([
            'nombre_provisional' => 'Paciente de prueba',
            'telefono_provisional' => '3001234567',
            'modalidad' => 'presencial',
            'fecha_inicio' => now()->addDay(),
            'fecha_fin' => now()->addDay()->addMinutes(50),
            'estado' => 'confirmada',
            'origen' => 'manual',
        ], $overrides));
    }

    public function test_el_dashboard_de_facturacion_carga_para_un_usuario_autenticado(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard.facturacion.index'));

        $response->assertOk();
        $response->assertSee('Facturación');
    }

    public function test_marcar_una_cita_pendiente_como_pagada_exige_estado_confirmada_o_realizada(): void
    {
        $user = User::factory()->create();
        $cita = $this->citaConfirmada(['estado' => 'pendiente']);

        $response = $this->actingAs($user)->patch(route('dashboard.citas.pago', $cita), [
            'pagado' => '1',
            'monto' => 100000,
        ]);

        $response->assertSessionHasErrors('monto');
        $this->assertFalse($cita->refresh()->pagado);
    }

    public function test_marcar_como_pagada_sin_importe_exige_el_importe(): void
    {
        $user = User::factory()->create();
        $cita = $this->citaConfirmada();

        $response = $this->actingAs($user)->patch(route('dashboard.citas.pago', $cita), [
            'pagado' => '1',
        ]);

        $response->assertSessionHasErrors('monto');
        $this->assertFalse($cita->refresh()->pagado);
    }

    public function test_marcar_una_cita_confirmada_como_pagada_registra_el_importe_y_la_fecha(): void
    {
        $user = User::factory()->create();
        $cita = $this->citaConfirmada();

        $response = $this->actingAs($user)->patch(route('dashboard.citas.pago', $cita), [
            'pagado' => '1',
            'monto' => 150000,
        ]);

        $response->assertRedirect(route('dashboard.citas.show', $cita));
        $cita->refresh();
        $this->assertTrue($cita->pagado);
        $this->assertEquals(150000, (float) $cita->monto);
        $this->assertNotNull($cita->pagado_at);
    }

    public function test_desmarcar_el_pago_conserva_el_importe_para_el_recordatorio(): void
    {
        $user = User::factory()->create();
        $cita = $this->citaConfirmada(['pagado' => true, 'monto' => 90000, 'pagado_at' => now()]);

        $this->actingAs($user)->patch(route('dashboard.citas.pago', $cita), [
            'pagado' => '0',
        ]);

        $cita->refresh();
        $this->assertFalse($cita->pagado);
        $this->assertNull($cita->pagado_at);
        $this->assertEquals(90000, (float) $cita->monto);
    }

    public function test_el_filtro_de_pendientes_de_pago_solo_muestra_confirmadas_o_realizadas_sin_pagar(): void
    {
        $user = User::factory()->create();
        $pendientePago = $this->citaConfirmada();
        $this->citaConfirmada(['pagado' => true, 'monto' => 50000, 'pagado_at' => now()]);
        $this->citaConfirmada(['estado' => 'pendiente']);

        $response = $this->actingAs($user)->get(route('dashboard.citas.index', ['pendiente_pago' => 1]));

        $response->assertOk();
        $response->assertSee($pendientePago->nombre_provisional);
    }

    public function test_sin_importe_no_se_genera_enlace_de_recordatorio_de_pago(): void
    {
        $cita = $this->citaConfirmada();

        $this->assertNull(whatsapp_recordatorio_pago_url($cita));
    }

    public function test_con_importe_se_genera_el_enlace_de_recordatorio_de_pago(): void
    {
        $cita = $this->citaConfirmada(['monto' => 120000]);

        $this->assertNotNull(whatsapp_recordatorio_pago_url($cita));
    }

    public function test_se_puede_registrar_un_gasto_y_aparece_en_el_listado(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('dashboard.facturacion.gastos.store'), [
            'descripcion' => 'Alquiler del consultorio',
            'monto' => 500000,
            'fecha' => now()->format('Y-m-d'),
            'categoria' => 'Alquiler',
        ]);

        $response->assertRedirect(route('dashboard.facturacion.index', ['tab' => 'gastos']));
        $this->assertDatabaseHas('gastos', ['descripcion' => 'Alquiler del consultorio']);
    }

    public function test_registrar_un_gasto_por_ajax_devuelve_json(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->postJson(route('dashboard.facturacion.gastos.store'), [
            'descripcion' => 'Alquiler del consultorio',
            'monto' => 500000,
            'fecha' => now()->format('Y-m-d'),
        ]);

        $response->assertOk();
        $response->assertJson(['success' => true]);
    }

    public function test_se_puede_eliminar_un_gasto(): void
    {
        $user = User::factory()->create();
        $gasto = Gasto::create(['descripcion' => 'Papelería', 'monto' => 20000, 'fecha' => now()]);

        $response = $this->actingAs($user)->delete(route('dashboard.facturacion.gastos.destroy', $gasto));

        $response->assertRedirect(route('dashboard.facturacion.index', ['tab' => 'gastos']));
        $this->assertDatabaseMissing('gastos', ['id' => $gasto->id]);
    }

    public function test_se_puede_eliminar_un_gasto_por_ajax(): void
    {
        $user = User::factory()->create();
        $gasto = Gasto::create(['descripcion' => 'Papelería', 'monto' => 20000, 'fecha' => now()]);

        $response = $this->actingAs($user)->deleteJson(route('dashboard.facturacion.gastos.destroy', $gasto));

        $response->assertOk();
        $response->assertJson(['success' => true]);
        $this->assertDatabaseMissing('gastos', ['id' => $gasto->id]);
    }

    public function test_se_puede_registrar_un_ingreso_manual_y_aparece_en_el_listado(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post(route('dashboard.facturacion.ingresos.store'), [
            'descripcion' => 'Taller particular',
            'monto' => 300000,
            'fecha' => now()->format('Y-m-d'),
        ]);

        $response->assertRedirect(route('dashboard.facturacion.index', ['tab' => 'ingresos']));
        $this->assertDatabaseHas('ingresos_manuales', ['descripcion' => 'Taller particular']);
    }

    public function test_el_resumen_del_mes_suma_citas_pagadas_e_ingresos_manuales_y_resta_gastos(): void
    {
        $this->citaConfirmada(['pagado' => true, 'monto' => 100000, 'pagado_at' => now()]);
        IngresoManual::create(['descripcion' => 'Extra', 'monto' => 50000, 'fecha' => now()]);
        Gasto::create(['descripcion' => 'Luz', 'monto' => 30000, 'fecha' => now()]);

        $resumen = app(\App\Services\FacturacionService::class)->resumenMes();

        $this->assertEquals(150000.0, $resumen['ingresos']);
        $this->assertEquals(30000.0, $resumen['gastos']);
        $this->assertEquals(120000.0, $resumen['neto']);
    }

    public function test_por_cobrar_suma_el_importe_de_las_citas_pendientes_de_pago(): void
    {
        $this->citaConfirmada(['monto' => 80000]);
        $this->citaConfirmada(['monto' => 40000]);
        $this->citaConfirmada(['pagado' => true, 'monto' => 100000, 'pagado_at' => now()]);

        $porCobrar = app(\App\Services\FacturacionService::class)->porCobrar();

        $this->assertEquals(120000.0, $porCobrar);
    }

    public function test_movimientos_del_mes_incluye_citas_pagadas_ingresos_y_gastos(): void
    {
        $this->citaConfirmada(['pagado' => true, 'monto' => 100000, 'pagado_at' => now()]);
        IngresoManual::create(['descripcion' => 'Extra', 'monto' => 50000, 'fecha' => now()]);
        Gasto::create(['descripcion' => 'Luz', 'monto' => 30000, 'fecha' => now(), 'categoria' => 'Servicios']);

        $movimientos = app(\App\Services\FacturacionService::class)->movimientosMes();

        $this->assertCount(3, $movimientos);
        $tipos = array_column($movimientos, 'tipo');
        $this->assertContains('cita', $tipos);
        $this->assertContains('manual', $tipos);
        $this->assertContains('gasto', $tipos);
    }

    public function test_la_pagina_de_facturacion_muestra_las_pestanas_y_el_mes_actual(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard.facturacion.index'));

        $response->assertOk();
        $response->assertSee('Ingresos del mes');
        $response->assertSee('Tendencia de ingresos');
        $response->assertSee('Pagos en línea');
    }

    public function test_se_puede_cambiar_de_mes_y_los_datos_de_ese_mes_se_muestran(): void
    {
        $user = User::factory()->create();

        $mesPasado = now()->copy()->subMonths(3)->startOfMonth();
        IngresoManual::create(['descripcion' => 'Taller de mayo', 'monto' => 400000, 'fecha' => $mesPasado->copy()->addDays(5)]);
        Gasto::create(['descripcion' => 'Arriendo de mayo', 'monto' => 300000, 'fecha' => $mesPasado->copy()->addDays(3), 'categoria' => 'Arriendo']);

        $response = $this->actingAs($user)->get(route('dashboard.facturacion.index', ['mes' => $mesPasado->format('Y-m'), 'tab' => 'ingresos']));

        $response->assertOk();
        $response->assertSee(ucfirst($mesPasado->translatedFormat('F Y')));
        $response->assertSee('Taller de mayo');
    }

    public function test_un_mes_invalido_no_rompe_la_pagina_y_usa_el_mes_actual(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard.facturacion.index', ['mes' => 'no-es-una-fecha']));

        $response->assertOk();
        $response->assertSee(ucfirst(now()->translatedFormat('F Y')));
    }
}
