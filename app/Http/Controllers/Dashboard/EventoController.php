<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Evento;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EventoController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'titulo'       => ['required', 'string', 'max:200'],
            'descripcion'  => ['nullable', 'string', 'max:2000'],
            'color'        => ['nullable', 'string', 'max:20'],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin'    => ['required', 'date', 'after_or_equal:fecha_inicio'],
            'all_day'      => ['nullable', 'boolean'],
            'ubicacion'    => ['nullable', 'string', 'max:200'],
        ]);

        $validated['color'] = $validated['color'] ?? '#9b59b6';
        $validated['all_day'] = (bool) ($validated['all_day'] ?? false);

        $evento = Evento::create($validated);

        return response()->json([
            'ok' => true,
            'evento' => $this->toEvento($evento),
        ], 201);
    }

    public function update(Request $request, Evento $evento): JsonResponse
    {
        $validated = $request->validate([
            'titulo'       => ['sometimes', 'required', 'string', 'max:200'],
            'descripcion'  => ['nullable', 'string', 'max:2000'],
            'color'        => ['nullable', 'string', 'max:20'],
            'fecha_inicio' => ['sometimes', 'required', 'date'],
            'fecha_fin'    => ['sometimes', 'required', 'date', 'after_or_equal:fecha_inicio'],
            'all_day'      => ['nullable', 'boolean'],
            'ubicacion'    => ['nullable', 'string', 'max:200'],
        ]);

        $evento->update($validated);

        return response()->json([
            'ok' => true,
            'evento' => $this->toEvento($evento->fresh()),
        ]);
    }

    public function destroy(Evento $evento): JsonResponse
    {
        $evento->delete();
        return response()->json(['ok' => true]);
    }

    private function toEvento(Evento $e): array
    {
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
            'all_day'     => $e->all_day,
            'estado'      => 'Evento',
            'modalidad'   => 'Evento',
        ];
    }
}
