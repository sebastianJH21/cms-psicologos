<?php

namespace App\Http\Requests\Dashboard\Configuracion;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PlanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tipo'         => ['required', Rule::in(['online', 'presencial'])],
            'nombre'       => ['required', 'string', 'max:150'],
            'descripcion'  => ['nullable', 'string', 'max:1000'],
            'precio'       => ['required', 'numeric', 'min:0', 'max:99999.99'],
            'duracion_min' => ['required', 'integer', 'min:5', 'max:600'],
            'orden'        => ['nullable', 'integer', 'min:0'],
        ];
    }
}
