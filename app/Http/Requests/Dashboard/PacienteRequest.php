<?php

namespace App\Http\Requests\Dashboard;

use App\Support\PhoneHelper;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PacienteRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge([
            'telefono' => PhoneHelper::normalize($this->input('telefono')),
        ]);
    }

    public function rules(): array
    {
        $id = $this->route('paciente')?->id;

        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellidos' => ['nullable', 'string', 'max:150'],
            'dni' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('pacientes', 'dni')->ignore($id)->whereNull('deleted_at'),
            ],
            'telefono' => [
                'required',
                'string',
                'max:30',
                'regex:/^\+?\d{6,20}$/',
                Rule::unique('pacientes', 'telefono')->ignore($id)->whereNull('deleted_at'),
            ],
            'email' => ['nullable', 'email', 'max:150'],
            'fecha_nacimiento' => ['nullable', 'date', 'before_or_equal:today'],
            'genero' => ['nullable', Rule::in(array_keys(\App\Models\Paciente::GENEROS))],
            'direccion' => ['nullable', 'string', 'max:255'],
            'motivo_inicial' => ['nullable', 'string', 'max:2000'],
            'notas' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'telefono.regex' => 'El teléfono debe contener solo números (puede empezar por +).',
            'telefono.unique' => 'Ya existe un paciente registrado con este teléfono.',
            'fecha_nacimiento.before_or_equal' => 'La fecha de nacimiento no puede ser futura.',
        ];
    }
}
