<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    protected $table = 'profile';

    protected $fillable = [
        'slogan',
        'telefono_publico',
        'email_publico',
        'numero_colegiado',
        'sobre_mi',
        'direccion',
        'lat',
        'lng',
        'foto_path',
    ];

    protected $casts = [
        'lat' => 'float',
        'lng' => 'float',
    ];

    public static function singleton(): self
    {
        return static::firstOrCreate(['id' => 1]);
    }

    public function setSobreMiAttribute(?string $value): void
    {
        $this->attributes['sobre_mi'] = $value === null ? null : clean($value, 'blog');
    }
}
