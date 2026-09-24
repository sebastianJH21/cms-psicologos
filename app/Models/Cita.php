<?php

namespace App\Models;

use App\Support\PhoneHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Cita extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'paciente_id',
        'nombre_provisional',
        'telefono_provisional',
        'email_provisional',
        'modalidad',
        'fecha_inicio',
        'fecha_fin',
        'motivo',
        'estado',
        'notas_internas',
        'origen',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
    ];

    public const ESTADOS = [
        'pendiente' => 'Pendiente',
        'confirmada' => 'Confirmada',
        'realizada' => 'Realizada',
        'no_asistio' => 'No asistió',
        //'cancelada' => 'Cancelada',
    ];

    public const MODALIDADES = [
        'online' => 'Online',
        'presencial' => 'Presencial',
    ];

    public function setTelefonoProvisionalAttribute(?string $value): void
    {
        $this->attributes['telefono_provisional'] = PhoneHelper::normalize($value);
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getModalidadLabelAttribute(): string
    {
        return self::MODALIDADES[$this->modalidad] ?? $this->modalidad;
    }

    public function scopeNoCanceladas($query)
    {
        return $query->where('estado', '!=', 'cancelada');
    }

    public function paciente(): BelongsTo
    {
        return $this->belongsTo(Paciente::class);
    }

    public function getPacienteNombreAttribute(): string
    {
        return $this->paciente?->nombre_completo ?: $this->nombre_provisional;
    }

    public function getPacienteTelefonoAttribute(): ?string
    {
        return $this->paciente?->telefono ?: $this->telefono_provisional;
    }
}
