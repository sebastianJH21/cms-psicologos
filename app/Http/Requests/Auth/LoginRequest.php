<?php

namespace App\Http\Requests\Auth;

use App\Support\PhoneHelper;
use Illuminate\Foundation\Http\FormRequest;

class LoginRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email:rfc', 'max:191'],
            'telefono' => ['required', 'string', 'max:30'],
            'password' => ['required', 'string'],
            'remember' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'email.required' => 'Introduce tu email.',
            'email.email' => 'El email no parece válido.',
            'telefono.required' => 'Introduce tu número de teléfono.',
            'password.required' => 'Introduce tu contraseña.',
        ];
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'telefono' => PhoneHelper::normalize($this->input('telefono')),
            'remember' => $this->boolean('remember'),
        ]);
    }

    public function throttleKey(): string
    {
        return strtolower($this->input('email', '')) . '|' . $this->ip();
    }
}
