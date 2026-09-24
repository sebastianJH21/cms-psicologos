<?php

namespace App\Services;

use App\Models\Paciente;
use App\Support\PhoneHelper;
use Illuminate\Support\Facades\DB;

class PacienteService
{
    public function findOrCreateByPhone(string $telefono, array $datos = []): Paciente
    {
        $normalizado = PhoneHelper::normalize($telefono);

        return DB::transaction(function () use ($normalizado, $datos) {
            $paciente = Paciente::withTrashed()
                ->where('telefono', $normalizado)
                ->lockForUpdate()
                ->first();

            if ($paciente) {
                if ($paciente->trashed()) {
                    $paciente->restore();
                }
                return $paciente;
            }

            $datos['telefono'] = $normalizado;
            $datos['origen'] = $datos['origen'] ?? 'publica';
            $datos['nombre'] = $datos['nombre'] ?? 'Sin nombre';

            return Paciente::create($datos);
        });
    }

    public function crear(array $datos): Paciente
    {
        $datos['origen'] = $datos['origen'] ?? 'manual';
        return Paciente::create($datos);
    }

    public function actualizar(Paciente $paciente, array $datos): Paciente
    {
        $paciente->fill($datos)->save();
        return $paciente;
    }

    public function eliminar(Paciente $paciente): void
    {
        $paciente->delete();
    }

    public function restaurar(Paciente $paciente): void
    {
        $paciente->restore();
    }
}
