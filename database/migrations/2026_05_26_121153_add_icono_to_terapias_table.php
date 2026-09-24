<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('terapias', function (Blueprint $table) {
            $table->string('icono', 80)->nullable()->after('titulo');
        });
    }

    public function down(): void
    {
        Schema::table('terapias', function (Blueprint $table) {
            $table->dropColumn('icono');
        });
    }
};
