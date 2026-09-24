<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pacientes', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('apellidos', 150)->nullable();
            $table->string('telefono', 30)->unique();
            $table->string('email', 150)->nullable();
            $table->date('fecha_nacimiento')->nullable();
            $table->enum('genero', ['mujer', 'hombre', 'otro', 'prefiero_no_decir'])->nullable();
            $table->string('direccion', 255)->nullable();
            $table->text('motivo_inicial')->nullable();
            $table->text('notas')->nullable();
            $table->enum('origen', ['publica', 'manual'])->default('manual');
            $table->timestamps();
            $table->softDeletes();

            $table->index('nombre');
            $table->index('apellidos');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pacientes');
    }
};
