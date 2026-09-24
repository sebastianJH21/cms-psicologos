<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PeriodoVacaciones extends Model
{
    protected $table = 'periodos_vacaciones';

    protected $fillable = ['fecha_inicio', 'fecha_fin'];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin'    => 'date',
    ];
}
