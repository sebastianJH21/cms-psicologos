@if ($pacientes->count() === 0)
    <div class="empty-state">
        <i class="fa-regular fa-user empty-state__icon" aria-hidden="true"></i>
        <p class="empty-state__title">{{ $papelera ? 'La papelera está vacía' : 'No se encontraron pacientes' }}</p>
        <p class="empty-state__hint">
            {{ $papelera ? 'Cuando elimines pacientes aparecerán aquí y podrás restaurarlos.' : 'Prueba con otros filtros de búsqueda o crea un nuevo paciente.' }}
        </p>
        @if (!$papelera)
            <a href="{{ route('dashboard.pacientes.create') }}" class="btn btn--primary">
                <i class="fa-solid fa-plus" aria-hidden="true"></i>
                Añadir primer paciente
            </a>
        @endif
    </div>
@else
    <div class="data-table__wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th scope="col">Paciente</th>
                    <th scope="col">Edad</th>
                    <th scope="col">Teléfono</th>
                    <th scope="col">Email</th>
                    <th scope="col">Citas</th>
                    <th scope="col">Origen</th>
                    <th scope="col" class="data-table__actions">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($pacientes as $paciente)
                    <tr>
                        <td>
                            @if ($papelera)
                                <strong>{{ $paciente->nombre_completo }}</strong>
                            @else
                                <a href="{{ route('dashboard.pacientes.show', $paciente) }}" class="data-cell-link">
                                    <strong>{{ $paciente->nombre_completo }}</strong>
                                </a>
                            @endif
                        </td>
                        <td>
                            @if ($paciente->edad)
                                {{ $paciente->edad }} años
                            @else
                                <span class="data-table__sub">—</span>
                            @endif
                        </td>
                        <td>{{ $paciente->telefono }}</td>
                        <td>{{ $paciente->email ?: '—' }}</td>
                        <td>
                            <span class="badge badge--info">{{ $paciente->citas_total ?? 0 }}</span>
                        </td>
                        <td>
                            <span class="badge badge--{{ $paciente->origen === 'publica' ? 'success' : 'warning' }}">
                                {{ $paciente->origen === 'publica' ? 'Pública' : 'Manual' }}
                            </span>
                        </td>
                        <td class="data-table__actions">
                            @if ($papelera)
                                <form method="POST" action="{{ route('dashboard.pacientes.restore', $paciente->id) }}"
                                    data-confirm="¿Restaurar este paciente?"
                                    data-confirm-title="Restaurar paciente"
                                    data-confirm-label="Sí, restaurar">
                                    @csrf
                                    <button type="submit" class="btn btn--icon" aria-label="Restaurar">
                                        <i class="fa-solid fa-rotate-left" aria-hidden="true"></i>
                                    </button>
                                </form>
                            @else
                                <a href="{{ route('dashboard.pacientes.show', $paciente) }}" class="btn btn--icon" aria-label="Ver">
                                    <i class="fa-solid fa-eye" aria-hidden="true"></i>
                                </a>
                                <a href="{{ route('dashboard.pacientes.edit', $paciente) }}" class="btn btn--icon" aria-label="Editar">
                                    <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                </a>
                                <form method="POST" action="{{ route('dashboard.pacientes.destroy', $paciente) }}"
                                    data-confirm="¿Mover este paciente a la papelera? Sus citas se conservarán."
                                    data-confirm-title="Eliminar paciente"
                                    data-confirm-label="Sí, eliminar"
                                    data-confirm-style="danger">
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

    {{ $pacientes->links() }}
@endif
