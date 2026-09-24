<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('planes_precios', function (Blueprint $table) {
            $table->id();
            $table->enum('tipo', ['online', 'presencial']);
            $table->string('nombre', 120);
            $table->text('descripcion')->nullable();
            $table->decimal('precio', 8, 2);
            $table->unsignedInteger('duracion_min')->default(60);
            $table->unsignedInteger('orden')->default(0);
            $table->timestamps();

            $table->index('tipo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('planes_precios');
    }
};
