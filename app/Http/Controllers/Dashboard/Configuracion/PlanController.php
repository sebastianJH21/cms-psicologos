<?php

namespace App\Http\Controllers\Dashboard\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Configuracion\PlanRequest;
use App\Models\PlanPrecio;

class PlanController extends Controller
{
    public function index()
    {
        $planesOnline     = PlanPrecio::where('tipo', 'online')->orderBy('orden')->orderBy('id')->get();
        $planesPresencial = PlanPrecio::where('tipo', 'presencial')->orderBy('orden')->orderBy('id')->get();

        return view('dashboard.configuracion.planes.index', compact('planesOnline', 'planesPresencial'));
    }

    public function create()
    {
        return view('dashboard.configuracion.planes.create');
    }

    public function store(PlanRequest $request)
    {
        $datos = $request->validated();
        $datos['orden'] = $datos['orden'] ?? ((int) (PlanPrecio::where('tipo', $datos['tipo'])->max('orden') ?? 0) + 1);

        PlanPrecio::create($datos);

        return redirect()
            ->route('dashboard.configuracion.planes.index')
            ->with('success', 'Plan creado correctamente.');
    }

    public function edit(PlanPrecio $plan)
    {
        return view('dashboard.configuracion.planes.edit', compact('plan'));
    }

    public function update(PlanRequest $request, PlanPrecio $plan)
    {
        $plan->update($request->validated());

        return redirect()
            ->route('dashboard.configuracion.planes.index')
            ->with('success', 'Plan actualizado.');
    }

    public function destroy(PlanPrecio $plan)
    {
        $plan->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('dashboard.configuracion.planes.index')
            ->with('success', 'Plan eliminado.');
    }
}
