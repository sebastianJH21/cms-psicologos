<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Articulo extends Model
{
    use SoftDeletes;

    protected $table = 'articulos';

    protected $fillable = [
        'categoria_id',
        'titulo',
        'slug',
        'extracto',
        'contenido',
        'imagen_path',
        'estado',
        'published_at',
        'meta_title',
        'meta_description',
    ];

    protected $casts = [
        'published_at' => 'datetime',
    ];

    public const ESTADOS = [
        'borrador' => 'Borrador',
        'publicado' => 'Publicado',
        'archivado' => 'Archivado',
    ];

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(CategoriaBlog::class, 'categoria_id');
    }

    public function scopePublicados(Builder $query): Builder
    {
        return $query
            ->where('estado', 'publicado')
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function getEstadoLabelAttribute(): string
    {
        return self::ESTADOS[$this->estado] ?? $this->estado;
    }

    public function getImagenUrlAttribute(): ?string
    {
        if (!$this->imagen_path) {
            return null;
        }
        return asset('storage/' . $this->imagen_path);
    }

    public function setContenidoAttribute(?string $value): void
    {
        $this->attributes['contenido'] = $value === null ? null : clean($value, 'blog');
    }

    public function setExtractoAttribute(?string $value): void
    {
        $this->attributes['extracto'] = $value === null ? null : strip_tags($value);
    }
}
