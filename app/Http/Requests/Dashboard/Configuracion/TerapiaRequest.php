<?php

namespace App\Http\Requests\Dashboard\Configuracion;

use Illuminate\Foundation\Http\FormRequest;

class TerapiaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['activo' => $this->boolean('activo')]);
    }

    public function rules(): array
    {
        return [
            'titulo'      => ['required', 'string', 'max:150'],
            'icono'       => ['nullable', 'string', 'max:80'],
            'descripcion' => ['nullable', 'string', 'max:1000'],
            'orden'       => ['nullable', 'integer', 'min:0'],
            'activo'      => ['nullable', 'boolean'],
        ];
    }
}
