<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Cita;
use App\Models\Evento;
use App\Rules\NoOverlap;
use App\Services\CitaService;
use App\Services\PacienteService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CalendarioController extends Controller
{
    public function __construct(
        private CitaService $citaService,
        private PacienteService $pacienteService,
    ) {
    }

    public function index(): \Illuminate\View\View
    {
        $user = \App\Models\User::first();

        return view('dashboard.calendario.index', [
            'modalidades' => Cita::MODALIDADES,
            'estados' => Cita::ESTADOS,
            'duracionPresencial' => $this->citaService->duracionPresencial(),
            'duracionOnline' => $this->citaService->duracionOnline(),
            'psicologaNombre' => $user ? trim($user->nombre . ' ' . $user->apellidos) : 'tu psicóloga',
        ]);
    }

    public function eventos(Request $request): JsonResponse
    {
        $query = Cita::query()->where('estado', '!=', 'cancelada');

        if ($desde = $request->input('start')) {
            try {
                $query->where('fecha_inicio', '>=', Carbon::parse($desde)->startOfDay());
            } catch (\Throwable) {
            }
        }

        if ($hasta = $request->input('end')) {
            try {
                $query->where('fecha_inicio', '<=', Carbon::parse($hasta)->endOfDay());
            } catch (\Throwable) {
            }
        }

        $eventos = $query->orderBy('fecha_inicio')
            ->get()
            ->map(fn ($cita) => $this->citaToEvento($cita))
            ->values()
            ->all();

        $extras = collect();
        if (\Illuminate\Support\Facades\Schema::hasTable('eventos')) {
            $extraQuery = Evento::query();
            if ($desde = $request->input('start')) {
                try { $extraQuery->where('fecha_inicio', '>=', Carbon::parse($desde)->startOfDay()); } catch (\Throwable) {}
            }
            if ($hasta = $request->input('end')) {
                try { $extraQuery->where('fecha_inicio', '<=', Carbon::parse($hasta)->endOfDay()); } catch (\Throwable) {}
            }
            $extras = $extraQuery->orderBy('fecha_inicio')->get();
        }

        $extras = $extras->map(function (Evento $e) {
            return [
                'guid'        => 'evt-' . $e->id,
                'tipo'        => 'evento',
                'evento_id'   => $e->id,
                'title'       => $e->titulo,
                'description' => $e->descripcion ?? '',
                'start'       => $e->fecha_inicio->format('H:i'),
                'end'         => $e->fecha_fin->format('H:i'),
                'date'        => $e->fecha_inicio->format('Y-m-d'),
                'color'       => $e->color ?: '#9b59b6',
                'location'    => $e->ubicacion ?? '',
                'readonly'    => true,
                'estado'      => 'Evento',
                'modalidad'   => 'Evento extra',
                'motivo'      => $e->descripcion ?? '',
                'telefono'    => '',
                'estado_key'  => null,
                'url'         => null,
            ];
        })->all();

        return response()->json(array_values(array_merge($eventos, $extras)));
    }

    public function crear(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'paciente_id' => ['nullable', 'integer', 'exists:pacientes,id'],
            'nombre_provisional' => ['required', 'string', 'max:150'],
            'telefono_provisional' => ['required', 'string', 'max:30'],
            'modalidad' => ['required', Rule::in(array_keys(Cita::MODALIDADES))],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after:fecha_inicio', new NoOverlap()],
            'motivo' => ['nullable', 'string', 'max:1000'],
            'estado' => ['nullable', Rule::in(array_keys(Cita::ESTADOS))],
            'notas_internas' => ['nullable', 'string', 'max:2000'],
        ]);

        $pacienteId = $validated['paciente_id'] ?? null;
        unset($validated['paciente_id']);

        $validated['origen'] = 'manual';
        $validated['estado'] = $validated['estado'] ?? 'confirmada';

        $cita = $this->citaService->crear($validated);

        // Vincular o crear paciente automáticamente
        if ($pacienteId) {
            $cita->update(['paciente_id' => $pacienteId]);
        } elseif (!empty($cita->telefono_provisional)) {
            $paciente = $this->pacienteService->findOrCreateByPhone(
                $cita->telefono_provisional,
                [
                    'nombre' => $validated['nombre_provisional'] ?? 'Sin nombre',
                    'origen' => 'manual',
                ]
            );
            $cita->update(['paciente_id' => $paciente->id]);
        }

        return response()->json([
            'ok' => true,
            'evento' => $this->citaToEvento($cita->fresh()),
            'url' => route('dashboard.citas.show', $cita),
        ], 201);
    }

    public function actualizar(Request $request, Cita $cita): JsonResponse
    {
        $validated = $request->validate([
            'fecha_inicio' => ['sometimes', 'required', 'date'],
            'fecha_fin' => ['sometimes', 'required', 'date', 'after:fecha_inicio', new NoOverlap($cita->id)],
            'estado' => ['sometimes', 'required', Rule::in(array_keys(Cita::ESTADOS))],
        ]);

        $this->citaService->actualizar($cita, $validated);

        return response()->json([
            'ok' => true,
            'evento' => $this->citaToEvento($cita->fresh()),
        ]);
    }

    private function citaToEvento(Cita $cita): array
    {
        $colores = [
            'presencial_confirmada' => '#c98b1e',
            'presencial_pendiente'  => '#d4a855',
            'presencial_realizada'  => '#2f8f5f',
            'presencial_no_asistio' => '#b03a2e',
            'online_confirmada'     => '#2f7ea1',
            'online_pendiente'      => '#5fa8c7',
            'online_realizada'      => '#2f8f5f',
            'online_no_asistio'     => '#b03a2e',
        ];

        $colorKey = $cita->modalidad . '_' . $cita->estado;

        return [
            'guid'        => (string) $cita->id,
            'title'       => $cita->nombre_provisional,
            'description' => $cita->modalidad_label . ($cita->motivo ? ' · ' . $cita->motivo : ''),
            'start'       => $cita->fecha_inicio->format('H:i'),
            'end'         => $cita->fecha_fin->format('H:i'),
            'date'        => $cita->fecha_inicio->format('Y-m-d'),
            'color'       => $colores[$colorKey] ?? '#6b7180',
            'location'    => $cita->modalidad === 'presencial' ? 'Consulta presencial' : 'Sesión online',
            'readonly'    => true,
            // Extra data for detail modal
            'telefono'    => $cita->telefono_provisional,
            'motivo'      => $cita->motivo ?? '',
            'estado'      => $cita->estado_label,
            'estado_key'  => $cita->estado,
            'modalidad'   => $cita->modalidad_label,
            'url'         => route('dashboard.citas.show', $cita),
        ];
    }
}
