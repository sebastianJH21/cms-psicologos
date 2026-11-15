<?php

namespace App\Http\Controllers\Dashboard\Facturacion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Facturacion\IngresoManualRequest;
use App\Models\IngresoManual;
use Illuminate\Http\Request;

class IngresoManualController extends Controller
{
    public function store(IngresoManualRequest $request)
    {
        $ingreso = IngresoManual::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'ingreso' => $ingreso]);
        }

        return redirect()
            ->route('dashboard.facturacion.index', ['tab' => 'ingresos'])
            ->with('success', 'Ingreso registrado correctamente.');
    }

    public function destroy(Request $request, IngresoManual $ingreso)
    {
        $ingreso->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('dashboard.facturacion.index', ['tab' => 'ingresos'])
            ->with('success', 'Ingreso eliminado.');
    }
}
