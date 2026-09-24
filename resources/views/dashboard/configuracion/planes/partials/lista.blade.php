<div class="data-table__wrapper">
    <table class="data-table">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Descripción</th>
                <th>Duración</th>
                <th>Precio</th>
                <th class="data-table__actions">Acciones</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($planes as $plan)
                <tr>
                    <td><strong>{{ $plan->nombre }}</strong></td>
                    <td>{{ Str::limit($plan->descripcion ?? '—', 80) }}</td>
                    <td>{{ $plan->duracion_min }} min</td>
                    <td><strong>{{ number_format($plan->precio, 2, ',', '.') }} €</strong></td>
                    <td class="data-table__actions">
                        <a href="{{ route('dashboard.configuracion.planes.edit', $plan) }}"
                            class="btn btn--icon" aria-label="Editar">
                            <i class="fa-solid fa-pen" aria-hidden="true"></i>
                        </a>
                        <form method="POST" action="{{ route('dashboard.configuracion.planes.destroy', $plan) }}"
                            data-confirm="¿Eliminar este plan?"
                            data-ajax-delete="true"
                            data-delete-target="tr"
                            style="display:contents">
                            @csrf @method('DELETE')
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
