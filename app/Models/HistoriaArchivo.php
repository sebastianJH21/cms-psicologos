<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Storage;

class HistoriaArchivo extends Model
{
    protected $fillable = ['historia_id', 'nombre_original', 'ruta', 'tipo', 'mime_type', 'tamanio'];

    public function historia(): BelongsTo
    {
        return $this->belongsTo(Historia::class);
    }

    public function getUrlAttribute(): string
    {
        if ($this->historia && $this->historia->paciente_id) {
            return route('dashboard.pacientes.historias.archivos.show', [
                'paciente' => $this->historia->paciente_id,
                'historia' => $this->historia_id,
                'archivo'  => $this->id,
            ]);
        }

        return Storage::disk('public')->url($this->ruta);
    }

    public function getTamanioFormateadoAttribute(): string
    {
        $bytes = $this->tamanio;
        if ($bytes < 1024) return $bytes . ' B';
        if ($bytes < 1048576) return round($bytes / 1024, 1) . ' KB';
        return round($bytes / 1048576, 1) . ' MB';
    }
}
