<?php

namespace App\Http\Controllers\Dashboard\Configuracion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Configuracion\TerapiaRequest;
use App\Models\Terapia;

class TerapiaController extends Controller
{
    public function index()
    {
        $terapias = Terapia::orderBy('orden')->orderBy('id')->get();

        return view('dashboard.configuracion.terapias.index', compact('terapias'));
    }

    public function create()
    {
        return view('dashboard.configuracion.terapias.create');
    }

    public function store(TerapiaRequest $request)
    {
        $datos = $request->validated();
        $datos['orden'] = $datos['orden'] ?? ((int) (Terapia::max('orden') ?? 0) + 1);

        Terapia::create($datos);

        return redirect()
            ->route('dashboard.configuracion.terapias.index')
            ->with('success', 'Terapia creada correctamente.');
    }

    public function edit(Terapia $terapia)
    {
        return view('dashboard.configuracion.terapias.edit', compact('terapia'));
    }

    public function update(TerapiaRequest $request, Terapia $terapia)
    {
        $terapia->update($request->validated());

        return redirect()
            ->route('dashboard.configuracion.terapias.index')
            ->with('success', 'Terapia actualizada.');
    }

    public function destroy(Terapia $terapia)
    {
        $terapia->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('dashboard.configuracion.terapias.index')
            ->with('success', 'Terapia eliminada.');
    }
}
