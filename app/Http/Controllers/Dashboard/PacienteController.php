<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\PacienteRequest;
use App\Models\Paciente;
use App\Services\PacienteService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PacienteController extends Controller
{
    public function __construct(private PacienteService $pacienteService)
    {
    }

    public function index(Request $request)
    {
        $papelera = $request->input('papelera') === '1';

        $query = Paciente::query()
            ->withCount(['citas as citas_total' => fn ($q) => $q->whereNull('citas.deleted_at')]);

        if ($termino = $request->input('q')) {
            $query->buscar($termino);
        }

        if ($origen = $request->input('origen')) {
            $query->where('origen', $origen);
        }

        if ($papelera) {
            $query->onlyTrashed();
        }

        $pacientes = $query->orderBy('nombre')->orderBy('apellidos')->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('dashboard.pacientes.partials.tabla', compact('pacientes', 'papelera'))->render(),
            ]);
        }

        return view('dashboard.pacientes.index', [
            'pacientes' => $pacientes,
            'papelera' => $papelera,
            'filtros' => [
                'q' => $request->input('q', ''),
                'origen' => $request->input('origen', ''),
                'papelera' => $request->input('papelera', ''),
            ],
        ]);
    }

    public function buscar(Request $request): JsonResponse
    {
        $q = trim((string) $request->input('q', ''));

        $pacientes = Paciente::query()
            ->buscar($q)
            ->limit(10)
            ->get(['id', 'nombre', 'apellidos', 'telefono', 'email']);

        return response()->json(
            $pacientes->map(fn ($p) => [
                'id' => $p->id,
                'nombre' => $p->nombre_completo,
                'telefono' => $p->telefono,
                'email' => $p->email,
            ])
        );
    }

    public function create()
    {
        return view('dashboard.pacientes.create', [
            'generos' => Paciente::GENEROS,
        ]);
    }

    public function store(PacienteRequest $request)
    {
        $paciente = $this->pacienteService->crear($request->validated());

        return redirect()
            ->route('dashboard.pacientes.show', $paciente)
            ->with('success', 'Paciente creado correctamente.');
    }

    public function show(Paciente $paciente)
    {
        $paciente->load(['citas' => fn ($q) => $q->limit(20)]);

        $historiasRecientes = $paciente->historias()
            ->withCount('archivos')
            ->orderBy('fecha_sesion', 'desc')
            ->take(3)
            ->get();

        return view('dashboard.pacientes.show', [
            'paciente' => $paciente,
            'modalidades' => \App\Models\Cita::MODALIDADES,
            'estados' => \App\Models\Cita::ESTADOS,
            'historiasRecientes' => $historiasRecientes,
            'totalHistorias' => $paciente->historias()->count(),
            'totalCitas' => $paciente->citas()->count()
        ]);
    }

    public function edit(Paciente $paciente)
    {
        return view('dashboard.pacientes.edit', [
            'paciente' => $paciente,
            'generos' => Paciente::GENEROS,
        ]);
    }

    public function update(PacienteRequest $request, Paciente $paciente)
    {
        $this->pacienteService->actualizar($paciente, $request->validated());

        return redirect()
            ->route('dashboard.pacientes.show', $paciente)
            ->with('success', 'Paciente actualizado correctamente.');
    }

    public function destroy(Paciente $paciente)
    {
        $this->pacienteService->eliminar($paciente);

        return redirect()
            ->route('dashboard.pacientes.index')
            ->with('success', 'Paciente movido a la papelera.');
    }

    public function restore(int $id)
    {
        $paciente = Paciente::onlyTrashed()->findOrFail($id);
        $this->pacienteService->restaurar($paciente);

        return redirect()
            ->route('dashboard.pacientes.show', $paciente)
            ->with('success', 'Paciente restaurado.');
    }

    public function citas(Paciente $paciente)
    {
        $citas = $paciente->citas()
            ->withTrashed()
            ->orderBy('fecha_inicio', 'desc')
            ->paginate(20);

        return view('dashboard.pacientes.citas', [
            'paciente' => $paciente,
            'citas' => $citas,
        ]);
    }
}
