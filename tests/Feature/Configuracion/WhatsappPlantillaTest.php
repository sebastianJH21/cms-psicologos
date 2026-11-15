<?php

namespace Tests\Feature\Configuracion;

use App\Models\Cita;
use App\Models\User;
use App\Services\WhatsappService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WhatsappPlantillaTest extends TestCase
{
    use RefreshDatabase;

    public function test_la_pagina_de_edicion_muestra_la_plantilla_por_defecto(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard.configuracion.whatsapp.edit'));

        $response->assertOk();
        $response->assertSee(WhatsappService::PLANTILLA_POR_DEFECTO, false);
    }

    public function test_la_pagina_muestra_el_selector_de_emojis_y_el_enlace_externo(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get(route('dashboard.configuracion.whatsapp.edit'));

        $response->assertOk();
        foreach (WhatsappService::EMOJIS_SUGERIDOS as $emoji) {
            $response->assertSee($emoji, false);
        }
        $response->assertSee(WhatsappService::ENLACE_MAS_EMOJIS, false);
    }

    public function test_guarda_una_plantilla_personalizada_con_emoji(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('dashboard.configuracion.whatsapp.update'), [
            'plantilla_confirmacion' => 'Hola {nombre} 😊, confirmamos tu cita {modalidad} del {fecha} a las {hora}.',
            'plantilla_recordatorio_pago' => 'Hola {nombre} 😊, recuerda tu pago de {monto}.',
        ]);

        $response->assertRedirect(route('dashboard.configuracion.whatsapp.edit'));
        $response->assertSessionHas('success');
        $this->assertDatabaseHas('settings', ['key' => 'whatsapp.plantilla_confirmacion']);
        $this->assertDatabaseHas('settings', ['key' => 'whatsapp.plantilla_recordatorio_pago']);
    }

    public function test_rechaza_una_plantilla_vacia(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('dashboard.configuracion.whatsapp.update'), [
            'plantilla_confirmacion' => '',
            'plantilla_recordatorio_pago' => 'Hola {nombre}, recuerda tu pago de {monto}.',
        ]);

        $response->assertSessionHasErrors('plantilla_confirmacion');
    }

    public function test_rechaza_una_plantilla_de_pago_vacia(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->put(route('dashboard.configuracion.whatsapp.update'), [
            'plantilla_confirmacion' => 'Hola {nombre}, confirmamos tu cita.',
            'plantilla_recordatorio_pago' => '',
        ]);

        $response->assertSessionHasErrors('plantilla_recordatorio_pago');
    }

    public function test_el_enlace_de_whatsapp_de_una_cita_usa_la_plantilla_guardada(): void
    {
        $user = User::factory()->create(['nombre' => 'Ana', 'apellidos' => 'Psicóloga']);
        $this->actingAs($user)->put(route('dashboard.configuracion.whatsapp.update'), [
            'plantilla_confirmacion' => 'Hola {nombre} 👋, soy {psicologa}. Nos vemos el {fecha} a las {hora} ({modalidad}).',
            'plantilla_recordatorio_pago' => 'Hola {nombre}, recuerda tu pago de {monto}.',
        ]);

        $cita = Cita::create([
            'nombre_provisional' => 'Laura Gómez',
            'telefono_provisional' => '600112233',
            'modalidad' => 'online',
            'fecha_inicio' => Carbon::create(2026, 10, 15, 9, 0),
            'fecha_fin' => Carbon::create(2026, 10, 15, 9, 50),
            'estado' => 'pendiente',
            'origen' => 'manual',
        ]);

        $url = whatsapp_confirmacion_url($cita);

        $this->assertNotNull($url);
        $this->assertStringStartsWith('https://wa.me/600112233?text=', $url);

        $mensajeDecodificado = urldecode(explode('?text=', $url)[1]);
        $this->assertSame('Hola Laura 👋, soy Ana Psicóloga. Nos vemos el 15/10/2026 a las 09:00 (Online).', $mensajeDecodificado);
    }

    public function test_el_enlace_de_recordatorio_de_pago_usa_la_plantilla_guardada_y_el_monto(): void
    {
        $user = User::factory()->create(['nombre' => 'Ana', 'apellidos' => 'Psicóloga']);
        $this->actingAs($user)->put(route('dashboard.configuracion.whatsapp.update'), [
            'plantilla_confirmacion' => 'Hola {nombre}, confirmamos tu cita.',
            'plantilla_recordatorio_pago' => 'Hola {nombre}, tu cita {modalidad} del {fecha} tiene un pago pendiente de {monto}.',
        ]);

        $cita = Cita::create([
            'nombre_provisional' => 'Laura Gómez',
            'telefono_provisional' => '600112233',
            'modalidad' => 'online',
            'fecha_inicio' => Carbon::create(2026, 10, 15, 9, 0),
            'fecha_fin' => Carbon::create(2026, 10, 15, 9, 50),
            'estado' => 'confirmada',
            'pagado' => false,
            'monto' => 150000,
            'origen' => 'manual',
        ]);

        $url = app(\App\Services\WhatsappService::class)->urlRecordatorioPago($cita);

        $this->assertNotNull($url);
        $mensajeDecodificado = urldecode(explode('?text=', $url)[1]);
        $this->assertSame('Hola Laura, tu cita Online del 15/10/2026 tiene un pago pendiente de $150.000.', $mensajeDecodificado);
    }

    public function test_sin_telefono_no_genera_enlace(): void
    {
        $cita = new Cita([
            'nombre_provisional' => 'Sin Teléfono',
            'telefono_provisional' => '',
            'modalidad' => 'online',
            'fecha_inicio' => Carbon::now(),
            'fecha_fin' => Carbon::now()->addMinutes(50),
        ]);

        $this->assertNull(whatsapp_confirmacion_url($cita));
    }
}
