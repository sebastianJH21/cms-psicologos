<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('citas', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('paciente_id')->nullable();
            $table->string('nombre_provisional', 150);
            $table->string('telefono_provisional', 30);
            $table->string('email_provisional', 150)->nullable();
            $table->enum('modalidad', ['online', 'presencial']);
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->text('motivo')->nullable();
            $table->enum('estado', ['pendiente', 'confirmada', 'cancelada', 'realizada', 'no_asistio'])->default('pendiente');
            $table->text('notas_internas')->nullable();
            $table->enum('origen', ['publica', 'manual'])->default('manual');
            $table->timestamps();
            $table->softDeletes();

            $table->index('fecha_inicio');
            $table->index('estado');
            $table->index('paciente_id');
            $table->index(['fecha_inicio', 'estado']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('citas');
    }
};
