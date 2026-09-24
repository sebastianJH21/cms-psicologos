<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\ProteccionDatosRequest;
use App\Models\Paciente;
use App\Models\Setting;
use App\Services\PdfPlantillaRenderer;
use Barryvdh\DomPDF\Facade\Pdf;
use Mews\Purifier\Facades\Purifier;

class ProteccionDatosController extends Controller
{
    public function __construct(private PdfPlantillaRenderer $renderer)
    {
    }

    public function edit()
    {
        return view('dashboard.configuracion.proteccion-datos.edit', [
            'plantilla'    => $this->renderer->plantillaHtml(),
            'placeholders' => $this->renderer->placeholdersDisponibles(),
        ]);
    }

    public function update(ProteccionDatosRequest $request)
    {
        $html = Purifier::clean($request->validated('plantilla_html'), 'blog');
        Setting::set('proteccion_datos.plantilla_html', $html, 'proteccion_datos');

        return redirect()
            ->route('dashboard.configuracion.proteccion-datos.edit')
            ->with('success', 'Plantilla actualizada correctamente.');
    }

    public function descargarVacio()
    {
        $html = $this->renderer->rellenarVacio();
        $pdf = Pdf::loadHTML($this->wrap($html))->setPaper('a4');

        return $pdf->download('proteccion-datos-vacio.pdf');
    }

    public function descargarRelleno(Paciente $paciente)
    {
        $html = $this->renderer->rellenarParaPaciente($paciente);
        $pdf = Pdf::loadHTML($this->wrap($html))->setPaper('a4');

        $nombre = str_replace(' ', '-', strtolower($paciente->nombre_completo));

        return $pdf->download("proteccion-datos-{$nombre}.pdf");
    }

    private function wrap(string $contenido): string
    {
        $css = <<<'CSS'
            body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #222; line-height: 1.5; }
            h1 { font-size: 18px; margin-bottom: 0.4em; }
            h2 { font-size: 14px; margin-top: 1.4em; margin-bottom: 0.5em; color: #2c4a7e; }
            p { margin: 0 0 0.8em 0; }
            table { border-collapse: collapse; }
            td, th { padding: 6px; vertical-align: top; }
        CSS;

        return <<<HTML
        <!DOCTYPE html>
        <html lang="es">
        <head>
            <meta charset="UTF-8">
            <style>{$css}</style>
        </head>
        <body>{$contenido}</body>
        </html>
        HTML;
    }
}
