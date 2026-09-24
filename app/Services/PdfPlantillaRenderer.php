<?php

namespace App\Services;

use App\Models\Paciente;
use App\Models\Profile;
use App\Models\Setting;
use App\Models\User;

class PdfPlantillaRenderer
{
    public function plantillaHtml(): string
    {
        return Setting::get('proteccion_datos.plantilla_html', '<p>Plantilla vacía. Edítala desde el panel.</p>');
    }

    public function placeholdersDisponibles(): array
    {
        return [
            '{{nombre}}'             => 'Nombre del paciente',
            '{{apellidos}}'          => 'Apellidos del paciente',
            '{{dni}}'                => 'DNI / NIE del paciente',
            '{{telefono}}'           => 'Teléfono del paciente',
            '{{email}}'              => 'Email del paciente',
            '{{direccion}}'          => 'Dirección del paciente',
            '{{fecha}}'              => 'Fecha actual (dd/mm/aaaa)',
            '{{psicologa_nombre}}'   => 'Nombre de la psicóloga',
            '{{psicologa_email}}'    => 'Email de la psicóloga',
            '{{psicologa_telefono}}' => 'Teléfono de la psicóloga',
            '{{psicologa_colegiado}}' => 'Nº de colegiado/a de la psicóloga',
        ];
    }

    public function rellenarParaPaciente(Paciente $paciente): string
    {
        $psicologa = User::query()->first();
        $perfil = Profile::query()->first();

        return $this->aplicar($this->plantillaHtml(), [
            '{{nombre}}'             => e($paciente->nombre),
            '{{apellidos}}'          => e($paciente->apellidos ?? ''),
            '{{dni}}'                => e($paciente->dni ?? ''),
            '{{telefono}}'           => e($paciente->telefono),
            '{{email}}'              => e($paciente->email ?? ''),
            '{{direccion}}'          => e($paciente->direccion ?? ''),
            '{{fecha}}'              => now()->format('d/m/Y'),
            '{{psicologa_nombre}}'   => e($psicologa?->nombre_completo ?? ''),
            '{{psicologa_email}}'    => e($psicologa?->email ?? ''),
            '{{psicologa_telefono}}' => e($psicologa?->telefono ?? ''),
            '{{psicologa_colegiado}}' => e($perfil?->numero_colegiado ?? ''),
        ]);
    }

    public function rellenarVacio(): string
    {
        return $this->aplicar($this->plantillaHtml(), array_fill_keys(
            array_keys($this->placeholdersDisponibles()),
            '<span style="display:inline-block;border-bottom:1px solid #999;min-width:6em;">&nbsp;</span>'
        ));
    }

    private function aplicar(string $plantilla, array $sustituciones): string
    {
        return str_replace(
            array_keys($sustituciones),
            array_values($sustituciones),
            $plantilla
        );
    }
}
