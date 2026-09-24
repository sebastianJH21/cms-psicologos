<?php

namespace App\Http\Requests\Dashboard\Configuracion;

use App\Support\PhoneHelper;
use Illuminate\Foundation\Http\FormRequest;

class PerfilRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        if ($this->filled('telefono_publico')) {
            $this->merge([
                'telefono_publico' => PhoneHelper::normalize($this->input('telefono_publico')),
            ]);
        }
    }

    public function rules(): array
    {
        return [
            'slogan'           => ['nullable', 'string', 'max:200'],
            'telefono_publico' => ['nullable', 'string', 'max:30', 'regex:/^\+?\d{6,20}$/'],
            'email_publico'    => ['nullable', 'email', 'max:150'],
            'numero_colegiado' => ['nullable', 'string', 'max:50'],
            'sobre_mi'         => ['nullable', 'string', 'max:200000'],
            'direccion'        => ['nullable', 'string', 'max:300'],
            'lat'              => ['nullable', 'numeric', 'between:-90,90'],
            'lng'              => ['nullable', 'numeric', 'between:-180,180'],
            'foto'             => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'telefono_publico.regex' => 'El teléfono debe contener solo números (puede empezar por +).',
            'foto.max'               => 'La foto no puede superar los 4 MB.',
        ];
    }
}
