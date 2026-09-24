@if ($articulos->count() === 0)
    <div class="empty-state">
        <i class="fa-regular fa-newspaper empty-state__icon" aria-hidden="true"></i>
        <p class="empty-state__title">No hay artículos</p>
        <p class="empty-state__hint">Empieza escribiendo tu primer artículo del blog.</p>
        <a href="{{ route('dashboard.blog.articulos.create') }}" class="btn btn--primary">
            <i class="fa-solid fa-plus" aria-hidden="true"></i>
            Crear primer artículo
        </a>
    </div>
@else
    <div class="data-table__wrapper">
        <table class="data-table">
            <thead>
                <tr>
                    <th scope="col">Título</th>
                    <th scope="col">Categoría</th>
                    <th scope="col">Estado</th>
                    <th scope="col">Publicado</th>
                    <th scope="col" class="data-table__actions">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($articulos as $articulo)
                    <tr>
                        <td>
                            <a href="{{ route('dashboard.blog.articulos.show', $articulo) }}" class="data-cell-link">
                                <strong>{{ $articulo->titulo }}</strong>
                                @if ($articulo->extracto)
                                    <small class="data-table__sub">{{ \Illuminate\Support\Str::limit($articulo->extracto, 80) }}</small>
                                @endif
                            </a>
                        </td>
                        <td>
                            @if ($articulo->categoria)
                                <span class="badge badge--info">{{ $articulo->categoria->nombre }}</span>
                            @else
                                <span class="data-table__sub">Sin categoría</span>
                            @endif
                        </td>
                        <td>
                            @php
                                $estadoClase = match ($articulo->estado) {
                                    'publicado' => 'success',
                                    'archivado' => 'warning',
                                    default => 'info',
                                };
                            @endphp
                            <span class="badge badge--{{ $estadoClase }}">{{ $articulo->estado_label }}</span>
                        </td>
                        <td>
                            @if ($articulo->published_at)
                                {{ $articulo->published_at->format('d/m/Y') }}
                                <small class="data-table__sub">{{ $articulo->published_at->format('H:i') }}</small>
                            @else
                                <span class="data-table__sub">—</span>
                            @endif
                        </td>
                        <td class="data-table__actions">
                            <a href="{{ route('dashboard.blog.articulos.show', $articulo) }}" class="btn btn--icon" aria-label="Ver">
                                <i class="fa-solid fa-eye" aria-hidden="true"></i>
                            </a>
                            <a href="{{ route('dashboard.blog.articulos.edit', $articulo) }}" class="btn btn--icon" aria-label="Editar">
                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                            </a>
                            <form method="POST" action="{{ route('dashboard.blog.articulos.destroy', $articulo) }}"
                                data-confirm="¿Mover este artículo a la papelera?"
                                data-confirm-title="Eliminar artículo"
                                data-confirm-label="Sí, eliminar"
                                data-confirm-style="danger">
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

    {{ $articulos->links() }}
@endif
