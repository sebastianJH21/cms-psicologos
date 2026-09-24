<?php

namespace App\Http\Requests\Dashboard\Blog;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class ArticuloRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $id = $this->route('articulo')?->id;

        return [
            'categoria_id' => ['nullable', 'integer', 'exists:categorias_blog,id'],
            'titulo' => ['required', 'string', 'max:200'],
            'slug' => [
                'nullable',
                'string',
                'max:220',
                'regex:/^[a-z0-9-]+$/',
                Rule::unique('articulos', 'slug')->ignore($id),
            ],
            'extracto' => ['nullable', 'string', 'max:300'],
            'contenido' => ['required', 'string'],
            'imagen' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'eliminar_imagen' => ['nullable', 'boolean'],
            'estado' => ['required', Rule::in(['borrador', 'publicado', 'archivado'])],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:200'],
            'meta_description' => ['nullable', 'string', 'max:300'],
        ];
    }

    public function withValidator($validator): void
    {
        $validator->after(function ($validator) {
            if ($this->input('estado') === 'publicado' && !$this->input('published_at')) {
                $validator->errors()->add('published_at', 'Indica la fecha de publicación cuando el estado es "publicado".');
            }
        });
    }

    public function messages(): array
    {
        return [
            'slug.regex' => 'El slug solo puede contener letras minúsculas, números y guiones.',
            'slug.unique' => 'Ese slug ya está en uso por otro artículo.',
            'imagen.max' => 'La imagen no puede superar los 4 MB.',
            'imagen.mimes' => 'Formatos permitidos: jpg, jpeg, png, webp.',
        ];
    }
}
