@if ($citas->count() === 0)
    <div class="empty-state">
        <i class="fa-regular fa-calendar empty-state__icon" aria-hidden="true"></i>
        <p class="empty-state__title">No hay citas que coincidan</p>
        <p class="empty-state__hint">Cuando crees una cita o un paciente reserve online, aparecerá aquí.</p>
        <a href="{{ route('dashboard.citas.create') }}" class="btn btn--primary">
            <i class="fa-solid fa-plus" aria-hidden="true"></i>
            Crear la primera cita
        </a>
    </div>
@else
    <div class="data-table__wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th scope="col">Fecha y hora</th>
                    <th scope="col">Paciente</th>
                    <th scope="col">Teléfono</th>
                    <th scope="col">Modalidad</th>
                    <th scope="col">Estado</th>
                    <th scope="col" class="data-table__actions">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($citas as $cita)
                    <tr>
                        <td>
                            <a href="{{ route('dashboard.citas.show', $cita) }}" class="data-cell-link">
                                <strong>{{ $cita->fecha_inicio->format('d/m/Y') }}</strong>
                                <small class="data-table__sub">{{ $cita->fecha_inicio->format('H:i') }} – {{ $cita->fecha_fin->format('H:i') }}</small>
                            </a>
                        </td>
                        <td><a href="{{ route('dashboard.citas.show', $cita) }}" class="data-cell-link">{{ $cita->nombre_provisional }}</a></td>
                        <td>
                            @php
                                $telCita = $cita->paciente_telefono;
                                $telCitaLimpio = preg_replace('/[^+0-9]/', '', (string) $telCita);
                                $waCitaRow = whatsapp_confirmacion_url($cita);
                            @endphp
                            <div class="cita-tel-cell">
                                @if ($telCita)
                                    <a href="tel:{{ $telCitaLimpio }}" class="cita-badge cita-badge--tel" title="Llamar al paciente">
                                        <i class="fa-solid fa-phone" aria-hidden="true"></i> {{ $telCita }}
                                    </a>
                                @else
                                    <span class="cita-badge cita-badge--muted">—</span>
                                @endif
                                @if ($waCitaRow)
                                    <a href="{{ $waCitaRow }}" target="_blank" rel="noopener" class="cita-badge cita-badge--wa" title="Enviar mensaje de confirmación por WhatsApp">
                                        <i class="fa-brands fa-whatsapp" aria-hidden="true"></i> Mensaje confirmación
                                    </a>
                                @endif
                            </div>
                        </td>
                        <td>
                            <span class="badge badge--{{ $cita->modalidad === 'online' ? 'info' : 'warning' }}">
                                {{ $cita->modalidad_label }}
                            </span>
                        </td>
                        <td>
                            @include('dashboard.citas.partials.estado-badge', ['cita' => $cita, 'pair' => ['pendiente', 'confirmada']])
                        </td>
                        <td class="data-table__actions">
                            <a href="{{ route('dashboard.citas.show', $cita) }}" class="btn btn--icon" aria-label="Ver detalle">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </a>
                            <a href="{{ route('dashboard.citas.edit', $cita) }}" class="btn btn--icon" aria-label="Editar">
                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                            </a>
                            <form method="POST" action="{{ route('dashboard.citas.destroy', $cita) }}"
                                data-confirm="¿Cancelar esta cita? La cita se elminirá definitivamente."
                                data-confirm-title="Cancelar cita"
                                data-confirm-label="Sí, cancelar"
                                data-confirm-style="danger">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn--icon btn--icon-danger" aria-label="Cancelar">
                                    <i class="fa-solid fa-ban" aria-hidden="true"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    {{ $citas->links() }}
@endif
