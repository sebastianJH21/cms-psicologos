<?php

namespace App\Http\Requests\Install;

use Illuminate\Foundation\Http\FormRequest;

class ServiciosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'servicios' => ['nullable', 'array', 'max:20'],
            'servicios.*.titulo' => ['required_with:servicios.*.descripcion', 'nullable', 'string', 'max:120'],
            'servicios.*.descripcion' => ['nullable', 'string', 'max:600'],

            'terapias' => ['nullable', 'array', 'max:20'],
            'terapias.*.titulo' => ['required_with:terapias.*.descripcion', 'nullable', 'string', 'max:120'],
            'terapias.*.descripcion' => ['nullable', 'string', 'max:600'],

            'planes' => ['nullable', 'array', 'max:20'],
            'planes.*.tipo' => ['required_with:planes.*.nombre', 'in:online,presencial'],
            'planes.*.nombre' => ['required_with:planes.*.precio', 'nullable', 'string', 'max:120'],
            'planes.*.descripcion' => ['nullable', 'string', 'max:600'],
            'planes.*.precio' => ['required_with:planes.*.nombre', 'nullable', 'numeric', 'min:0', 'max:99999.99'],
            'planes.*.duracion_min' => ['nullable', 'integer', 'min:5', 'max:600'],
        ];
    }
}
