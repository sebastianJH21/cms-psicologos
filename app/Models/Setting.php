<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['key', 'value', 'group', 'updated_at'];

    protected $casts = [
        'value' => 'array',
        'updated_at' => 'datetime',
    ];

    public static function set(string $key, mixed $value, string $group = 'general'): void
    {
        static::updateOrCreate(
            ['key' => $key],
            ['value' => $value, 'group' => $group, 'updated_at' => now()]
        );
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        try {
            $row = static::find($key);
            return $row ? $row->value : $default;
        } catch (\Throwable) {
            // Sin conexión a BD o tablas inexistentes (instalación nueva):
            // se devuelve el valor por defecto para que el wizard pueda arrancar.
            return $default;
        }
    }
}
