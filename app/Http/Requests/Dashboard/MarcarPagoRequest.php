<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class MarcarPagoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'pagado' => ['required', 'boolean'],
            'monto' => ['nullable', 'numeric', 'min:0.01', 'max:9999999.99'],
        ];
    }

    public function messages(): array
    {
        return [
            'monto.min' => 'El importe debe ser mayor que 0.',
        ];
    }
}
