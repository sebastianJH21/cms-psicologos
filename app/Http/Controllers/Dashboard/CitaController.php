<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\StoreCitaRequest;
use App\Http\Requests\Dashboard\UpdateCitaRequest;
use App\Models\Cita;
use App\Services\CitaService;
use App\Services\PacienteService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CitaController extends Controller
{
    public function __construct(
        private CitaService $citaService,
        private PacienteService $pacienteService,
    ) {
    }

    public function index(Request $request)
    {
        $periodo = $request->input('periodo', 'proximas');
        if (!in_array($periodo, ['proximas', 'pasadas'], true)) {
            $periodo = 'proximas';
        }

        $query = Cita::query();

        if ($periodo === 'pasadas') {
            $query->where('fecha_inicio', '<', now())->orderBy('fecha_inicio', 'desc');
        } else {
            $query->where('fecha_inicio', '>=', now())->orderBy('fecha_inicio', 'asc');
        }

        if ($modalidad = $request->input('modalidad')) {
            $query->where('modalidad', $modalidad);
        }

        if ($estado = $request->input('estado')) {
            $query->where('estado', $estado);
        }

        if ($desde = $request->input('desde')) {
            try {
                $query->where('fecha_inicio', '>=', Carbon::parse($desde)->startOfDay());
            } catch (\Throwable) {
            }
        }

        if ($hasta = $request->input('hasta')) {
            try {
                $query->where('fecha_inicio', '<=', Carbon::parse($hasta)->endOfDay());
            } catch (\Throwable) {
            }
        }

        if ($buscar = trim((string) $request->input('q'))) {
            $query->where(function ($q) use ($buscar) {
                $q->where('nombre_provisional', 'like', "%{$buscar}%")
                    ->orWhere('telefono_provisional', 'like', "%{$buscar}%");
            });
        }

        $citas = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('dashboard.citas.partials.tabla', compact('citas'))->render(),
            ]);
        }

        return view('dashboard.citas.index', [
            'citas' => $citas,
            'filtros' => [
                'modalidad' => $request->input('modalidad', ''),
                'estado' => $request->input('estado', ''),
                'desde' => $request->input('desde', ''),
                'hasta' => $request->input('hasta', ''),
                'q' => $request->input('q', ''),
                'periodo' => $periodo,
            ],
            'modalidades' => Cita::MODALIDADES,
            'estados' => Cita::ESTADOS,
        ]);
    }

    public function create(Request $request)
    {
        return view('dashboard.citas.create', [
            'modalidades' => Cita::MODALIDADES,
            'estados' => Cita::ESTADOS,
            'duracionPresencial' => $this->citaService->duracionPresencial(),
            'duracionOnline' => $this->citaService->duracionOnline(),
            'descansoPresencial' => $this->citaService->descansoActivo('presencial') ? $this->citaService->descansoMin('presencial') : 0,
            'descansoOnline' => $this->citaService->descansoActivo('online') ? $this->citaService->descansoMin('online') : 0,
            'fechaPredeterminada' => $request->input('fecha', now()->addHour()->format('Y-m-d\TH:00')),
        ]);
    }

    public function store(StoreCitaRequest $request)
    {
        $datos = $request->validated();
        $datos['origen'] = 'manual';
        $datos['estado'] = $datos['estado'] ?? 'confirmada';

        $pacienteId = $datos['paciente_id'] ?? null;
        unset($datos['paciente_id']);

        $cita = $this->citaService->crear($datos);

        if ($pacienteId) {
            $cita->update(['paciente_id' => $pacienteId]);
        } elseif (!empty($cita->telefono_provisional)) {
            $paciente = $this->pacienteService->findOrCreateByPhone(
                $cita->telefono_provisional,
                [
                    'nombre' => $datos['nombre_provisional'] ?? 'Sin nombre',
                    'email' => $datos['email_provisional'] ?? null,
                    'origen' => 'manual',
                ]
            );
            $cita->update(['paciente_id' => $paciente->id]);
        }

        return redirect()
            ->route('dashboard.citas.show', $cita)
            ->with('success', 'Cita creada correctamente.');
    }

    public function show(Cita $cita)
    {
        return view('dashboard.citas.show', [
            'cita' => $cita,
        ]);
    }

    public function edit(Cita $cita)
    {
        return view('dashboard.citas.edit', [
            'cita' => $cita,
            'modalidades' => Cita::MODALIDADES,
            'estados' => Cita::ESTADOS,
            'duracionPresencial' => $this->citaService->duracionPresencial(),
            'duracionOnline' => $this->citaService->duracionOnline(),
            'descansoPresencial' => $this->citaService->descansoActivo('presencial') ? $this->citaService->descansoMin('presencial') : 0,
            'descansoOnline' => $this->citaService->descansoActivo('online') ? $this->citaService->descansoMin('online') : 0,
        ]);
    }

    public function update(UpdateCitaRequest $request, Cita $cita)
    {
        $datos = $request->validated();
        $this->citaService->actualizar($cita, $datos);

        if ($datos['estado'] === 'cancelada' && !$cita->trashed()) {
            $cita->delete();
        }

        return redirect()
            ->route('dashboard.citas.show', $cita)
            ->with('success', 'Cita actualizada correctamente.');
    }

    public function cambiarEstado(Request $request, Cita $cita)
    {
        $validated = $request->validate([
            'estado' => ['required', Rule::in(['pendiente', 'confirmada', 'cancelada'])],
        ]);

        $cita->update(['estado' => $validated['estado']]);

        $clase = match ($cita->estado) {
            'confirmada' => 'success',
            'realizada'  => 'info',
            'cancelada'  => 'danger',
            'no_asistio' => 'warning',
            default      => 'info',
        };

        return response()->json([
            'ok' => true,
            'estado' => $cita->estado,
            'estado_label' => $cita->estado_label,
            'badge' => $clase,
        ]);
    }

    public function destroy(Cita $cita)
    {
        $this->citaService->cancelar($cita);

        return redirect()
            ->route('dashboard.citas.index')
            ->with('success', 'La cita se ha cancelado.');
    }
}
