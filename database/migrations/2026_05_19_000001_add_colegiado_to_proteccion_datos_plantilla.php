<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private string $fragmentoAntiguo = '<strong>{{psicologa_nombre}}</strong>, con email de contacto';
    private string $fragmentoNuevo = '<strong>{{psicologa_nombre}}</strong> (Nº de colegiado/a <strong>{{psicologa_colegiado}}</strong>), con email de contacto';

    public function up(): void
    {
        $raw = DB::table('settings')
            ->where('key', 'proteccion_datos.plantilla_html')
            ->value('value');

        if ($raw === null) {
            return;
        }

        $html = json_decode($raw, true);
        if (!is_string($html)) {
            return;
        }

        // No tocar si ya incluye la variable o si la plantilla fue personalizada
        // (el fragmento ancla ya no existe).
        if (str_contains($html, '{{psicologa_colegiado}}')) {
            return;
        }
        if (!str_contains($html, $this->fragmentoAntiguo)) {
            return;
        }

        $html = str_replace($this->fragmentoAntiguo, $this->fragmentoNuevo, $html);

        DB::table('settings')
            ->where('key', 'proteccion_datos.plantilla_html')
            ->update([
                'value' => json_encode($html),
                'updated_at' => now(),
            ]);
    }

    public function down(): void
    {
        $raw = DB::table('settings')
            ->where('key', 'proteccion_datos.plantilla_html')
            ->value('value');

        if ($raw === null) {
            return;
        }

        $html = json_decode($raw, true);
        if (!is_string($html) || !str_contains($html, $this->fragmentoNuevo)) {
            return;
        }

        $html = str_replace($this->fragmentoNuevo, $this->fragmentoAntiguo, $html);

        DB::table('settings')
            ->where('key', 'proteccion_datos.plantilla_html')
            ->update([
                'value' => json_encode($html),
                'updated_at' => now(),
            ]);
    }
};
