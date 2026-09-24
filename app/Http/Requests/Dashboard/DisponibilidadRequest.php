<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class DisponibilidadRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'duracion_sesion_presencial_min' => ['required', 'integer', 'min:15', 'max:240'],
            'duracion_sesion_online_min'     => ['required', 'integer', 'min:15', 'max:240'],
            'hora_apertura_manana'      => ['required', 'date_format:H:i'],
            'hora_cierre_manana'        => ['required', 'date_format:H:i', 'after:hora_apertura_manana'],
            'hora_apertura_tarde'       => ['required', 'date_format:H:i'],
            'hora_cierre_tarde'         => ['required', 'date_format:H:i', 'after:hora_apertura_tarde'],
            'modo_vacaciones'           => ['nullable', 'boolean'],
            'mensaje_vacaciones'        => ['nullable', 'string', 'max:500'],
            'descanso_activo_presencial'=> ['nullable', 'boolean'],
            'descanso_min_presencial'   => ['nullable', 'integer', 'min:0', 'max:120'],
            'descanso_activo_online'    => ['nullable', 'boolean'],
            'descanso_min_online'       => ['nullable', 'integer', 'min:0', 'max:120'],
            'dias_adelante'             => ['required', 'integer', 'min:7', 'max:365'],
            'slots'                     => ['nullable', 'array'],
            'slots.*'                   => ['string'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($v) {
            $cierreManana   = $this->input('hora_cierre_manana');
            $aperturaTarde  = $this->input('hora_apertura_tarde');
            if ($cierreManana && $aperturaTarde && $cierreManana >= $aperturaTarde) {
                $v->errors()->add('hora_apertura_tarde',
                    'La apertura de tarde (' . $aperturaTarde . ') debe ser posterior al cierre de mañana (' . $cierreManana . '). Los horarios no pueden solaparse.');
            }
        });
    }

    public function messages(): array
    {
        return [
            'hora_cierre_manana.after'  => 'El cierre de mañana debe ser posterior a la apertura de mañana.',
            'hora_cierre_tarde.after'   => 'El cierre de tarde debe ser posterior a la apertura de tarde.',
            'duracion_sesion_presencial_min.min' => 'La duración mínima de sesión presencial es de 15 minutos.',
            'duracion_sesion_presencial_min.max' => 'La duración máxima de sesión presencial es de 240 minutos.',
            'duracion_sesion_online_min.min'     => 'La duración mínima de sesión online es de 15 minutos.',
            'duracion_sesion_online_min.max'     => 'La duración máxima de sesión online es de 240 minutos.',
        ];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'modo_vacaciones'            => $this->boolean('modo_vacaciones'),
            'descanso_activo_presencial' => $this->boolean('descanso_activo_presencial'),
            'descanso_activo_online'     => $this->boolean('descanso_activo_online'),
        ]);
    }
}
