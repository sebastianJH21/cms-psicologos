<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Terapia extends Model
{
    protected $table = 'terapias';

    protected $fillable = ['titulo', 'icono', 'descripcion', 'orden', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
