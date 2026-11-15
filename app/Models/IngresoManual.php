<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IngresoManual extends Model
{
    protected $table = 'ingresos_manuales';

    protected $fillable = [
        'descripcion',
        'monto',
        'fecha',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
    ];
}
