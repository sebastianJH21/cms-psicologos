<?php

namespace App\Http\Requests\Public;

use Illuminate\Foundation\Http\FormRequest;

class ReservaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'modalidad' => 'required|in:online,presencial',
            'fecha_hora' => 'required|date|after:now',
            'nombre' => ['required', 'string', 'max:120', 'regex:/^[\p{L}\s\'.\-]+$/u'],
            'telefono' => ['required', 'string', 'max:30', 'regex:/^(?=(?:\D*\d){9,})\+?[\d\s().\-]{9,30}$/'],
            'motivo' => 'nullable|string|max:1000',
            'website' => 'nullable|max:0',
            'privacidad' => ['accepted'],
            'captcha_token' => ['required', 'string'],
            'captcha' => ['required', function ($attribute, $value, $fail) {
                if (!ctype_digit((string) $value)) {
                    $fail('Introduce solo números en la operación de seguridad.');
                    return;
                }

                $token = (string) $this->input('captcha_token');
                if ($token === '') {
                    $fail('No se ha podido validar la operación de seguridad. Recarga la página e inténtalo de nuevo.');
                    return;
                }

                try {
                    $payload = \Illuminate\Support\Facades\Crypt::decryptString($token);
                } catch (\Throwable) {
                    $fail('No se ha podido validar la operación de seguridad. Recarga la página e inténtalo de nuevo.');
                    return;
                }

                [$respuesta, $ts] = array_pad(explode('|', $payload, 2), 2, null);

                if ($ts === null || (time() - (int) $ts) > 3600) {
                    $fail('La operación de seguridad ha caducado. Recarga la página e inténtalo de nuevo.');
                    return;
                }

                if ((int) $value !== (int) $respuesta) {
                    $fail('La operación de seguridad no es correcta. Inténtalo de nuevo.');
                }
            }],
        ];
    }

    public function messages(): array
    {
        return [
            'modalidad.required' => 'Selecciona si la cita es online o presencial.',
            'modalidad.in' => 'La modalidad seleccionada no es válida.',
            'fecha_hora.required' => 'Selecciona un día y hora.',
            'fecha_hora.date' => 'La fecha y hora seleccionadas no son válidas.',
            'fecha_hora.after' => 'La fecha seleccionada ya no es válida.',
            'nombre.required' => 'Indícame tu nombre.',
            'nombre.string' => 'El nombre no es válido.',
            'nombre.max' => 'El nombre es demasiado largo (máximo 120 caracteres).',
            'nombre.regex' => 'El nombre solo puede contener letras y espacios.',
            'telefono.required' => 'El teléfono es obligatorio.',
            'telefono.string' => 'El teléfono no es válido.',
            'telefono.max' => 'El teléfono es demasiado largo (máximo 30 caracteres).',
            'telefono.regex' => 'El teléfono no es correcto: debe tener al menos 9 dígitos. Revisa que no falte ningún número.',
            'motivo.string' => 'El motivo de la consulta no es válido.',
            'motivo.max' => 'El motivo de la consulta es demasiado largo (máximo 1000 caracteres).',
            'website.max' => 'No se ha podido procesar el formulario. Inténtalo de nuevo.',
            'privacidad.accepted' => 'Debes aceptar la política de privacidad para reservar la cita.',
            'captcha.required' => 'Resuelve la operación de seguridad para continuar.',
            'captcha_token.required' => 'No se ha podido validar la operación de seguridad. Recarga la página e inténtalo de nuevo.',
        ];
    }
}
