<nav class="progress" aria-label="Progreso de instalación">
    <ol class="progress__list">
        @php
            $labels = [
                1 => 'Base de datos',
                2 => 'Cuenta',
                3 => 'Datos públicos',
                4 => 'Servicios',
                5 => 'Tema visual',
                6 => 'Foto',
            ];
        @endphp
        @foreach ($labels as $n => $label)
            <li class="progress__item @if (isset($allCompleted) && $allCompleted) progress__item--active @elseif ($n === $step) progress__item--active @elseif ($n < $step) progress__item--done @endif">
                <span class="progress__num">{{ $n }}</span>
                <span class="progress__label">{{ $label }}</span>
            </li>
        @endforeach
    </ol>
</nav>
