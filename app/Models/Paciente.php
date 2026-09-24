<?php

namespace App\Models;

use App\Support\PhoneHelper;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Paciente extends Model
{
    use SoftDeletes;

    protected $table = 'pacientes';

    protected $fillable = [
        'nombre',
        'apellidos',
        'dni',
        'telefono',
        'email',
        'fecha_nacimiento',
        'genero',
        'direccion',
        'motivo_inicial',
        'notas',
        'origen',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
    ];

    public const GENEROS = [
        'mujer' => 'Mujer',
        'hombre' => 'Hombre',
        'otro' => 'Otro',
        'prefiero_no_decir' => 'Prefiero no decir',
    ];

    public function setTelefonoAttribute(?string $value): void
    {
        $this->attributes['telefono'] = PhoneHelper::normalize($value);
    }

    public function getNombreCompletoAttribute(): string
    {
        return trim($this->nombre . ' ' . ($this->apellidos ?? ''));
    }

    public function getGeneroLabelAttribute(): ?string
    {
        return $this->genero ? (self::GENEROS[$this->genero] ?? $this->genero) : null;
    }

    public function getEdadAttribute(): ?int
    {
        return $this->fecha_nacimiento?->age;
    }

    public function citas(): HasMany
    {
        return $this->hasMany(Cita::class)->orderBy('fecha_inicio', 'desc');
    }

    public function historias(): HasMany
    {
        return $this->hasMany(Historia::class)->orderBy('fecha_sesion', 'desc');
    }

    public function scopeBuscar($query, ?string $termino)
    {
        $termino = trim((string) $termino);
        if ($termino === '') {
            return $query;
        }

        $telefono = PhoneHelper::normalize($termino);

        return $query->where(function ($q) use ($termino, $telefono) {
            $q->where('nombre', 'like', "%{$termino}%")
                ->orWhere('apellidos', 'like', "%{$termino}%")
                ->orWhere('email', 'like', "%{$termino}%")
                ->orWhereRaw("CONCAT(COALESCE(nombre,''), ' ', COALESCE(apellidos,'')) LIKE ?", ["%{$termino}%"]);
            if ($telefono !== null) {
                $q->orWhere('telefono', 'like', "%{$telefono}%");
            }
        });
    }
}
