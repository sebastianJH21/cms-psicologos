<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\Disponibilidad;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class CitaService
{
    public function duracionSesion(): int
    {
        return (int) Setting::get('disponibilidad.duracion_sesion_min', 60);
    }

    public function duracionPresencial(): int
    {
        return (int) Setting::get(
            'disponibilidad.duracion_sesion_presencial_min',
            Setting::get('disponibilidad.duracion_sesion_min', 60)
        );
    }

    public function duracionOnline(): int
    {
        return (int) Setting::get(
            'disponibilidad.duracion_sesion_online_min',
            Setting::get('disponibilidad.duracion_sesion_min', 60)
        );
    }

    public function descansoMin(string $modalidad = 'presencial'): int
    {
        return max(0, (int) Setting::get("disponibilidad.descanso_min_{$modalidad}", 0));
    }

    public function descansoActivo(string $modalidad = 'presencial'): bool
    {
        return (bool) Setting::get("disponibilidad.descanso_activo_{$modalidad}", false);
    }

    public function modoVacaciones(): bool
    {
        return (bool) Setting::get('disponibilidad.modo_vacaciones', false);
    }

    public function calcularFechaFin(Carbon $inicio): Carbon
    {
        return $inicio->copy()->addMinutes($this->duracionSesion());
    }

    public function existeSolapamiento(Carbon $inicio, Carbon $fin, ?int $ignoreId = null): bool
    {
        $query = Cita::query()
            ->where('estado', '!=', 'cancelada')
            ->where('fecha_inicio', '<', $fin)
            ->where('fecha_fin', '>', $inicio);

        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        return $query->exists();
    }

    public function crear(array $datos): Cita
    {
        return DB::transaction(function () use ($datos) {
            $cita = new Cita($datos);
            $cita->save();
            return $cita;
        });
    }

    public function actualizar(Cita $cita, array $datos): Cita
    {
        return DB::transaction(function () use ($cita, $datos) {
            $cita->fill($datos)->save();
            return $cita;
        });
    }

    public function cancelar(Cita $cita): Cita
    {
        $cita->estado = 'cancelada';
        $cita->save();
        $cita->delete();
        return $cita;
    }

    public function horaAperturaManana(): string
    {
        return substr(Setting::get('disponibilidad.hora_apertura_manana',
            Setting::get('disponibilidad.hora_apertura', '09:00')), 0, 5);
    }

    public function horaCierreManana(): string
    {
        return substr(Setting::get('disponibilidad.hora_cierre_manana', '14:00'), 0, 5);
    }

    public function horaAperturaTarde(): string
    {
        return substr(Setting::get('disponibilidad.hora_apertura_tarde', '15:00'), 0, 5);
    }

    public function horaCierreTarde(): string
    {
        return substr(Setting::get('disponibilidad.hora_cierre_tarde',
            Setting::get('disponibilidad.hora_cierre', '20:00')), 0, 5);
    }

    public function disponibilidadesAgrupadas(): array
    {
        $duracionPresencial = $this->duracionPresencial();
        $duracionOnline     = $this->duracionOnline();
        $aperturaManana     = $this->horaAperturaManana();
        $cierreManana       = $this->horaCierreManana();
        $aperturaTarde      = $this->horaAperturaTarde();
        $cierreTarde        = $this->horaCierreTarde();

        $descansoPresencial = $this->descansoActivo('presencial') ? $this->descansoMin('presencial') : 0;
        $descansoOnline     = $this->descansoActivo('online') ? $this->descansoMin('online') : 0;

        $existentes = Disponibilidad::query()
            ->where('activa', true)
            ->get()
            ->groupBy(fn($d) => $d->modalidad . '|' . $d->dia_semana . '|' . substr($d->hora_inicio, 0, 5));

        return [
            'duracion_presencial'       => $duracionPresencial,
            'duracion_online'           => $duracionOnline,
            'hora_apertura_manana'      => $aperturaManana,
            'hora_cierre_manana'        => $cierreManana,
            'hora_apertura_tarde'       => $aperturaTarde,
            'hora_cierre_tarde'         => $cierreTarde,
            'slots_presencial_manana'   => $this->generarSlots($aperturaManana, $cierreManana, $duracionPresencial, $descansoPresencial),
            'slots_online_manana'       => $this->generarSlots($aperturaManana, $cierreManana, $duracionOnline, $descansoOnline),
            'slots_presencial_tarde'    => $this->generarSlots($aperturaTarde, $cierreTarde, $duracionPresencial, $descansoPresencial),
            'slots_online_tarde'        => $this->generarSlots($aperturaTarde, $cierreTarde, $duracionOnline, $descansoOnline),
            'dias'                      => Disponibilidad::DIAS,
            'orden_dias'                => Disponibilidad::ORDEN_DIAS,
            'seleccion'                 => $existentes->keys()->all(),
        ];
    }

    public function generarSlots(string $apertura, string $cierre, int $duracionMin, int $descansoMin = 0): array
    {
        $slots = [];
        $inicio = Carbon::createFromFormat('H:i', substr($apertura, 0, 5));
        $fin = Carbon::createFromFormat('H:i', substr($cierre, 0, 5));
        $paso = $duracionMin + max(0, $descansoMin);

        if ($duracionMin <= 0) {
            return $slots;
        }

        while ($inicio->copy()->addMinutes($duracionMin)->lte($fin)) {
            $slots[] = [
                'inicio' => $inicio->format('H:i'),
                'fin' => $inicio->copy()->addMinutes($duracionMin)->format('H:i'),
            ];
            $inicio->addMinutes($paso);
        }

        return $slots;
    }
}
