<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * FIX-002 (fixes.md): precios en pesos colombianos necesitan hasta 7
     * cifras enteras (decimal(8,2) solo admitía 6). Se usa SQL nativo para
     * no depender de doctrine/dbal. SQLite (tests) no aplica precisión
     * real a las columnas numéricas, así que no necesita este ALTER.
     */
    public function up(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE planes_precios MODIFY precio DECIMAL(10,2) NOT NULL');
        }
    }

    public function down(): void
    {
        if (Schema::getConnection()->getDriverName() === 'mysql') {
            DB::statement('ALTER TABLE planes_precios MODIFY precio DECIMAL(8,2) NOT NULL');
        }
    }
};
