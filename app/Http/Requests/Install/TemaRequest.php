<?php

namespace App\Http\Requests\Install;

use Illuminate\Foundation\Http\FormRequest;

class TemaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'tema' => ['required', 'string', 'in:tema-base,tema-minimal,tema-clinica,tema-warm,tema-modern'],
            'modo' => ['required', 'string', 'in:landing,multipage'],
        ];
    }
}
