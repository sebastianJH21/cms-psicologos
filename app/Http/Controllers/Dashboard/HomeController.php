<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Articulo;
use App\Models\Cita;
use App\Models\Disponibilidad;
use App\Models\Faq;
use App\Models\Paciente;
use App\Models\Profile;
use App\Models\Servicio;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $profile = Profile::first();

        $hoyInicio = Carbon::today();
        $hoyFin = Carbon::today()->endOfDay();
        $mesInicio = Carbon::now()->startOfMonth();
        $mesFin = Carbon::now()->endOfMonth();

        $stats = [
            'proximas_citas_hoy' => Cita::query()
                ->whereBetween('fecha_inicio', [Carbon::now(), $hoyFin])
                ->where('estado', '!=', 'cancelada')
                ->count(),
            'pacientes_activos' => Paciente::query()->count(),
            'articulos_publicados' => Articulo::query()->publicados()->count(),
            'sesiones_mes' => Cita::query()
                ->whereBetween('fecha_inicio', [$mesInicio, Carbon::now()])
                ->where('estado', '!=', 'cancelada')
                ->count(),
        ];

        $proximasCitas = Cita::query()
            ->whereBetween('fecha_inicio', [Carbon::now(), $hoyFin])
            ->where('estado', '!=', 'cancelada')
            ->orderBy('fecha_inicio')
            ->limit(5)
            ->get()
            ->map(fn ($cita) => [
                'hora' => $cita->fecha_inicio->format('H:i'),
                'modalidad' => $cita->modalidad,
                'modalidad_label' => $cita->modalidad_label,
                'paciente' => $cita->nombre_provisional,
                'url' => route('dashboard.citas.show', $cita),
            ])
            ->all();

        $disponibilidades = Disponibilidad::query()
            ->where('activa', true)
            ->orderBy('modalidad')
            ->orderBy('hora_inicio')
            ->get()
            ->groupBy('modalidad')
            ->map(fn ($items) => $items->groupBy('dia_semana'));

        $disponibilidadSemanal = [];
        foreach (['presencial', 'online'] as $modalidad) {
            $disponibilidadSemanal[$modalidad] = [];
            $itemsPorDia = $disponibilidades->get($modalidad, collect());
            foreach (Disponibilidad::ORDEN_DIAS as $idx) {
                $items = $itemsPorDia->get($idx, collect());
                $disponibilidadSemanal[$modalidad][] = [
                    'dia' => Disponibilidad::DIAS[$idx],
                    'rango' => $items->isEmpty()
                        ? null
                        : $items->first()->hora_inicio_corta . ' – ' . $items->last()->hora_fin_corta,
                ];
            }
        }

        $pasos = $this->calcularPasos($profile);

        return view('dashboard.home.index', compact('profile', 'stats', 'proximasCitas', 'disponibilidadSemanal', 'pasos'));
    }

    private function calcularPasos(?Profile $profile): array
    {
        $pasos = [
            [
                'label' => 'Instalación inicial completada',
                'icon' => 'fa-check-double',
                'completed' => true,
            ],
            [
                'label' => 'Configurar tu disponibilidad semanal',
                'icon' => 'fa-calendar-days',
                'completed' => Disponibilidad::where('activa', true)->count() > 0,
                'url' => route('dashboard.disponibilidad.edit'),
            ],
            [
                'label' => 'Completar la información pública (slogan, teléfono y email)',
                'icon' => 'fa-circle-info',
                'completed' => $profile && $profile->slogan && $profile->telefono_publico && $profile->email_publico,
                'url' => route('dashboard.configuracion.perfil.edit'),
            ],
            [
                'label' => 'Subir tu foto de perfil pública',
                'icon' => 'fa-camera',
                'completed' => $profile && !empty($profile->foto_path),
                'url' => route('dashboard.configuracion.perfil.edit'),
            ],
            [
                'label' => 'Añadir tus servicios',
                'icon' => 'fa-briefcase',
                'completed' => Servicio::where('activo', true)->count() > 0,
                'url' => route('dashboard.configuracion.servicios.index'),
            ],
            [
                'label' => 'Crear preguntas frecuentes',
                'icon' => 'fa-circle-question',
                'completed' => Faq::where('activa', true)->count() > 0,
                'url' => route('dashboard.faqs.index'),
            ],
            [
                'label' => 'Configurar las notificaciones por email (SMTP)',
                'icon' => 'fa-envelope',
                'completed' => !empty(Setting::get('mail.smtp_host')) && !empty(Setting::get('mail.smtp_user')),
                'url' => route('dashboard.configuracion.email-notificaciones.edit'),
            ],
            [
                'label' => 'Publicar tu primer artículo del blog',
                'icon' => 'fa-newspaper',
                'completed' => Articulo::query()->publicados()->count() > 0,
                'url' => route('dashboard.blog.articulos.index'),
            ],
        ];

        $totalPasos = count($pasos);
        $completados = collect($pasos)->where('completed', true)->count();
        $todoCompletado = $completados === $totalPasos;

        return [
            'items' => $pasos,
            'completados' => $completados,
            'total' => $totalPasos,
            'todo_completado' => $todoCompletado,
            'porcentaje' => (int) round(($completados / max($totalPasos, 1)) * 100),
        ];
    }
}
