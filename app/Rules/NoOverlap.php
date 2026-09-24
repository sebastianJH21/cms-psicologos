<?php

namespace App\Rules;

use App\Models\Cita;
use Closure;
use Illuminate\Contracts\Validation\DataAwareRule;
use Illuminate\Contracts\Validation\ValidationRule;

class NoOverlap implements ValidationRule, DataAwareRule
{
    protected array $data = [];

    public function __construct(protected ?int $ignoreCitaId = null)
    {
    }

    public function setData(array $data): static
    {
        $this->data = $data;
        return $this;
    }

    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        $inicio = $this->data['fecha_inicio'] ?? null;
        $fin = $this->data['fecha_fin'] ?? null;

        if (!$inicio || !$fin) {
            return;
        }

        $query = Cita::query()
            ->whereNull('deleted_at')
            ->where('estado', '!=', 'cancelada')
            ->where('fecha_inicio', '<', $fin)
            ->where('fecha_fin', '>', $inicio);

        if ($this->ignoreCitaId) {
            $query->where('id', '!=', $this->ignoreCitaId);
        }

        if ($query->exists()) {
            $fail('Existe otra cita que se solapa con este horario. Revisa la agenda y elige otro hueco.');
        }
    }
}
