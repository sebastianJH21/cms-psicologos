@if ($historias->isEmpty())
    <div class="empty-state panel">
        <i class="fa-solid fa-folder-open empty-state__icon" aria-hidden="true"></i>
        <p class="empty-state__title">No hay entradas de historia</p>
        <p class="empty-state__hint">
            {{ request('q') ? 'No se encontraron resultados para tu búsqueda.' : 'Las entradas de historia aparecerán aquí cuando crees registros de sesiones desde la ficha de cada paciente.' }}
        </p>
    </div>
@else
    <div class="panel">
        <div class="data-table__wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th scope="col">Paciente</th>
                        <th scope="col">Fecha sesión</th>
                        <th scope="col">Título / Sesión</th>
                        <th scope="col" class="data-table__actions">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($historias as $historia)
                        @if (!$historia->paciente) @continue @endif
                        <tr>
                            <td>
                                <a href="{{ route('dashboard.pacientes.show', $historia->paciente) }}">
                                    {{ $historia->paciente->nombre_completo }}
                                </a>
                                <small class="data-table__sub">{{ $historia->paciente->telefono }}</small>
                            </td>
                            <td>
                                <strong>{{ $historia->fecha_sesion->format('d/m/Y') }}</strong>
                                <small class="data-table__sub">{{ $historia->fecha_sesion->translatedFormat('l') }}</small>
                            </td>
                            <td>{{ $historia->titulo_mostrado }}</td>
                            <td class="data-table__actions">
                                <a href="{{ route('dashboard.pacientes.historias.show', [$historia->paciente, $historia]) }}"
                                    class="btn btn--icon" aria-label="Ver entrada">
                                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                </a>
                                <a href="{{ route('dashboard.pacientes.historias.edit', [$historia->paciente, $historia]) }}"
                                    class="btn btn--icon" aria-label="Editar">
                                    <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        {{ $historias->links() }}
    </div>
@endif
