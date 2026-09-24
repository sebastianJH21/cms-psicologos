<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Servicio extends Model
{
    protected $table = 'servicios';

    protected $fillable = ['titulo', 'descripcion', 'icono', 'orden', 'activo'];

    protected $casts = [
        'activo' => 'boolean',
    ];
}
