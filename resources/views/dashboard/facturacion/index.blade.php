@extends('dashboard.layout')

@section('titulo', 'Facturación')

@php
    $breadcrumbs = [
        ['label' => 'Inicio', 'url' => route('dashboard.home')],
        ['label' => 'Facturación'],
    ];

    $deltaIngresos = null;
    if ($ingresosMesAnterior !== null && $ingresosMesAnterior > 0) {
        $deltaIngresos = round((($resumen['ingresos'] - $ingresosMesAnterior) / $ingresosMesAnterior) * 100, 1);
    }
    $margenNeto = $resumen['ingresos'] > 0 ? round(($resumen['neto'] / $resumen['ingresos']) * 100) : null;

    $movimientosIn = array_values(array_filter($movimientos, fn ($m) => $m['dir'] === 'in'));
    $movimientosOut = array_values(array_filter($movimientos, fn ($m) => $m['dir'] === 'out'));
    $citasPagadasMes = array_values(array_filter($movimientosIn, fn ($m) => $m['tipo'] === 'cita'));
    $ingresosManualesMes = array_values(array_filter($movimientosIn, fn ($m) => $m['tipo'] === 'manual'));
    $totalGastosMes = array_sum(array_column($movimientosOut, 'monto'));

    $tabInicial = in_array(request('tab'), ['resumen', 'ingresos', 'gastos', 'pagos'], true) ? request('tab') : 'resumen';
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/dashboard/facturacion.css') }}">
@endpush

@section('contenido')
    <header class="factu-topbar">
        <div>
            <p class="factu-crumb">Módulos / Facturación</p>
            <h1 class="page-header__title"><i class="fa-solid fa-sack-dollar" aria-hidden="true"></i> Facturación</h1>
        </div>
        <div class="factu-topbar__actions">
            <form method="GET" action="{{ route('dashboard.facturacion.index') }}" class="factu-periodo-form" id="form-periodo-facturacion">
                <input type="hidden" name="tab" value="{{ $tabInicial }}">
                <label class="factu-periodo-label" for="selector-mes-facturacion">
                    <i class="fa-regular fa-calendar" aria-hidden="true"></i>
                    <select name="mes" id="selector-mes-facturacion" aria-label="Mes de facturación">
                        @foreach ($mesesDisponibles as $opcion)
                            <option value="{{ $opcion['valor'] }}" {{ $opcion['valor'] === $mesSeleccionado ? 'selected' : '' }}>
                                {{ $opcion['etiqueta'] }}
                            </option>
                        @endforeach
                    </select>
                </label>
            </form>
            <button type="button" class="btn" data-open-modal="modal-gasto">
                <i class="fa-solid fa-minus" aria-hidden="true"></i> Gasto
            </button>
            <button type="button" class="btn btn--primary" data-open-modal="modal-ingreso">
                <i class="fa-solid fa-plus" aria-hidden="true"></i> Ingreso manual
            </button>
        </div>
    </header>

    <nav class="factu-tabs" id="factu-tabs" role="tablist" aria-label="Secciones de facturación">
        <button type="button" class="factu-tab {{ $tabInicial === 'resumen' ? 'is-active' : '' }}" data-tab="resumen" role="tab">Resumen</button>
        <button type="button" class="factu-tab {{ $tabInicial === 'ingresos' ? 'is-active' : '' }}" data-tab="ingresos" role="tab">Ingresos</button>
        <button type="button" class="factu-tab {{ $tabInicial === 'gastos' ? 'is-active' : '' }}" data-tab="gastos" role="tab">Gastos</button>
        <button type="button" class="factu-tab {{ $tabInicial === 'pagos' ? 'is-active' : '' }}" data-tab="pagos" role="tab">
            Pagos en línea <span class="factu-tag-soon">Pronto</span>
        </button>
    </nav>

    {{-- ================= RESUMEN ================= --}}
    <div class="factu-pane {{ $tabInicial === 'resumen' ? 'is-active' : '' }}" data-pane="resumen">
        <div class="factu-kpis">
            <article class="factu-kpi">
                <div class="factu-kpi__top">
                    <span class="factu-kpi__label">Ingresos del mes</span>
                    <span class="factu-kpi__ico factu-kpi__ico--success"><i class="fa-solid fa-arrow-trend-up" aria-hidden="true"></i></span>
                </div>
                <strong class="factu-kpi__value">${{ number_format($resumen['ingresos'], 0, ',', '.') }}</strong>
                <div class="factu-kpi__foot">
                    @if ($deltaIngresos !== null)
                        <span class="factu-delta {{ $deltaIngresos >= 0 ? 'is-up' : 'is-down' }}">
                            <i class="fa-solid fa-arrow-{{ $deltaIngresos >= 0 ? 'up' : 'down' }}" aria-hidden="true"></i>
                            {{ number_format(abs($deltaIngresos), 1, ',', '.') }}%
                        </span>
                        vs. mes anterior
                    @else
                        Citas pagadas + ingresos manuales
                    @endif
                </div>
            </article>
            <article class="factu-kpi">
                <div class="factu-kpi__top">
                    <span class="factu-kpi__label">Gastos del mes</span>
                    <span class="factu-kpi__ico factu-kpi__ico--danger"><i class="fa-solid fa-arrow-trend-down" aria-hidden="true"></i></span>
                </div>
                <strong class="factu-kpi__value">${{ number_format($resumen['gastos'], 0, ',', '.') }}</strong>
                <div class="factu-kpi__foot">Alquiler y otros gastos registrados</div>
            </article>
            <article class="factu-kpi">
                <div class="factu-kpi__top">
                    <span class="factu-kpi__label">Utilidad neta</span>
                    <span class="factu-kpi__ico factu-kpi__ico--info"><i class="fa-solid fa-scale-balanced" aria-hidden="true"></i></span>
                </div>
                <strong class="factu-kpi__value">${{ number_format($resumen['neto'], 0, ',', '.') }}</strong>
                <div class="factu-kpi__foot">{{ $margenNeto !== null ? 'Margen ' . $margenNeto . '%' : 'Ingresos menos gastos' }}</div>
            </article>
            <article class="factu-kpi">
                <div class="factu-kpi__top">
                    <span class="factu-kpi__label">Por cobrar</span>
                    <span class="factu-kpi__ico factu-kpi__ico--warning"><i class="fa-solid fa-clock" aria-hidden="true"></i></span>
                </div>
                <strong class="factu-kpi__value">${{ number_format($resumen['por_cobrar'], 0, ',', '.') }}</strong>
                <div class="factu-kpi__foot">
                    <a href="{{ route('dashboard.citas.index', ['pendiente_pago' => 1]) }}" class="factu-kpi__link">
                        {{ $resumen['citas_pendientes_pago'] }} {{ $resumen['citas_pendientes_pago'] === 1 ? 'cita confirmada sin pago' : 'citas confirmadas sin pago' }} →
                    </a>
                </div>
            </article>
        </div>

        <div class="factu-charts-grid">
            <div class="panel factu-chart-card">
                <header class="panel__header">
                    <div>
                        <h2 class="panel__title">Ingresos del mes</h2>
                        <p class="panel__subtitle">Solo citas pagadas · {{ $mesActual }}</p>
                    </div>
                    <div class="factu-seg" data-seg-mes>
                        <button type="button" class="factu-seg__btn is-active" data-value="diario">Diario</button>
                        <button type="button" class="factu-seg__btn" data-value="acumulado">Acumulado</button>
                    </div>
                </header>
                <div class="factu-chart-stats" data-stats-mes></div>
                <div class="factu-linechart" data-chart-mes role="img" aria-label="Ingresos diarios del mes actual"></div>
                <div class="factu-legend" data-legend-mes>
                    <span><i></i>Citas pagadas</span>
                    <span data-legend-ref-mes><i class="is-dash"></i></span>
                </div>
            </div>

            <div class="panel factu-chart-card">
                <header class="panel__header">
                    <div>
                        <h2 class="panel__title">Tendencia de ingresos</h2>
                        <p class="panel__subtitle">Ingresos mensuales por citas pagadas</p>
                    </div>
                    <div class="factu-seg" data-seg-tendencia>
                        <button type="button" class="factu-seg__btn is-active" data-value="6">6 meses</button>
                        <button type="button" class="factu-seg__btn" data-value="12">12 meses</button>
                    </div>
                </header>
                <div class="factu-chart-stats" data-stats-tendencia></div>
                <div class="factu-linechart" data-chart-tendencia role="img" aria-label="Tendencia mensual de ingresos"></div>
                <div class="factu-legend">
                    <span><i class="is-info"></i>Ingreso mensual</span>
                    <span><i class="is-dash"></i>Promedio</span>
                </div>
            </div>
        </div>

        <div class="panel">
            <header class="panel__header factu-toolbar">
                <div>
                    <h2 class="panel__title">Últimos movimientos</h2>
                    <p class="panel__subtitle">Ingresos por citas, ingresos manuales y gastos de {{ $mesActual }}</p>
                </div>
                <div class="factu-seg" data-seg-movimientos>
                    <button type="button" class="factu-seg__btn is-active" data-value="todos">Todos</button>
                    <button type="button" class="factu-seg__btn" data-value="in">Ingresos</button>
                    <button type="button" class="factu-seg__btn" data-value="out">Gastos</button>
                </div>
            </header>
            @if (empty($movimientos))
                <div class="empty-state">
                    <i class="fa-regular fa-chart-bar empty-state__icon" aria-hidden="true"></i>
                    <p class="empty-state__title">Todavía no hay movimientos este mes</p>
                    <p class="empty-state__hint">Marca citas como pagadas, o añade un gasto o ingreso manual.</p>
                </div>
            @else
                <div class="data-table__wrapper" data-tabla-wrapper="movimientos">
                    <table class="data-table" data-tabla-movimientos>
                        <thead>
                            <tr>
                                <th scope="col">Fecha</th>
                                <th scope="col">Concepto</th>
                                <th scope="col">Tipo</th>
                                <th scope="col" class="factu-right">Monto</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($movimientos as $m)
                                <tr data-dir="{{ $m['dir'] }}">
                                    <td>{{ \Carbon\Carbon::parse($m['fecha'])->format('d/m') }}</td>
                                    <td>
                                        <span class="data-cell-link" style="cursor:default">{{ $m['concepto'] }}</span>
                                        <br><small class="data-table__sub">{{ $m['detalle'] }}</small>
                                    </td>
                                    <td>
                                        @if ($m['tipo'] === 'cita')
                                            <span class="badge badge--success">Cita</span>
                                        @elseif ($m['tipo'] === 'manual')
                                            <span class="badge badge--info">Manual</span>
                                        @else
                                            <span class="badge badge--danger">Gasto</span>
                                        @endif
                                    </td>
                                    <td class="factu-right">
                                        <span class="factu-monto factu-monto--{{ $m['dir'] }}">{{ $m['dir'] === 'in' ? '+' : '−' }} ${{ number_format($m['monto'], 0, ',', '.') }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @include('dashboard.facturacion.partials.paginador', ['id' => 'movimientos'])
            @endif
        </div>
    </div>

    {{-- ================= INGRESOS ================= --}}
    <div class="factu-pane {{ $tabInicial === 'ingresos' ? 'is-active' : '' }}" data-pane="ingresos">
        <div class="factu-minis">
            <div class="factu-mini">
                <small>Total ingresos</small>
                <strong>${{ number_format(array_sum(array_column($movimientosIn, 'monto')), 0, ',', '.') }}</strong>
            </div>
            <div class="factu-mini">
                <small>Citas pagadas · {{ count($citasPagadasMes) }}</small>
                <strong>${{ number_format(array_sum(array_column($citasPagadasMes, 'monto')), 0, ',', '.') }}</strong>
            </div>
            <div class="factu-mini">
                <small>Ingresos manuales · {{ count($ingresosManualesMes) }}</small>
                <strong>${{ number_format(array_sum(array_column($ingresosManualesMes, 'monto')), 0, ',', '.') }}</strong>
            </div>
        </div>

        <div class="panel">
            <header class="panel__header">
                <h2 class="panel__title">Ingresos de {{ $mesActual }}</h2>
            </header>
            @if (empty($movimientosIn))
                <div class="empty-state">
                    <i class="fa-regular fa-circle-up empty-state__icon" aria-hidden="true"></i>
                    <p class="empty-state__title">Todavía no hay ingresos este mes</p>
                </div>
            @else
                <div class="data-table__wrapper" data-tabla-wrapper="ingresos">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th scope="col">Fecha</th>
                                <th scope="col">Concepto</th>
                                <th scope="col">Origen</th>
                                <th scope="col" class="factu-right">Monto</th>
                                <th scope="col" class="data-table__actions">Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($movimientosIn as $m)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($m['fecha'])->format('d/m') }}</td>
                                    <td>{{ $m['concepto'] }}<br><small class="data-table__sub">{{ $m['detalle'] }}</small></td>
                                    <td>
                                        @if ($m['tipo'] === 'cita')
                                            <span class="badge badge--success">Cita</span>
                                        @else
                                            <span class="badge badge--info">Manual</span>
                                        @endif
                                    </td>
                                    <td class="factu-right"><span class="factu-monto factu-monto--in">${{ number_format($m['monto'], 0, ',', '.') }}</span></td>
                                    <td class="data-table__actions">
                                        @if ($m['tipo'] === 'manual')
                                            <form method="POST" action="{{ route('dashboard.facturacion.ingresos.destroy', $m['id']) }}"
                                                data-confirm="¿Eliminar este ingreso manual?"
                                                data-confirm-title="Eliminar ingreso"
                                                data-confirm-label="Sí, eliminar"
                                                data-confirm-style="danger"
                                                data-ajax-delete="true"
                                                data-delete-target="tr"
                                                style="display:contents">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn--icon btn--icon-danger" aria-label="Eliminar">
                                                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                @include('dashboard.facturacion.partials.paginador', ['id' => 'ingresos'])
            @endif
        </div>
    </div>

    {{-- ================= GASTOS ================= --}}
    <div class="factu-pane {{ $tabInicial === 'gastos' ? 'is-active' : '' }}" data-pane="gastos">
        <div class="factu-minis">
            <div class="factu-mini">
                <small>Total gastos</small>
                <strong>${{ number_format($totalGastosMes, 0, ',', '.') }}</strong>
            </div>
            <div class="factu-mini">
                <small>Mayor categoría</small>
                <strong>{{ array_key_first($gastosPorCategoria) ?? '—' }}</strong>
            </div>
            <div class="factu-mini">
                <small>% sobre ingresos</small>
                <strong>{{ $resumen['ingresos'] > 0 ? round($totalGastosMes / $resumen['ingresos'] * 100) . '%' : '—' }}</strong>
            </div>
        </div>

        <div class="factu-grid-2">
            <div class="panel">
                <header class="panel__header">
                    <h2 class="panel__title">Gastos de {{ $mesActual }}</h2>
                </header>
                @if (empty($movimientosOut))
                    <div class="empty-state">
                        <i class="fa-regular fa-receipt empty-state__icon" aria-hidden="true"></i>
                        <p class="empty-state__title">Sin gastos registrados este mes</p>
                    </div>
                @else
                    <div class="data-table__wrapper" data-tabla-wrapper="gastos">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th scope="col">Fecha</th>
                                    <th scope="col">Concepto</th>
                                    <th scope="col">Categoría</th>
                                    <th scope="col" class="factu-right">Monto</th>
                                    <th scope="col" class="data-table__actions">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($movimientosOut as $m)
                                    <tr>
                                        <td>{{ \Carbon\Carbon::parse($m['fecha'])->format('d/m') }}</td>
                                        <td>{{ $m['concepto'] }}</td>
                                        <td>{{ $m['detalle'] }}</td>
                                        <td class="factu-right"><span class="factu-monto factu-monto--out">${{ number_format($m['monto'], 0, ',', '.') }}</span></td>
                                        <td class="data-table__actions">
                                            <form method="POST" action="{{ route('dashboard.facturacion.gastos.destroy', $m['id']) }}"
                                                data-confirm="¿Eliminar este gasto?"
                                                data-confirm-title="Eliminar gasto"
                                                data-confirm-label="Sí, eliminar"
                                                data-confirm-style="danger"
                                                data-ajax-delete="true"
                                                data-delete-target="tr"
                                                style="display:contents">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn--icon btn--icon-danger" aria-label="Eliminar">
                                                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @include('dashboard.facturacion.partials.paginador', ['id' => 'gastos'])
                @endif
            </div>

            <div class="panel">
                <header class="panel__header">
                    <h2 class="panel__title">Gastos por categoría</h2>
                    <p class="panel__subtitle">Distribución del mes</p>
                </header>
                @if (empty($gastosPorCategoria))
                    <p class="form-hint">Aún no hay gastos este mes.</p>
                @else
                    @php $maxCategoria = max($gastosPorCategoria); @endphp
                    <div class="factu-cat-list">
                        @foreach ($gastosPorCategoria as $categoria => $monto)
                            <div class="factu-cat-row">
                                <div class="factu-cat-row__top">
                                    <span>{{ $categoria }}</span>
                                    <span>${{ number_format($monto, 0, ',', '.') }}</span>
                                </div>
                                <div class="factu-cat-row__bar"><i style="width:{{ $maxCategoria > 0 ? round($monto / $maxCategoria * 100) : 0 }}%"></i></div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ================= PAGOS EN LÍNEA (próximamente) ================= --}}
    <div class="factu-pane {{ $tabInicial === 'pagos' ? 'is-active' : '' }}" data-pane="pagos">
        <div class="panel factu-soon">
            <div>
                <span class="badge badge--info">Próximamente</span>
                <h2 class="factu-soon__title">Pagos en línea para tus pacientes</h2>
                <p class="factu-soon__desc">
                    Este espacio del módulo de facturación está reservado para conectar una pasarela de
                    pago. Cuando lo actives, las citas pagadas en línea se marcarán automáticamente y
                    alimentarán tus reportes, sin tocar el resto de la facturación.
                </p>
                <div class="factu-soon__actions">
                    <button type="button" class="btn btn--primary" disabled>Conectar pasarela</button>
                </div>
            </div>
            <ul class="factu-soon__list">
                <li>
                    <i class="fa-solid fa-link" aria-hidden="true"></i>
                    <div><b>Enlaces de pago por cita</b><small>Se podrán enviar por WhatsApp junto al recordatorio.</small></div>
                </li>
                <li>
                    <i class="fa-solid fa-check" aria-hidden="true"></i>
                    <div><b>Conciliación automática</b><small>El estado "Pagada" se actualizará solo.</small></div>
                </li>
                <li>
                    <i class="fa-solid fa-file-invoice" aria-hidden="true"></i>
                    <div><b>Comprobantes y facturas</b><small>Recibos descargables para cada paciente.</small></div>
                </li>
            </ul>
        </div>
    </div>

    {{-- ================= MODALES ================= --}}
    <div class="modal" id="modal-gasto" data-modal hidden aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="modal-gasto-title">
        <div class="modal__backdrop" data-modal-close></div>
        <div class="modal__dialog" role="document">
            <form method="POST" action="{{ route('dashboard.facturacion.gastos.store') }}">
                @csrf
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-gasto-title">Registrar gasto</h2>
                    <button type="button" class="modal__close" data-modal-close aria-label="Cerrar">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </header>
                <div class="modal__body">
                    <div class="form-field">
                        <label for="gasto-descripcion">Descripción</label>
                        <input type="text" id="gasto-descripcion" name="descripcion" maxlength="150" required placeholder="Ej. Arriendo del consultorio">
                    </div>
                    <div class="factu-modal-grid">
                        <div class="form-field">
                            <label for="gasto-categoria">Categoría (opcional)</label>
                            <input type="text" id="gasto-categoria" name="categoria" maxlength="100" placeholder="Arriendo, software…">
                        </div>
                        <div class="form-field">
                            <label for="gasto-fecha">Fecha</label>
                            <input type="date" id="gasto-fecha" name="fecha" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    <div class="form-field">
                        <label for="gasto-monto">Monto</label>
                        <div class="factu-money">
                            <span class="factu-money__prefix">$</span>
                            <input type="number" id="gasto-monto" name="monto" step="1" min="1" max="9999999" required placeholder="0">
                        </div>
                    </div>
                </div>
                <footer class="modal__footer">
                    <button type="button" class="btn btn--ghost" data-modal-close>Cancelar</button>
                    <button type="submit" class="btn btn--primary">Guardar gasto</button>
                </footer>
            </form>
        </div>
    </div>

    <div class="modal" id="modal-ingreso" data-modal hidden aria-hidden="true" role="dialog" aria-modal="true" aria-labelledby="modal-ingreso-title">
        <div class="modal__backdrop" data-modal-close></div>
        <div class="modal__dialog" role="document">
            <form method="POST" action="{{ route('dashboard.facturacion.ingresos.store') }}">
                @csrf
                <header class="modal__header">
                    <h2 class="modal__title" id="modal-ingreso-title">Registrar ingreso manual</h2>
                    <button type="button" class="modal__close" data-modal-close aria-label="Cerrar">
                        <i class="fa-solid fa-xmark" aria-hidden="true"></i>
                    </button>
                </header>
                <div class="modal__body">
                    <div class="form-field">
                        <label for="ingreso-descripcion">Descripción</label>
                        <input type="text" id="ingreso-descripcion" name="descripcion" maxlength="150" required placeholder="Ej. Taller de manejo del estrés">
                    </div>
                    <div class="factu-modal-grid">
                        <div class="form-field">
                            <label for="ingreso-fecha">Fecha</label>
                            <input type="date" id="ingreso-fecha" name="fecha" value="{{ now()->format('Y-m-d') }}" required>
                        </div>
                        <div class="form-field">
                            <label for="ingreso-monto">Monto</label>
                            <div class="factu-money">
                                <span class="factu-money__prefix">$</span>
                                <input type="number" id="ingreso-monto" name="monto" step="1" min="1" max="9999999" required placeholder="0">
                            </div>
                        </div>
                    </div>
                </div>
                <footer class="modal__footer">
                    <button type="button" class="btn btn--ghost" data-modal-close>Cancelar</button>
                    <button type="submit" class="btn btn--primary">Guardar ingreso</button>
                </footer>
            </form>
        </div>
    </div>

    @push('scripts')
        <script>
            window.FacturacionDatos = {
                serieDiariaCitas: @json($serieDiariaCitas),
                serieMensualCitas: @json($serieMensualCitas),
                citasPagadasMes: @json($citasPagadasResumenMes),
                citasPagadasMesAnterior: @json($citasPagadasResumenMesAnterior),
            };
        </script>
        <script src="{{ asset('js/dashboard/facturacion.js') }}" defer></script>
    @endpush
@endsection
