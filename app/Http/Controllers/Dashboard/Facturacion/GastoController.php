<?php

namespace App\Http\Controllers\Dashboard\Facturacion;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\Facturacion\GastoRequest;
use App\Models\Gasto;
use Illuminate\Http\Request;

class GastoController extends Controller
{
    public function store(GastoRequest $request)
    {
        $gasto = Gasto::create($request->validated());

        if ($request->wantsJson()) {
            return response()->json(['success' => true, 'gasto' => $gasto]);
        }

        return redirect()
            ->route('dashboard.facturacion.index', ['tab' => 'gastos'])
            ->with('success', 'Gasto registrado correctamente.');
    }

    public function destroy(Request $request, Gasto $gasto)
    {
        $gasto->delete();

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json(['success' => true]);
        }

        return redirect()
            ->route('dashboard.facturacion.index', ['tab' => 'gastos'])
            ->with('success', 'Gasto eliminado.');
    }
}
