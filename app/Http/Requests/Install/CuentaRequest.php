<?php

namespace App\Http\Requests\Install;

use App\Support\PhoneHelper;
use Illuminate\Foundation\Http\FormRequest;

class CuentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'telefono' => PhoneHelper::normalize($this->input('telefono')),
        ]);
    }

    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:80'],
            'apellidos' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:150'],
            'telefono' => ['required', 'string', 'min:6', 'max:30'],
            'password' => ['required', 'string', 'min:10', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'confirmed'],
        ];
    }

    public function messages(): array
    {
        return [
            'password.min' => 'La contraseña debe tener al menos 10 caracteres.',
            'password.regex' => 'La contraseña debe contener al menos una mayúscula y un número.',
            'password.confirmed' => 'La confirmación de contraseña no coincide.',
            'telefono.min' => 'El teléfono debe tener al menos 6 dígitos.',
        ];
    }
}
