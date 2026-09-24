<?php

namespace App\Http\Requests\Install;

use Illuminate\Foundation\Http\FormRequest;

class BdConfigRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'db_host' => ['required', 'string', 'max:120'],
            'db_port' => ['required', 'integer', 'between:1,65535'],
            'db_database' => ['required', 'string', 'max:64', 'regex:/^[a-zA-Z0-9_-]+$/'],
            'db_username' => ['required', 'string', 'max:60'],
            'db_password' => ['nullable', 'string', 'max:120'],
        ];
    }

    public function messages(): array
    {
        return [
            'db_database.regex' => 'El nombre de la base de datos solo puede contener letras, números, guiones y guiones bajos.',
        ];
    }
}
