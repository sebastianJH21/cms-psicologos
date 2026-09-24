<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PlanPrecio extends Model
{
    protected $table = 'planes_precios';

    protected $fillable = ['tipo', 'nombre', 'descripcion', 'precio', 'duracion_min', 'orden'];

    protected $casts = [
        'precio' => 'decimal:2',
    ];
}
