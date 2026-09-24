<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\PeriodoVacaciones;
use Illuminate\Http\Request;

class PeriodoVacacionesController extends Controller
{
    public function store(Request $request)
    {
        $data = $request->validate([
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['required', 'date', 'after_or_equal:fecha_inicio'],
        ], [
            'fecha_inicio.required' => 'La fecha de inicio es obligatoria.',
            'fecha_inicio.date'     => 'La fecha de inicio no es válida.',
            'fecha_fin.required'    => 'La fecha de fin es obligatoria.',
            'fecha_fin.date'        => 'La fecha de fin no es válida.',
            'fecha_fin.after_or_equal' => 'La fecha de fin debe ser igual o posterior a la de inicio.',
        ]);

        $periodo = PeriodoVacaciones::create($data);

        return response()->json([
            'ok'      => true,
            'periodo' => [
                'id'           => $periodo->id,
                'fecha_inicio' => $periodo->fecha_inicio->format('d/m/Y'),
                'fecha_fin'    => $periodo->fecha_fin->format('d/m/Y'),
            ],
        ]);
    }

    public function destroy(PeriodoVacaciones $periodo)
    {
        $periodo->delete();
        return response()->json(['ok' => true]);
    }
}
