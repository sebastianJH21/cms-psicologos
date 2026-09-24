<?php

namespace App\Http\Requests\Install;

use Illuminate\Foundation\Http\FormRequest;

class FotoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'foto' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
        ];
    }

    public function messages(): array
    {
        return [
            'foto.max' => 'La foto no puede superar los 4MB.',
            'foto.mimes' => 'La foto debe ser jpg, png o webp.',
        ];
    }
}
