<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\Public\ReservaRequest;
use App\Mail\NuevaCitaMail;
use App\Models\Disponibilidad;
use App\Models\Profile;
use App\Models\Setting;
use App\Models\User;
use App\Services\ReservaService;
use App\Services\ThemeManager;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailer as LaravelMailer;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport;

class CitaController extends Controller
{
    public function index(ThemeManager $themes)
    {
        if ($themes->activeMode() !== 'multipage') {
            return redirect('/#cita');
        }

        if (!Setting::get('features.reservas_enabled', true)) {
            abort(404);
        }

        return view('theme::multipage.cita', $this->datosVista());
    }

    public function slots(Request $request, ReservaService $reservas): JsonResponse
    {
        $modalidad = $request->input('modalidad', 'online');
        $fecha = $request->input('fecha');

        if (!in_array($modalidad, ['online', 'presencial'], true)) {
            return response()->json(['slots' => []]);
        }

        return response()->json([
            'slots' => $reservas->slotsDisponibles($modalidad, (string) $fecha),
        ]);
    }

    public function diasDisponibles(Request $request, ReservaService $reservas): JsonResponse
    {
        $modalidad = $request->input('modalidad', 'online');
        if (!in_array($modalidad, ['online', 'presencial'], true)) {
            return response()->json(['dias' => []]);
        }

        return response()->json([
            'dias' => $reservas->diasDisponibles($modalidad),
        ]);
    }

    public function reservar(ReservaRequest $request, ReservaService $reservas): JsonResponse
    {
        if (!Setting::get('features.reservas_enabled', true)) {
            return response()->json(['ok' => false, 'message' => 'Las reservas no están disponibles.'], 404);
        }

        if ($reservas->modoVacaciones()) {
            return response()->json(['ok' => false, 'message' => 'En modo vacaciones. No se pueden reservar citas.'], 422);
        }

        if (!app()->environment('local')) {
            $clave = 'reserva:' . $request->ip();
            if (RateLimiter::tooManyAttempts($clave, 3)) {
                $segundos = RateLimiter::availableIn($clave);
                return response()->json([
                    'ok' => false,
                    'message' => "Demasiados intentos. Inténtalo de nuevo en {$segundos} segundos.",
                ], 429);
            }
            RateLimiter::hit($clave, 3600);
        }

        try {
            $cita = $reservas->registrar($request->validated());
        } catch (\RuntimeException $e) {
            return response()->json(['ok' => false, 'message' => $e->getMessage()], 422);
        }

        $this->enviarNotificacion($cita);

        $user = User::first();
        $profile = Profile::first();
        $psicologa = trim(($user?->nombre ?? '') . ' ' . ($user?->apellidos ?? '')) ?: 'la psicóloga';

        return response()->json([
            'ok' => true,
            'message' => 'Cita reservada correctamente.',
            'cita' => [
                'fecha' => $cita->fecha_inicio->format('d/m/Y'),
                'hora' => $cita->fecha_inicio->format('H:i'),
                'fin' => $cita->fecha_fin->format('H:i'),
                'modalidad' => $cita->modalidad,
                'motivo' => $cita->motivo,
            ],
            'google_calendar' => $reservas->googleCalendarLink($cita, $psicologa, $profile?->direccion),
        ]);
    }

    private function enviarNotificacion($cita): void
    {
        if (!Setting::get('mail.notif_enabled', false)) {
            return;
        }

        $host = Setting::get('mail.smtp_host');
        $port = (int) Setting::get('mail.smtp_port', 587);
        $user = Setting::get('mail.smtp_user');
        $passwordEnc = Setting::get('mail.smtp_password');
        $from = Setting::get('mail.from_address') ?: $user;

        if (!$host || !$user || !$passwordEnc || !$from) {
            return;
        }

        try {
            $password = Crypt::decryptString($passwordEnc);

            $useSsl = $port === 465;
            $transport = new EsmtpTransport($host, $port, $useSsl);
            $transport->setUsername($user);
            $transport->setPassword($password);

            if (app()->environment('local')) {
                $stream = $transport->getStream();
                $stream->setStreamOptions([
                    'ssl' => [
                        'verify_peer'       => false,
                        'verify_peer_name'  => false,
                        'allow_self_signed' => true,
                    ],
                ]);
            }

            $mailer = new LaravelMailer('smtp_runtime', app('view'), $transport, app('events'));
            $mailer->alwaysFrom($from, 'PsicoCMS');

            if ($from) {
                $destinatario = $from;
                if (str_contains($from, '@') && !str_contains(explode('@', $from, 2)[0], '+')) {
                    [$local, $dominio] = explode('@', $from, 2);
                    $destinatario = $local . '+notificaciones@' . $dominio;
                }
                $mailer->to($destinatario)->send(new NuevaCitaMail($cita));
            }
        } catch (\Throwable $e) {
            Log::warning('Fallo al enviar notificación de cita: ' . $e->getMessage());
        }
    }

    private function datosVista(): array
    {
        $disponibilidades = Disponibilidad::where('activa', true)->get();

        return [
            'modoVacaciones' => (bool) Setting::get('disponibilidad.modo_vacaciones', false),
            'mensajeVacaciones' => (string) Setting::get('disponibilidad.mensaje_vacaciones', 'Estoy de vacaciones temporalmente. Vuelvo pronto.'),
            'tieneOnline' => $disponibilidades->where('modalidad', 'online')->isNotEmpty(),
            'tienePresencial' => $disponibilidades->where('modalidad', 'presencial')->isNotEmpty(),
        ];
    }
}
