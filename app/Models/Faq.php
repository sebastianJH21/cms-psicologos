<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    protected $table = 'faqs';

    protected $fillable = ['pregunta', 'respuesta', 'orden', 'activa'];

    protected $casts = [
        'activa' => 'boolean',
        'orden'  => 'integer',
    ];

    public function setRespuestaAttribute(?string $value): void
    {
        $this->attributes['respuesta'] = $value === null ? null : clean($value, 'blog');
    }
}
