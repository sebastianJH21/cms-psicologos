<div class="dispo-grid__bulk">
    <button type="button" class="btn btn--ghost btn--sm" data-dispo-bulk="all">Marcar todos</button>
    <button type="button" class="btn btn--ghost btn--sm" data-dispo-bulk="weekdays">L-V completos</button>
    <button type="button" class="btn btn--ghost btn--sm" data-dispo-bulk="clear">Limpiar</button>
</div>

<div class="dispo-grid__scroll">
    <table class="dispo-grid__table">
        <thead>
            <tr>
                <th scope="col">Hora</th>
                @foreach ($ordenDias as $diaIdx)
                    <th scope="col">{{ $dias[$diaIdx] }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @foreach ($slotsTemplate as $slot)
                <tr>
                    <th scope="row" class="dispo-grid__hora">{{ $slot['inicio'] }}</th>
                    @foreach ($ordenDias as $diaIdx)
                        @php
                            $key = $modalidad . '|' . $diaIdx . '|' . $slot['inicio'];
                            $marcado = in_array($key, old('slots', $seleccion), true);
                        @endphp
                        <td class="dispo-grid__cell">
                            <label class="dispo-slot {{ $marcado ? 'dispo-slot--on' : '' }}">
                                <input type="checkbox" name="slots[]" value="{{ $key }}" {{ $marcado ? 'checked' : '' }}>
                                <span class="dispo-slot__dot" aria-hidden="true"></span>
                                <span class="visually-hidden">{{ $dias[$diaIdx] }} {{ $slot['inicio'] }}</span>
                            </label>
                        </td>
                    @endforeach
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
