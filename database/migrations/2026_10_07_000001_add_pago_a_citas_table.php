<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->boolean('pagado')->default(false)->after('estado');
            $table->decimal('monto', 10, 2)->nullable()->after('pagado');
            $table->timestamp('pagado_at')->nullable()->after('monto');

            $table->index('pagado');
        });
    }

    public function down(): void
    {
        Schema::table('citas', function (Blueprint $table) {
            $table->dropIndex(['pagado']);
            $table->dropColumn(['pagado', 'monto', 'pagado_at']);
        });
    }
};
