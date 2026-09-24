<?php

namespace App\Http\Controllers\Dashboard\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Configuracion\ServicioRequest;
use App\Models\Servicio;

class ServicioController extends Controller
{
    public function index()
    {
        $servicios = Servicio::orderBy('orden')->orderBy('id')->get();

        return view('dashboard.configuracion.servicios.index', compact('servicios'));
    }

    public function create()
    {
        return view('dashboard.configuracion.servicios.create');
    }

    public function store(ServicioRequest $request)
    {
        $datos = $request->validated();
        $datos['orden'] = $datos['orden'] ?? ((int) (Servicio::max('orden') ?? 0) + 1);

        Servicio::create($datos);

        return redirect()
            ->route('dashboard.configuracion.servicios.index')
            ->with('success', 'Servicio creado correctamente.');
    }

    public function edit(Servicio $servicio)
    {
        return view('dashboard.configuracion.servicios.edit', compact('servicio'));
    }

    public function update(ServicioRequest $request, Servicio $servicio)
    {
        $servicio->update($request->validated());

        return redirect()
            ->route('dashboard.configuracion.servicios.index')
            ->with('success', 'Servicio actualizado.');
    }

    public function destroy(Servicio $servicio)
    {
        $servicio->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('dashboard.configuracion.servicios.index')
            ->with('success', 'Servicio eliminado.');
    }
}
