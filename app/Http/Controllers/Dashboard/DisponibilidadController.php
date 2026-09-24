<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Requests\Dashboard\DisponibilidadRequest;
use App\Models\Disponibilidad;
use App\Models\PeriodoVacaciones;
use App\Models\Setting;
use App\Services\CitaService;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class DisponibilidadController extends Controller
{
    public function __construct(private CitaService $citaService)
    {
    }

    public function edit()
    {
        $datos = $this->citaService->disponibilidadesAgrupadas();

        return view('dashboard.disponibilidad.edit', [
            'duracionPresencial'         => $datos['duracion_presencial'],
            'duracionOnline'             => $datos['duracion_online'],
            'horaAperturaManana'         => $datos['hora_apertura_manana'],
            'horaCierreManana'           => $datos['hora_cierre_manana'],
            'horaAperturaTarde'          => $datos['hora_apertura_tarde'],
            'horaCierreTarde'            => $datos['hora_cierre_tarde'],
            'slotsPresencialManana'      => $datos['slots_presencial_manana'],
            'slotsOnlineManana'          => $datos['slots_online_manana'],
            'slotsPresencialTarde'       => $datos['slots_presencial_tarde'],
            'slotsOnlineTarde'           => $datos['slots_online_tarde'],
            'dias'                       => $datos['dias'],
            'ordenDias'                  => $datos['orden_dias'],
            'seleccion'                  => $datos['seleccion'],
            'modoVacaciones'             => $this->citaService->modoVacaciones(),
            'mensajeVacaciones'          => Setting::get('disponibilidad.mensaje_vacaciones', ''),
            'descansoActivoPresencial'   => $this->citaService->descansoActivo('presencial'),
            'descansoMinPresencial'      => $this->citaService->descansoMin('presencial'),
            'descansoActivoOnline'       => $this->citaService->descansoActivo('online'),
            'descansoMinOnline'          => $this->citaService->descansoMin('online'),
            'diasAdelante'               => (int) Setting::get('disponibilidad.dias_adelante', 60),
            'periodos'                   => PeriodoVacaciones::orderBy('fecha_inicio')->get(),
        ]);
    }

    public function update(DisponibilidadRequest $request)
    {
        $duracionPresencial    = (int) $request->input('duracion_sesion_presencial_min');
        $duracionOnline        = (int) $request->input('duracion_sesion_online_min');
        $aperturaManana        = $request->input('hora_apertura_manana') . ':00';
        $cierreManana          = $request->input('hora_cierre_manana') . ':00';
        $aperturaTarde         = $request->input('hora_apertura_tarde') . ':00';
        $cierreTarde           = $request->input('hora_cierre_tarde') . ':00';
        $slotsSeleccionados    = $request->input('slots', []);
        $modoVacaciones        = (bool) $request->input('modo_vacaciones', false);
        $mensajeVacaciones     = (string) $request->input('mensaje_vacaciones', '');
        $descansoActivoPresencial = (bool) $request->input('descanso_activo_presencial', false);
        $descansoMinPresencial = max(0, (int) $request->input('descanso_min_presencial', 0));
        $descansoActivoOnline  = (bool) $request->input('descanso_activo_online', false);
        $descansoMinOnline     = max(0, (int) $request->input('descanso_min_online', 0));
        $diasAdelante          = max(7, (int) $request->input('dias_adelante', 60));

        DB::transaction(function () use ($duracionPresencial, $duracionOnline, $aperturaManana, $cierreManana, $aperturaTarde, $cierreTarde, $slotsSeleccionados, $modoVacaciones, $mensajeVacaciones, $descansoActivoPresencial, $descansoMinPresencial, $descansoActivoOnline, $descansoMinOnline, $diasAdelante) {
            Setting::set('disponibilidad.duracion_sesion_presencial_min', $duracionPresencial, 'disponibilidad');
            Setting::set('disponibilidad.duracion_sesion_online_min', $duracionOnline, 'disponibilidad');
            Setting::set('disponibilidad.hora_apertura_manana', $aperturaManana, 'disponibilidad');
            Setting::set('disponibilidad.hora_cierre_manana', $cierreManana, 'disponibilidad');
            Setting::set('disponibilidad.hora_apertura_tarde', $aperturaTarde, 'disponibilidad');
            Setting::set('disponibilidad.hora_cierre_tarde', $cierreTarde, 'disponibilidad');
            Setting::set('disponibilidad.modo_vacaciones', $modoVacaciones, 'disponibilidad');
            Setting::set('disponibilidad.mensaje_vacaciones', $mensajeVacaciones, 'disponibilidad');
            Setting::set('disponibilidad.descanso_activo_presencial', $descansoActivoPresencial, 'disponibilidad');
            Setting::set('disponibilidad.descanso_min_presencial', $descansoMinPresencial, 'disponibilidad');
            Setting::set('disponibilidad.descanso_activo_online', $descansoActivoOnline, 'disponibilidad');
            Setting::set('disponibilidad.descanso_min_online', $descansoMinOnline, 'disponibilidad');
            Setting::set('disponibilidad.dias_adelante', $diasAdelante, 'disponibilidad');

            Disponibilidad::query()->delete();

            foreach ($slotsSeleccionados as $slot) {
                $partes = explode('|', $slot);
                if (count($partes) !== 3) {
                    continue;
                }

                [$modalidad, $diaSemana, $horaInicio] = $partes;

                if (!in_array($modalidad, ['online', 'presencial'], true)) {
                    continue;
                }

                $diaSemana = (int) $diaSemana;
                if ($diaSemana < 0 || $diaSemana > 6) {
                    continue;
                }

                if (!preg_match('/^\d{2}:\d{2}$/', $horaInicio)) {
                    continue;
                }

                $durMinSlot = $modalidad === 'presencial' ? $duracionPresencial : $duracionOnline;
                $horaFin = Carbon::createFromFormat('H:i', $horaInicio)->addMinutes($durMinSlot)->format('H:i:s');

                Disponibilidad::create([
                    'modalidad' => $modalidad,
                    'dia_semana' => $diaSemana,
                    'hora_inicio' => $horaInicio . ':00',
                    'hora_fin' => $horaFin,
                    'activa' => true,
                ]);
            }
        });

        return redirect()
            ->route('dashboard.disponibilidad.edit')
            ->with('success', 'Disponibilidad actualizada correctamente.');
    }
}
