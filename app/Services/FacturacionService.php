<?php

namespace App\Services;

use App\Models\Cita;
use App\Models\Gasto;
use App\Models\IngresoManual;
use Carbon\Carbon;

class FacturacionService
{
    public function resumenMes(?Carbon $mes = null): array
    {
        $mes = ($mes ?? now())->copy();
        $inicio = $mes->copy()->startOfMonth();
        $fin = $mes->copy()->endOfMonth();

        $ingresos = $this->ingresosEntre($inicio, $fin);
        $gastos = $this->gastosEntre($inicio, $fin);

        return [
            'ingresos' => $ingresos,
            'gastos' => $gastos,
            'neto' => round($ingresos - $gastos, 2),
            'citas_pendientes_pago' => Cita::query()->pendientesDePago()->count(),
        ];
    }

    public function ingresosEntre(Carbon $inicio, Carbon $fin): float
    {
        $citas = (float) Cita::query()
            ->where('pagado', true)
            ->whereBetween('pagado_at', [$inicio, $fin])
            ->sum('monto');

        $manuales = (float) IngresoManual::query()
            ->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->sum('monto');

        return round($citas + $manuales, 2);
    }

    public function gastosEntre(Carbon $inicio, Carbon $fin): float
    {
        return round((float) Gasto::query()
            ->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->sum('monto'), 2);
    }

    /**
     * Las gráficas del panel de facturación solo deben reflejar citas ya
     * pagadas (no ingresos manuales): a diferencia de ingresosEntre().
     */
    public function citasPagadasEntre(Carbon $inicio, Carbon $fin): array
    {
        $citas = Cita::query()->where('pagado', true)->whereBetween('pagado_at', [$inicio, $fin])->get(['monto']);

        return [
            'total' => round((float) $citas->sum('monto'), 2),
            'count' => $citas->count(),
        ];
    }

    public function serieDiariaCitasDelMes(?Carbon $mes = null): array
    {
        $mes = ($mes ?? now())->copy();
        $inicio = $mes->copy()->startOfMonth();
        $fin = $mes->copy()->endOfMonth();
        $limite = $mes->isSameMonth(now()) ? now() : $fin;

        $porDia = Cita::query()
            ->where('pagado', true)
            ->whereBetween('pagado_at', [$inicio, $fin])
            ->get(['monto', 'pagado_at'])
            ->groupBy(fn ($cita) => $cita->pagado_at->format('Y-m-d'));

        $serie = [];
        $cursor = $inicio->copy();

        while ($cursor->lte($fin) && $cursor->lte($limite)) {
            $clave = $cursor->format('Y-m-d');

            $serie[] = [
                'etiqueta' => $cursor->format('d/m'),
                'total' => round((float) $porDia->get($clave, collect())->sum('monto'), 2),
            ];

            $cursor->addDay();
        }

        return $serie;
    }

    public function serieMensual(int $meses, ?Carbon $hasta = null): array
    {
        $meses = in_array($meses, [6, 12], true) ? $meses : 6;
        $hasta = ($hasta ?? now())->copy();
        $serie = [];

        for ($i = $meses - 1; $i >= 0; $i--) {
            $mes = $hasta->copy()->subMonths($i);

            $serie[] = [
                'etiqueta' => ucfirst($mes->translatedFormat('M Y')),
                'total' => $this->ingresosEntre($mes->copy()->startOfMonth(), $mes->copy()->endOfMonth()),
            ];
        }

        return $serie;
    }

    public function serieMensualCitas(int $meses, ?Carbon $hasta = null): array
    {
        $meses = in_array($meses, [6, 12], true) ? $meses : 6;
        $hasta = ($hasta ?? now())->copy();
        $serie = [];

        for ($i = $meses - 1; $i >= 0; $i--) {
            $mes = $hasta->copy()->subMonths($i);

            $serie[] = [
                'etiqueta' => ucfirst($mes->translatedFormat('M Y')),
                'total' => $this->citasPagadasEntre($mes->copy()->startOfMonth(), $mes->copy()->endOfMonth())['total'],
            ];
        }

        return $serie;
    }

    public function porCobrar(): float
    {
        return round((float) Cita::query()->pendientesDePago()->sum('monto'), 2);
    }

    public function movimientosMes(?Carbon $mes = null): array
    {
        $mes = ($mes ?? now())->copy();
        $inicio = $mes->copy()->startOfMonth();
        $fin = $mes->copy()->endOfMonth();

        $movimientos = [];

        foreach (Cita::query()->where('pagado', true)->whereBetween('pagado_at', [$inicio, $fin])->get() as $cita) {
            $movimientos[] = [
                'id' => null,
                'tipo' => 'cita',
                'dir' => 'in',
                'fecha' => $cita->pagado_at->format('Y-m-d'),
                'concepto' => 'Sesión · ' . $cita->paciente_nombre,
                'detalle' => $cita->modalidad_label,
                'monto' => (float) $cita->monto,
            ];
        }

        foreach (IngresoManual::query()->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])->get() as $ingreso) {
            $movimientos[] = [
                'id' => $ingreso->id,
                'tipo' => 'manual',
                'dir' => 'in',
                'fecha' => $ingreso->fecha->format('Y-m-d'),
                'concepto' => $ingreso->descripcion,
                'detalle' => 'Ingreso manual',
                'monto' => (float) $ingreso->monto,
            ];
        }

        foreach (Gasto::query()->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])->get() as $gasto) {
            $movimientos[] = [
                'id' => $gasto->id,
                'tipo' => 'gasto',
                'dir' => 'out',
                'fecha' => $gasto->fecha->format('Y-m-d'),
                'concepto' => $gasto->descripcion,
                'detalle' => $gasto->categoria ?: 'Sin categoría',
                'monto' => (float) $gasto->monto,
            ];
        }

        usort($movimientos, fn ($a, $b) => strcmp($b['fecha'], $a['fecha']));

        return $movimientos;
    }

    public function gastosPorCategoriaMes(?Carbon $mes = null): array
    {
        $mes = ($mes ?? now())->copy();
        $inicio = $mes->copy()->startOfMonth();
        $fin = $mes->copy()->endOfMonth();

        return Gasto::query()
            ->whereBetween('fecha', [$inicio->toDateString(), $fin->toDateString()])
            ->get()
            ->groupBy(fn ($gasto) => $gasto->categoria ?: 'Sin categoría')
            ->map(fn ($grupo) => round((float) $grupo->sum('monto'), 2))
            ->sortDesc()
            ->all();
    }
}
