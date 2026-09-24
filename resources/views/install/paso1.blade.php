@extends('install.layout')

@section('paso')
    <section class="step">
        <header class="step__head">
            <h2 class="step__title">Paso 1 · Conexión a la base de datos</h2>
            <p class="step__desc">Introduce los datos de tu servidor MySQL. PsicoCMS creará automáticamente la base de datos si no existe.</p>
        </header>

        <form method="POST" action="{{ route('install.step.process', ['n' => 1]) }}" class="form" novalidate>
            @csrf

            <div class="grid grid--2">
                <div class="field">
                    <label for="db_host">Servidor (host)</label>
                    <input type="text" id="db_host" name="db_host" value="{{ old('db_host', '127.0.0.1') }}" required autocomplete="off">
                </div>

                <div class="field">
                    <label for="db_port">Puerto</label>
                    <input type="number" id="db_port" name="db_port" value="{{ old('db_port', 3306) }}" min="1" max="65535" required>
                </div>

                <div class="field">
                    <label for="db_database">Nombre de la base de datos</label>
                    <input type="text" id="db_database" name="db_database" value="{{ old('db_database', 'psicocms') }}" required autocomplete="off">
                </div>

                <div class="field">
                    <label for="db_username">Usuario</label>
                    <input type="text" id="db_username" name="db_username" value="{{ old('db_username', 'root') }}" required autocomplete="off">
                </div>

                <div class="field field--full">
                    <label for="db_password">Contraseña</label>
                    <input type="password" id="db_password" name="db_password" value="{{ old('db_password') }}" autocomplete="off">
                    <small class="field__hint">Si tu MySQL local no tiene contraseña, deja este campo vacío.</small>
                </div>
            </div>

            <div class="actions">
                <button type="button" class="btn btn--ghost" id="test-db-btn">Probar conexión</button>
                <span class="test-result" id="test-db-result" aria-live="polite"></span>
                <button type="submit" class="btn btn--primary">Crear base de datos y continuar</button>
            </div>
        </form>
    </section>

    @push('scripts')
        <script>
            window.PSICOCMS_INSTALL = {
                testDbUrl: @json(route('install.test-db')),
                csrfToken: @json(csrf_token())
            };
        </script>
    @endpush
@endsection
