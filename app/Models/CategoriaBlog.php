<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CategoriaBlog extends Model
{
    protected $table = 'categorias_blog';

    protected $fillable = [
        'nombre',
        'slug',
        'descripcion',
        'orden',
    ];

    public function articulos(): HasMany
    {
        return $this->hasMany(Articulo::class, 'categoria_id');
    }

    public function getRouteKeyName(): string
    {
        return 'id';
    }
}
