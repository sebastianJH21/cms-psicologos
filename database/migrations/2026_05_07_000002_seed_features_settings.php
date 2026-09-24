<?php

use App\Models\Setting;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    public function up(): void
    {
        $features = [
            'features.blog_enabled'      => true,
            'features.reservas_enabled'  => true,
            'features.faq_enabled'       => true,
            'features.servicios_enabled' => true,
            'features.sobre_mi_enabled'  => true,
        ];

        foreach ($features as $key => $value) {
            if (!Setting::find($key)) {
                Setting::set($key, $value, 'features');
            }
        }
    }

    public function down(): void {}
};
