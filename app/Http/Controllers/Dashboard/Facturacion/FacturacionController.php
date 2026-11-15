<?php

namespace App\Http\Controllers\Dashboard\Facturacion;

use App\Http\Controllers\Controller;
use App\Services\FacturacionService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class FacturacionController extends Controller
{
    public function __construct(private FacturacionService $facturacion)
    {
    }

    public function index(Request $request)
    {
        $mes = $this->resolverMes($request->input('mes'));
        $mesAnterior = $mes->copy()->subMonth();

        $resumen = $this->facturacion->resumenMes($mes);
        $resumen['por_cobrar'] = $this->facturacion->porCobrar();

        $ingresosMesAnterior = $this->facturacion->ingresosEntre($mesAnterior->copy()->startOfMonth(), $mesAnterior->copy()->endOfMonth());
        $citasPagadasResumenMes = $this->facturacion->citasPagadasEntre($mes->copy()->startOfMonth(), $mes->copy()->endOfMonth());
        $citasPagadasResumenMesAnterior = $this->facturacion->citasPagadasEntre($mesAnterior->copy()->startOfMonth(), $mesAnterior->copy()->endOfMonth());

        return view('dashboard.facturacion.index', [
            'resumen' => $resumen,
            'ingresosMesAnterior' => $ingresosMesAnterior,
            'citasPagadasResumenMes' => $citasPagadasResumenMes,
            'citasPagadasResumenMesAnterior' => $citasPagadasResumenMesAnterior,
            'serieDiariaCitas' => $this->facturacion->serieDiariaCitasDelMes($mes),
            'serieMensualCitas' => $this->facturacion->serieMensualCitas(12, $mes),
            'movimientos' => $this->facturacion->movimientosMes($mes),
            'gastosPorCategoria' => $this->facturacion->gastosPorCategoriaMes($mes),
            'mesActual' => ucfirst($mes->translatedFormat('F Y')),
            'mesSeleccionado' => $mes->format('Y-m'),
            'mesesDisponibles' => $this->mesesDisponibles(),
        ]);
    }

    private function resolverMes(?string $valor): Carbon
    {
        if ($valor) {
            try {
                return Carbon::createFromFormat('Y-m', $valor)->startOfMonth();
            } catch (\Throwable) {
                // Valor inválido: se usa el mes actual.
            }
        }

        return now()->startOfMonth();
    }

    /** @return array<int, array{valor: string, etiqueta: string}> */
    private function mesesDisponibles(): array
    {
        $meses = [];

        for ($i = 0; $i < 12; $i++) {
            $mes = now()->copy()->subMonths($i);
            $meses[] = [
                'valor' => $mes->format('Y-m'),
                'etiqueta' => ucfirst($mes->translatedFormat('F Y')),
            ];
        }

        return $meses;
    }
}
