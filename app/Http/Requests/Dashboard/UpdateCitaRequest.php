<?php

namespace App\Http\Requests\Dashboard;

use App\Rules\NoOverlap;
use App\Services\CitaService;
use App\Support\PhoneHelper;
use Carbon\Carbon;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateCitaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function prepareForValidation(): void
    {
        $duracion = (int) (app(CitaService::class)->duracionSesion());
        $fechaInicio = $this->input('fecha_inicio');
        $fechaFin = null;

        if ($fechaInicio) {
            try {
                $fechaFin = Carbon::parse($fechaInicio)->addMinutes($duracion)->format('Y-m-d H:i:s');
            } catch (\Throwable) {
                $fechaFin = null;
            }
        }

        $this->merge([
            'telefono_provisional' => PhoneHelper::normalize($this->input('telefono_provisional')),
            'fecha_fin' => $fechaFin,
        ]);
    }

    public function rules(): array
    {
        $citaId = $this->route('cita')?->id;

        return [
            'nombre_provisional' => ['required', 'string', 'max:150'],
            'telefono_provisional' => ['required', 'string', 'max:30'],
            'email_provisional' => ['nullable', 'email', 'max:150'],
            'modalidad' => ['required', Rule::in(['online', 'presencial'])],
            'fecha_inicio' => ['required', 'date'],
            'fecha_fin' => ['required', 'date', 'after:fecha_inicio', new NoOverlap($citaId)],
            'motivo' => ['nullable', 'string', 'max:1000'],
            'estado' => ['required', Rule::in(['pendiente', 'confirmada', 'realizada', 'no_asistio', 'cancelada'])],
            'notas_internas' => ['nullable', 'string', 'max:5000'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre_provisional.required' => 'El nombre del paciente es obligatorio.',
            'nombre_provisional.max' => 'El nombre no puede superar los 150 caracteres.',
            'telefono_provisional.required' => 'El teléfono es obligatorio.',
            'telefono_provisional.max' => 'El teléfono no puede superar los 30 caracteres.',
            'email_provisional.email' => 'El email no tiene un formato válido.',
            'email_provisional.max' => 'El email no puede superar los 150 caracteres.',
            'modalidad.required' => 'Selecciona la modalidad de la cita.',
            'modalidad.in' => 'Modalidad no válida.',
            'fecha_inicio.required' => 'Indica fecha y hora de la cita.',
            'fecha_inicio.date' => 'La fecha indicada no es válida.',
            'fecha_fin.after' => 'La hora de fin debe ser posterior a la de inicio.',
            'motivo.max' => 'El motivo de la consulta no puede superar los 1000 caracteres.',
            'estado.required' => 'Selecciona el estado de la cita.',
            'estado.in' => 'El estado seleccionado no es válido.',
            'notas_internas.max' => 'Las notas internas no pueden superar los 5000 caracteres.',
        ];
    }
}
