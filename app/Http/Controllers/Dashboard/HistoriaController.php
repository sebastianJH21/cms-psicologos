<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\HistoriaRequest;
use App\Models\Historia;
use App\Models\HistoriaArchivo;
use App\Models\Paciente;
use App\Services\ImagenOptimizer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class HistoriaController extends Controller
{
    public function __construct(private ImagenOptimizer $optimizador)
    {
    }

    public function indexGeneral(Request $request)
    {
        $query = Historia::with('paciente')->orderBy('fecha_sesion', 'desc');

        if ($buscar = trim((string) $request->input('q', ''))) {
            $query->whereHas('paciente', function ($q) use ($buscar) {
                $q->where('nombre', 'like', "%{$buscar}%")
                    ->orWhere('apellidos', 'like', "%{$buscar}%")
                    ->orWhere('telefono', 'like', "%{$buscar}%");
            });
        }

        $historias = $query->paginate(15)->withQueryString();

        if ($request->ajax()) {
            return response()->json([
                'html' => view('dashboard.historias.partials.tabla', compact('historias'))->render(),
            ]);
        }

        return view('dashboard.historias.index', compact('historias'));
    }

    public function index(Paciente $paciente)
    {
        $historias = $paciente->historias()
            ->withCount('archivos')
            ->orderBy('fecha_sesion', 'desc')
            ->paginate(10);

        return view('dashboard.pacientes.historias.index', compact('paciente', 'historias'));
    }

    public function create(Paciente $paciente)
    {
        return view('dashboard.pacientes.historias.create', compact('paciente'));
    }

    public function store(HistoriaRequest $request, Paciente $paciente)
    {
        $historia = $paciente->historias()->create($request->safe()->except('archivos'));

        $this->guardarArchivos($request, $historia, $paciente);

        return redirect()
            ->route('dashboard.pacientes.historias.show', [$paciente, $historia])
            ->with('success', 'Entrada de historia creada correctamente.');
    }

    public function show(Paciente $paciente, Historia $historia)
    {
        abort_if($historia->paciente_id !== $paciente->id, 404);

        $historia->load('archivos');
        $historia->archivos->each->setRelation('historia', $historia);

        return view('dashboard.pacientes.historias.show', compact('paciente', 'historia'));
    }

    public function edit(Paciente $paciente, Historia $historia)
    {
        abort_if($historia->paciente_id !== $paciente->id, 404);

        $historia->load('archivos');
        $historia->archivos->each->setRelation('historia', $historia);

        return view('dashboard.pacientes.historias.edit', compact('paciente', 'historia'));
    }

    public function update(HistoriaRequest $request, Paciente $paciente, Historia $historia)
    {
        abort_if($historia->paciente_id !== $paciente->id, 404);

        $historia->update($request->safe()->except('archivos'));

        $this->guardarArchivos($request, $historia, $paciente);

        return redirect()
            ->route('dashboard.pacientes.historias.show', [$paciente, $historia])
            ->with('success', 'Historia actualizada correctamente.');
    }

    public function destroy(Paciente $paciente, Historia $historia)
    {
        abort_if($historia->paciente_id !== $paciente->id, 404);

        foreach ($historia->archivos as $archivo) {
            $this->borrarArchivoFisico($archivo->ruta);
        }

        $historia->delete();

        return redirect()
            ->route('dashboard.pacientes.historias.index', $paciente)
            ->with('success', 'Entrada de historia eliminada correctamente.');
    }

    public function destroyArchivo(Paciente $paciente, Historia $historia, HistoriaArchivo $archivo)
    {
        abort_if($historia->paciente_id !== $paciente->id, 404);
        abort_if($archivo->historia_id !== $historia->id, 404);

        $this->borrarArchivoFisico($archivo->ruta);
        $archivo->delete();

        return back()->with('success', 'Archivo eliminado correctamente.');
    }

    public function verArchivo(Paciente $paciente, Historia $historia, HistoriaArchivo $archivo): Response
    {
        abort_if($historia->paciente_id !== $paciente->id, 404);
        abort_if($archivo->historia_id !== $historia->id, 404);

        $disco = $this->discoArchivo($archivo->ruta);
        abort_if($disco === null, 404);

        return Storage::disk($disco)->response(
            $archivo->ruta,
            $archivo->nombre_original,
            [
                'Content-Type' => $archivo->mime_type ?: 'application/octet-stream',
                'X-Content-Type-Options' => 'nosniff',
            ]
        );
    }

    private function guardarArchivos(HistoriaRequest $request, Historia $historia, Paciente $paciente): void
    {
        if (!$request->hasFile('archivos')) {
            return;
        }

        foreach ($request->file('archivos') as $file) {
            $mime = strtolower((string) $file->getMimeType());
            $dir = "historias/{$paciente->id}/{$historia->id}";

            if (str_contains($mime, 'pdf')) {
                $ruta = $file->store($dir, 'local');
            } else {
                $ruta = $this->optimizador->procesarYGuardar($file, $dir, 1600, 80, 'local');
            }

            $rutaFisica = Storage::disk('local')->path($ruta);
            $tamanio = is_file($rutaFisica) ? filesize($rutaFisica) : $file->getSize();

            $historia->archivos()->create([
                'nombre_original' => $file->getClientOriginalName(),
                'ruta'            => $ruta,
                'tipo'            => str_contains($mime, 'pdf') ? 'pdf' : 'imagen',
                'mime_type'       => $mime,
                'tamanio'         => $tamanio,
            ]);
        }
    }

    private function discoArchivo(string $ruta): ?string
    {
        if (Storage::disk('local')->exists($ruta)) {
            return 'local';
        }
        if (Storage::disk('public')->exists($ruta)) {
            return 'public';
        }
        return null;
    }

    private function borrarArchivoFisico(string $ruta): void
    {
        if (Storage::disk('local')->exists($ruta)) {
            Storage::disk('local')->delete($ruta);
        }
        if (Storage::disk('public')->exists($ruta)) {
            Storage::disk('public')->delete($ruta);
        }
    }
}
