<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\Disponibilidad;
use App\Models\Paciente;
use App\Models\PeriodoVacaciones;
use App\Models\Setting;
use App\Support\PhoneHelper;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReservaService
{
    public function __construct(
        private CitaService $citaService,
        private PacienteService $pacienteService,
    ) {}

    public function modoVacaciones(): bool
    {
        return (bool) Setting::get('disponibilidad.modo_vacaciones', false);
    }

    private function periodosVacacionesCargados(): \Illuminate\Database\Eloquent\Collection
    {
        static $periodos = null;
        if ($periodos === null) {
            $periodos = PeriodoVacaciones::all();
        }
        return $periodos;
    }

    private function enPeriodoVacaciones(Carbon $dia): bool
    {
        $fecha = $dia->toDateString();
        foreach ($this->periodosVacacionesCargados() as $periodo) {
            if ($periodo->fecha_inicio->toDateString() <= $fecha && $periodo->fecha_fin->toDateString() >= $fecha) {
                return true;
            }
        }
        return false;
    }

    public function slotsDisponibles(string $modalidad, string $fecha): array
    {
        if ($this->modoVacaciones()) {
            return [];
        }

        try {
            $dia = Carbon::createFromFormat('Y-m-d', $fecha)->startOfDay();
        } catch (\Exception) {
            return [];
        }

        if ($dia->isPast() && !$dia->isToday()) {
            return [];
        }

        if ($this->enPeriodoVacaciones($dia)) {
            return [];
        }

        $diaSemana = (int) $dia->dayOfWeek;

        $disponibilidades = Disponibilidad::query()
            ->where('modalidad', $modalidad)
            ->where('dia_semana', $diaSemana)
            ->where('activa', true)
            ->orderBy('hora_inicio')
            ->get();

        if ($disponibilidades->isEmpty()) {
            return [];
        }

        $duracion = $modalidad === 'online'
            ? $this->citaService->duracionOnline()
            : $this->citaService->duracionPresencial();
        $ahora = Carbon::now();

        // Cargamos las citas que solapan el día (no solo las que empiezan en él),
        // para contemplar también citas que se extienden desde días anteriores.
        $diaInicio = $dia->copy()->startOfDay();
        $diaFin = $dia->copy()->endOfDay();
        $citasOcupadas = Cita::query()
            ->where('estado', '!=', 'cancelada')
            ->where('fecha_inicio', '<', $diaFin)
            ->where('fecha_fin', '>', $diaInicio)
            ->get(['fecha_inicio', 'fecha_fin']);

        $slots = [];
        foreach ($disponibilidades as $disp) {
            $inicioStr = substr($disp->hora_inicio, 0, 5);
            $finStr = substr($disp->hora_fin, 0, 5);
            $inicio = $dia->copy()->setTimeFromTimeString($inicioStr);
            $fin = $dia->copy()->setTimeFromTimeString($finStr);

            if ($inicio->copy()->addMinutes($duracion)->gt($fin)) {
                continue;
            }
            if ($inicio->lt($ahora)) {
                continue;
            }

            $finSlot = $inicio->copy()->addMinutes($duracion);
            $libre = true;
            foreach ($citasOcupadas as $cita) {
                if ($cita->fecha_inicio < $finSlot && $cita->fecha_fin > $inicio) {
                    $libre = false;
                    break;
                }
            }

            if ($libre) {
                $slots[] = [
                    'hora' => $inicioStr,
                    'inicio_iso' => $inicio->toIso8601String(),
                    'fin_iso' => $finSlot->toIso8601String(),
                ];
            }
        }

        return $slots;
    }

    public function diasDisponibles(string $modalidad, int $diasAdelante = 0): array
    {
        if ($diasAdelante === 0) {
            $diasAdelante = (int) Setting::get('disponibilidad.dias_adelante', 60);
        }

        $diasSemana = Disponibilidad::query()
            ->where('modalidad', $modalidad)
            ->where('activa', true)
            ->distinct()
            ->pluck('dia_semana')
            ->all();

        if (empty($diasSemana)) {
            return [];
        }

        $resultado = [];
        $hoy = Carbon::today();
        for ($i = 0; $i < $diasAdelante; $i++) {
            $dia = $hoy->copy()->addDays($i);
            if (!in_array((int) $dia->dayOfWeek, $diasSemana, true)) {
                continue;
            }
            if ($this->enPeriodoVacaciones($dia)) {
                continue;
            }
            $resultado[] = $dia->toDateString();
        }

        return $resultado;
    }

    public function registrar(array $datos): Cita
    {
        return DB::transaction(function () use ($datos) {
            $telefono = PhoneHelper::normalize($datos['telefono'] ?? null);
            if ($telefono === null) {
                throw new \RuntimeException('El teléfono indicado no es válido. Introduce solo números.');
            }
            $modalidadReserva = $datos['modalidad'] ?? 'presencial';
            $duracion = $modalidadReserva === 'online'
                ? $this->citaService->duracionOnline()
                : $this->citaService->duracionPresencial();
            $inicio = Carbon::parse($datos['fecha_hora']);
            $fin = $inicio->copy()->addMinutes($duracion);

            if ($this->citaService->existeSolapamiento($inicio, $fin)) {
                throw new \RuntimeException('El horario ya no está disponible. Elige otro.');
            }

            $paciente = $this->pacienteService->findOrCreateByPhone($telefono, [
                'nombre' => $datos['nombre'],
                'motivo_inicial' => $datos['motivo'] ?? null,
                'origen' => 'publica',
            ]);

            $yaTieneCitaEseDia = Cita::query()
                ->where('paciente_id', $paciente->id)
                ->where('estado', '!=', 'cancelada')
                ->whereDate('fecha_inicio', $inicio->toDateString())
                ->exists();

            if ($yaTieneCitaEseDia) {
                throw new \RuntimeException('Ya tienes una cita reservada para ese día. Si necesitas otra, contáctame directamente.');
            }

            $cita = Cita::create([
                'paciente_id' => $paciente->id,
                'nombre_provisional' => $paciente->nombre,
                'telefono_provisional' => $paciente->telefono,
                'modalidad' => $datos['modalidad'],
                'fecha_inicio' => $inicio,
                'fecha_fin' => $fin,
                'motivo' => $datos['motivo'] ?? null,
                'estado' => 'pendiente',
                'origen' => 'publica',
            ]);

            return $cita;
        });
    }

    public function googleCalendarLink(Cita $cita, string $psicologa, ?string $direccion = null): string
    {
        // Aseguramos que las fechas estén en la zona horaria de Madrid
        $inicio = $cita->fecha_inicio
            ->copy()
            ->setTimezone('Europe/Madrid')
            ->format('Ymd\THis');

        $fin = $cita->fecha_fin
            ->copy()
            ->setTimezone('Europe/Madrid')
            ->format('Ymd\THis');

        $titulo = 'Cita con ' . $psicologa;
        $detalles = $cita->motivo ?: 'Sesión de psicología';

        $ubicacion = $cita->modalidad === 'online'
            ? 'Online'
            : ($direccion ?: 'Consulta presencial');

        $params = http_build_query([
            'action'   => 'TEMPLATE',
            'text'     => $titulo,
            'dates'    => "{$inicio}/{$fin}",
            'ctz'      => 'Europe/Madrid',
            'details'  => $detalles,
            'location' => $ubicacion,
        ]);

        return 'https://calendar.google.com/calendar/render?' . $params;
    }
}
