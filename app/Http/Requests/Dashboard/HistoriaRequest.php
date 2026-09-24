<?php

namespace App\Http\Requests\Dashboard;

use Illuminate\Foundation\Http\FormRequest;

class HistoriaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'fecha_sesion' => 'required|date',
            'titulo'       => 'nullable|string|max:255',
            'contenido'    => 'required|string',
            'archivos'     => 'nullable|array|max:10',
            'archivos.*'   => [
                'file',
                'mimes:jpg,jpeg,png,webp,pdf',
                'mimetypes:application/pdf,image/jpeg,image/png,image/webp',
                'max:10240',
            ],
        ];
    }

    public function messages(): array
    {
        return [
            'fecha_sesion.required' => 'La fecha de sesión es obligatoria.',
            'fecha_sesion.date'     => 'La fecha de sesión no es válida.',
            'contenido.required'    => 'El contenido de la sesión es obligatorio.',
            'archivos.max'          => 'Máximo 10 archivos por sesión.',
            'archivos.*.mimes'      => 'Solo se aceptan imágenes (JPG, PNG, WebP) y PDFs.',
            'archivos.*.mimetypes'  => 'Solo se aceptan imágenes (JPG, PNG, WebP) y PDFs.',
            'archivos.*.max'        => 'Cada archivo no puede superar los 10 MB.',
        ];
    }
}
