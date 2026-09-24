<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('articulos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('categoria_id')->nullable();
            $table->string('titulo', 200);
            $table->string('slug', 220)->unique();
            $table->string('extracto', 300)->nullable();
            $table->longText('contenido');
            $table->string('imagen_path', 255)->nullable();
            $table->enum('estado', ['borrador', 'publicado', 'archivado'])->default('borrador');
            $table->dateTime('published_at')->nullable();
            $table->string('meta_title', 200)->nullable();
            $table->string('meta_description', 300)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index('categoria_id');
            $table->index(['estado', 'published_at']);

            $table->foreign('categoria_id')
                ->references('id')->on('categorias_blog')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('articulos');
    }
};
