<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\Setting;
use App\Models\User;

class WhatsappService
{
    public const PLANTILLA_POR_DEFECTO = 'Hola {nombre}, soy {psicologa}. Te escribo para confirmar tu cita {modalidad} del {fecha} a las {hora}. ¿Me confirmas que podrás asistir? ¡Gracias!';

    public const PLANTILLA_PAGO_POR_DEFECTO = 'Hola {nombre}, soy {psicologa}. Te escribo para recordarte el pago pendiente de tu cita {modalidad} del {fecha} a las {hora}, por un valor de {monto}. ¡Gracias!';

    public const VARIABLES = [
        '{nombre}' => 'Nombre del paciente (solo el primer nombre)',
        '{psicologa}' => 'Nombre completo de la psicóloga',
        '{modalidad}' => 'Online o Presencial',
        '{fecha}' => 'Fecha de la cita (dd/mm/aaaa)',
        '{hora}' => 'Hora de la cita (hh:mm)',
    ];

    public const VARIABLES_PAGO = [
        '{nombre}' => 'Nombre del paciente (solo el primer nombre)',
        '{psicologa}' => 'Nombre completo de la psicóloga',
        '{modalidad}' => 'Online o Presencial',
        '{fecha}' => 'Fecha de la cita (dd/mm/aaaa)',
        '{hora}' => 'Hora de la cita (hh:mm)',
        '{monto}' => 'Importe pendiente de pago (en COP)',
    ];

    public const EMOJIS_SUGERIDOS = [
        '😊', '🙂', '😄', '😁', '🤗', '👋', '🙏', '👍',
        '📅', '🗓️', '⏰', '✅', '📍', '📞', '💬', '💻',
        '❤️', '💚', '💙', '💛', '🧡', '💜', '✨', '🌿',
        '🌸', '🌼', '☀️', '🌙', '🎉', '🎊', '😉', '🥰',
    ];

    public const ENLACE_MAS_EMOJIS = 'https://es.piliapp.com/emoji/list/';

    public function plantilla(): string
    {
        $valor = Setting::get('whatsapp.plantilla_confirmacion');

        return is_string($valor) && trim($valor) !== '' ? $valor : self::PLANTILLA_POR_DEFECTO;
    }

    public function plantillaPago(): string
    {
        $valor = Setting::get('whatsapp.plantilla_recordatorio_pago');

        return is_string($valor) && trim($valor) !== '' ? $valor : self::PLANTILLA_PAGO_POR_DEFECTO;
    }

    public function renderizar(string $plantilla, array $datos): string
    {
        return strtr($plantilla, $datos);
    }

    private function datosCita(Cita $cita): array
    {
        $psicologa = User::first()?->nombre_completo ?: 'tu psicóloga';
        $nombre = trim((string) ($cita->paciente_nombre ?? ''));
        $primerNombre = $nombre !== '' ? explode(' ', $nombre)[0] : 'hola';

        return [
            '{nombre}' => $primerNombre,
            '{psicologa}' => $psicologa,
            '{modalidad}' => $cita->modalidad_label,
            '{fecha}' => $cita->fecha_inicio->format('d/m/Y'),
            '{hora}' => $cita->fecha_inicio->format('H:i'),
        ];
    }

    public function mensajeConfirmacion(Cita $cita): string
    {
        return $this->renderizar($this->plantilla(), $this->datosCita($cita));
    }

    public function mensajeRecordatorioPago(Cita $cita): string
    {
        $monto = '$' . number_format((float) $cita->monto, 0, ',', '.');

        return $this->renderizar($this->plantillaPago(), $this->datosCita($cita) + ['{monto}' => $monto]);
    }

    public function urlConfirmacion(Cita $cita): ?string
    {
        $telefono = preg_replace('/\D+/', '', (string) $cita->paciente_telefono);
        if ($telefono === '' || $telefono === null) {
            return null;
        }

        return 'https://wa.me/' . $telefono . '?text=' . rawurlencode($this->mensajeConfirmacion($cita));
    }

    public function urlRecordatorioPago(Cita $cita): ?string
    {
        if ($cita->monto === null) {
            return null;
        }

        $telefono = preg_replace('/\D+/', '', (string) $cita->paciente_telefono);
        if ($telefono === '' || $telefono === null) {
            return null;
        }

        return 'https://wa.me/' . $telefono . '?text=' . rawurlencode($this->mensajeRecordatorioPago($cita));
    }

    public function datosPrevisualizacion(): array
    {
        return [
            '{nombre}' => 'Laura',
            '{psicologa}' => User::first()?->nombre_completo ?: 'tu psicóloga',
            '{modalidad}' => 'Presencial',
            '{fecha}' => now()->addDay()->format('d/m/Y'),
            '{hora}' => '10:00',
        ];
    }

    public function datosPrevisualizacionPago(): array
    {
        return $this->datosPrevisualizacion() + ['{monto}' => '$120.000'];
    }

    public function previsualizar(string $plantilla): string
    {
        return $this->renderizar($plantilla, $this->datosPrevisualizacion());
    }

    public function previsualizarPago(string $plantilla): string
    {
        return $this->renderizar($plantilla, $this->datosPrevisualizacionPago());
    }
}
