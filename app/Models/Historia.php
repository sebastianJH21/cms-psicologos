<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Historia extends Model
{
    use SoftDeletes;

    protected $fillable = ['paciente_id', 'fecha_sesion', 'titulo', 'contenido'];

    protected $casts = [
        'fecha_sesion' => 'date',
    ];

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function archivos(): HasMany
    {
        return $this->hasMany(HistoriaArchivo::class);
    }

    public function getTituloMostradoAttribute(): string
    {
        return $this->titulo ?: 'Sesión del ' . $this->fecha_sesion->format('d/m/Y');
    }

    public function setContenidoAttribute(?string $value): void
    {
        $this->attributes['contenido'] = $value === null ? null : clean($value, 'blog');
    }
}
