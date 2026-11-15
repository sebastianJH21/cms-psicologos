<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ingresos_manuales', function (Blueprint $table) {
            $table->id();
            $table->string('descripcion', 150);
            $table->decimal('monto', 10, 2);
            $table->date('fecha');
            $table->timestamps();

            $table->index('fecha');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ingresos_manuales');
    }
};
