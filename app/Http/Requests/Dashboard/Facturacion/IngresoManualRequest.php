<?php

namespace App\Http\Requests\Dashboard\Facturacion;

use Illuminate\Foundation\Http\FormRequest;

class IngresoManualRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'descripcion' => ['required', 'string', 'max:150'],
            'monto' => ['required', 'numeric', 'min:0.01', 'max:9999999.99'],
            'fecha' => ['required', 'date'],
        ];
    }

    public function messages(): array
    {
        return [
            'descripcion.required' => 'Describe el ingreso.',
            'monto.required' => 'Indica el importe del ingreso.',
            'monto.min' => 'El importe debe ser mayor que 0.',
            'fecha.required' => 'Indica la fecha del ingreso.',
        ];
    }
}
