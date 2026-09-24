<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Articulo;
use App\Models\Cita;
use App\Models\Faq;
use App\Models\Historia;
use App\Models\Paciente;
use Illuminate\Http\Request;

class BuscadorController extends Controller
{
    public function index(Request $request)
    {
        $q = trim((string) $request->query('q', ''));

        $resultados = [
            'pacientes'  => collect(),
            'citas'      => collect(),
            'historias'  => collect(),
            'articulos'  => collect(),
            'faqs'       => collect(),
        ];

        $total = 0;

        if (mb_strlen($q) >= 2) {
            $like = '%' . $q . '%';

            $resultados['pacientes'] = Paciente::query()
                ->where(function ($qb) use ($like) {
                    $qb->where('nombre', 'like', $like)
                       ->orWhere('apellidos', 'like', $like)
                       ->orWhere('telefono', 'like', $like)
                       ->orWhere('email', 'like', $like)
                       ->orWhere('dni', 'like', $like);
                })
                ->orderBy('nombre')
                ->limit(20)
                ->get();

            $resultados['citas'] = Cita::query()
                ->where(function ($qb) use ($like) {
                    $qb->where('nombre_provisional', 'like', $like)
                       ->orWhere('telefono_provisional', 'like', $like)
                       ->orWhere('motivo', 'like', $like)
                       ->orWhere('notas_internas', 'like', $like);
                })
                ->orderByDesc('fecha_inicio')
                ->limit(20)
                ->get();

            $resultados['historias'] = Historia::query()
                ->where(function ($qb) use ($like) {
                    $qb->where('titulo', 'like', $like)
                       ->orWhere('contenido', 'like', $like);
                })
                ->with('paciente')
                ->orderByDesc('fecha_sesion')
                ->limit(20)
                ->get();

            $resultados['articulos'] = Articulo::query()
                ->where(function ($qb) use ($like) {
                    $qb->where('titulo', 'like', $like)
                       ->orWhere('extracto', 'like', $like)
                       ->orWhere('contenido', 'like', $like);
                })
                ->orderByDesc('updated_at')
                ->limit(20)
                ->get();

            $resultados['faqs'] = Faq::query()
                ->where(function ($qb) use ($like) {
                    $qb->where('pregunta', 'like', $like)
                       ->orWhere('respuesta', 'like', $like);
                })
                ->orderBy('orden')
                ->limit(20)
                ->get();

            $total = collect($resultados)->sum(fn ($items) => $items->count());
        }

        return view('dashboard.buscador.index', compact('q', 'resultados', 'total'));
    }
}
