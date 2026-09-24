<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;
use App\Http\Requests\Install\BdConfigRequest;
use App\Http\Requests\Install\CuentaRequest;
use App\Http\Requests\Install\DatosPublicosRequest;
use App\Http\Requests\Install\FotoRequest;
use App\Http\Requests\Install\ServiciosRequest;
use App\Http\Requests\Install\TemaRequest;
use App\Models\PlanPrecio;
use App\Models\Profile;
use App\Models\Servicio;
use App\Models\Setting;
use App\Models\Terapia;
use App\Models\User;
use App\Services\ImagenOptimizer;
use App\Services\InstallerService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class WizardController extends Controller
{
    private const SESSION_KEY = 'install_wizard';
    private const TOTAL_STEPS = 6;

    public function __construct(
        private InstallerService $installer,
        private ImagenOptimizer $optimizador
    ) {
    }

    public function index(): RedirectResponse
    {
        $current = $this->currentStep();
        return redirect()->route('install.step.show', ['n' => $current]);
    }

    public function show(int $n): View|RedirectResponse
    {
        if ($n < 1 || $n > self::TOTAL_STEPS) {
            return redirect()->route('install.index');
        }

        $maxAllowed = $this->currentStep();
        if ($n > $maxAllowed) {
            return redirect()->route('install.step.show', ['n' => $maxAllowed]);
        }

        $data = session(self::SESSION_KEY, []);

        return view("install.paso{$n}", [
            'step' => $n,
            'totalSteps' => self::TOTAL_STEPS,
            'data' => $data,
        ]);
    }

    public function process(int $n, Request $request): RedirectResponse
    {
        return match ($n) {
            1 => $this->handleStep1(app(BdConfigRequest::class)),
            2 => $this->handleStep2(app(CuentaRequest::class)),
            3 => $this->handleStep3(app(DatosPublicosRequest::class)),
            4 => $this->handleStep4(app(ServiciosRequest::class)),
            5 => $this->handleStep5(app(TemaRequest::class)),
            6 => $this->handleStep6(app(FotoRequest::class)),
            default => redirect()->route('install.index'),
        };
    }

    public function testDb(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'db_host' => ['required', 'string'],
            'db_port' => ['required', 'integer'],
            'db_username' => ['required', 'string'],
            'db_password' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return response()->json(['ok' => false, 'error' => 'Datos incompletos.'], 422);
        }

        $result = $this->installer->testConnection(
            $request->input('db_host'),
            (int) $request->input('db_port'),
            $request->input('db_username'),
            $request->input('db_password', '') ?? ''
        );

        return response()->json($result);
    }

    public function completed(): View|RedirectResponse
    {
        if (!$this->installer->isInstalled()) {
            return redirect()->route('install.index');
        }

        return view('install.completado');
    }

    private function handleStep1(BdConfigRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $test = $this->installer->testConnection(
            $data['db_host'],
            (int) $data['db_port'],
            $data['db_username'],
            $data['db_password'] ?? ''
        );

        if (!$test['ok']) {
            return back()->withInput()->withErrors(['db_host' => 'No se pudo conectar al servidor MySQL: ' . $test['error']]);
        }

        $create = $this->installer->createDatabaseIfNotExists(
            $data['db_host'],
            (int) $data['db_port'],
            $data['db_username'],
            $data['db_password'] ?? '',
            $data['db_database']
        );

        if (!$create['ok']) {
            return back()->withInput()->withErrors(['db_database' => 'No se pudo crear la base de datos: ' . $create['error']]);
        }

        $this->installer->applyDbConfig(
            $data['db_host'],
            (int) $data['db_port'],
            $data['db_database'],
            $data['db_username'],
            $data['db_password'] ?? ''
        );

        $migrate = $this->installer->runMigrations();
        if (!$migrate['ok']) {
            return back()->withInput()->withErrors(['db_database' => 'Error al ejecutar las migraciones: ' . $migrate['error']]);
        }

        $this->installer->writeEnv([
            'DB_HOST' => $data['db_host'],
            'DB_PORT' => (string) $data['db_port'],
            'DB_DATABASE' => $data['db_database'],
            'DB_USERNAME' => $data['db_username'],
            'DB_PASSWORD' => $data['db_password'] ?? '',
        ]);

        $this->mergeSession(['step1_done' => true, 'db_database' => $data['db_database']]);
        $this->setMaxStep(2);

        return redirect()->route('install.step.show', ['n' => 2])->with('success', 'Base de datos creada y migraciones ejecutadas correctamente.');
    }

    private function handleStep2(CuentaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if (User::where('email', $data['email'])->exists() || User::where('telefono', $data['telefono'])->exists()) {
            return back()->withInput()->withErrors(['email' => 'Ya existe una cuenta registrada. Restablece la instalación si quieres empezar de cero.']);
        }

        $user = User::create([
            'nombre' => $data['nombre'],
            'apellidos' => $data['apellidos'],
            'email' => $data['email'],
            'telefono' => $data['telefono'],
            'password' => Hash::make($data['password']),
        ]);

        $this->mergeSession(['user_id' => $user->id, 'step2_done' => true]);
        $this->setMaxStep(3);

        return redirect()->route('install.step.show', ['n' => 3])->with('success', 'Cuenta de acceso creada.');
    }

    private function handleStep3(DatosPublicosRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $profile = Profile::singleton();
        $profile->fill([
            'slogan' => $data['slogan'] ?? null,
            'telefono_publico' => $data['telefono_publico'],
            'email_publico' => $data['email_publico'],
            'numero_colegiado' => $data['numero_colegiado'] ?? null,
            'sobre_mi' => $data['sobre_mi'] ?? null,
            'direccion' => $data['direccion'] ?? null,
        ])->save();

        $this->mergeSession(['step3_done' => true]);
        $this->setMaxStep(4);

        return redirect()->route('install.step.show', ['n' => 4])->with('success', 'Datos públicos guardados.');
    }

    private function handleStep4(ServiciosRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Servicio::query()->delete();
        $orden = 0;
        foreach ($data['servicios'] ?? [] as $item) {
            $titulo = trim($item['titulo'] ?? '');
            if ($titulo === '') {
                continue;
            }
            Servicio::create([
                'titulo' => $titulo,
                'descripcion' => $item['descripcion'] ?? null,
                'orden' => $orden++,
                'activo' => true,
            ]);
        }

        Terapia::query()->delete();
        $orden = 0;
        foreach ($data['terapias'] ?? [] as $item) {
            $titulo = trim($item['titulo'] ?? '');
            if ($titulo === '') {
                continue;
            }
            Terapia::create([
                'titulo' => $titulo,
                'descripcion' => $item['descripcion'] ?? null,
                'orden' => $orden++,
                'activo' => true,
            ]);
        }

        PlanPrecio::query()->delete();
        $orden = 0;
        foreach ($data['planes'] ?? [] as $item) {
            $nombre = trim($item['nombre'] ?? '');
            if ($nombre === '' || !isset($item['precio']) || !isset($item['tipo'])) {
                continue;
            }
            PlanPrecio::create([
                'tipo' => $item['tipo'],
                'nombre' => $nombre,
                'descripcion' => $item['descripcion'] ?? null,
                'precio' => $item['precio'],
                'duracion_min' => $item['duracion_min'] ?? 60,
                'orden' => $orden++,
            ]);
        }

        $this->mergeSession(['step4_done' => true]);
        $this->setMaxStep(5);

        return redirect()->route('install.step.show', ['n' => 5])->with('success', 'Servicios, especialidades y planes guardados.');
    }

    private function handleStep5(TemaRequest $request): RedirectResponse
    {
        $data = $request->validated();

        Setting::set('active_theme', $data['tema'], 'theme');
        Setting::set('theme_mode', $data['modo'], 'theme');

        $this->mergeSession(['step5_done' => true, 'tema' => $data['tema']]);
        $this->setMaxStep(6);

        return redirect()->route('install.step.show', ['n' => 6])->with('success', 'Tema visual seleccionado.');
    }

    private function handleStep6(FotoRequest $request): RedirectResponse
    {
        $data = $request->validated();

        if ($request->hasFile('foto')) {
            $path = $this->optimizador->procesarYGuardar(
                $request->file('foto'),
                'profile',
                1200,
                82
            );
            $profile = Profile::singleton();
            $profile->foto_path = $path;
            $profile->save();
        }

        Setting::set('features.blog_enabled', true, 'features');
        Setting::set('features.reservas_enabled', true, 'features');
        Setting::set('features.faq_enabled', true, 'features');
        Setting::set('features.servicios_enabled', true, 'features');
        Setting::set('features.sobre_mi_enabled', true, 'features');

        $this->installer->markInstalled();

        try {
            $linkPath = public_path('storage');
            if (is_link($linkPath)) {
                // Symlink existente (posiblemente roto al copiar de local): recrear.
                @unlink($linkPath);
            } elseif (is_dir($linkPath) && count(scandir($linkPath)) <= 2) {
                // Carpeta vacía subida por FTP en lugar del symlink real.
                @rmdir($linkPath);
            }
            Artisan::call('storage:link', ['--force' => true]);
        } catch (\Throwable) {
            // El enlace ya existe con datos o el sistema no lo permite — se continúa igualmente
        }

        try {
            // Limpiar (no cachear) la config: durante esta misma petición Dotenv
            // no recargaría las credenciales recién escritas en el .env, así que
            // un config:cache aquí guardaría valores erróneos. Limpiando, la
            // siguiente petición arranca leyendo el .env correcto. Esto borra
            // además cualquier caché obsoleta copiada del entorno local.
            Artisan::call('optimize:clear');
        } catch (\Throwable) {
            // No crítico si falla en algún entorno
        }

        $userId = session(self::SESSION_KEY . '.user_id');
        if ($userId) {
            $user = User::find($userId);
            if ($user) {
                Auth::login($user, true);
            }
        }

        session()->forget(self::SESSION_KEY);

        return redirect()->route('install.completed');
    }

    private function currentStep(): int
    {
        return (int) session(self::SESSION_KEY . '.max_step', 1);
    }

    private function setMaxStep(int $step): void
    {
        $current = $this->currentStep();
        if ($step > $current) {
            $this->mergeSession(['max_step' => $step]);
        }
    }

    private function mergeSession(array $values): void
    {
        $existing = session(self::SESSION_KEY, []);
        session([self::SESSION_KEY => array_merge($existing, $values)]);
    }
}
