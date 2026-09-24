<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class ProteccionDatosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'plantilla_html' => ['required', 'string', 'max:200000'],
        ];
    }

    public function messages(): array
    {
        return [
            'plantilla_html.required' => 'La plantilla no puede estar vacía.',
        ];
    }
}
