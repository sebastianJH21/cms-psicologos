<?php

namespace App\Http\Requests\Install;

use App\Support\PhoneHelper;
use Illuminate\Foundation\Http\FormRequest;

class DatosPublicosRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'telefono_publico' => PhoneHelper::normalize($this->input('telefono_publico')),
        ]);
    }

    public function rules(): array
    {
        return [
            'slogan' => ['nullable', 'string', 'max:200'],
            'telefono_publico' => ['required', 'string', 'min:6', 'max:30'],
            'email_publico' => ['required', 'email:rfc', 'max:150'],
            'numero_colegiado' => ['nullable', 'string', 'max:50'],
            'sobre_mi' => ['nullable', 'string', 'max:5000'],
            'direccion' => ['nullable', 'string', 'max:255'],
        ];
    }
}
