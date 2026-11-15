<?php

namespace App\Http\Requests\Dashboard\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

class WhatsappRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plantilla_confirmacion' => ['required', 'string', 'max:1000'],
            'plantilla_recordatorio_pago' => ['required', 'string', 'max:1000'],
        ];
    }

    public function messages(): array
    {
        return [
            'plantilla_confirmacion.required' => 'La plantilla no puede estar vacía.',
            'plantilla_confirmacion.max' => 'La plantilla es demasiado larga (máximo 1000 caracteres).',
            'plantilla_recordatorio_pago.required' => 'La plantilla de recordatorio de pago no puede estar vacía.',
            'plantilla_recordatorio_pago.max' => 'La plantilla es demasiado larga (máximo 1000 caracteres).',
        ];
    }
}
