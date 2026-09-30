<?php

namespace Tests\Feature;

use App\Models\User;
use App\Services\ThemeManager;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ThemePreviewTest extends TestCase
{
    use RefreshDatabase;

    public function test_un_visitante_sin_sesion_no_puede_forzar_la_previsualizacion(): void
    {
        $this->get('/?preview_theme=tema-aurora&preview_mode=multipage');

        $this->assertSame('tema-base', app(ThemeManager::class)->activeSlug());
        $this->assertSame('landing', app(ThemeManager::class)->activeMode());
    }

    public function test_la_psicologa_con_sesion_si_puede_previsualizar_otro_tema(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)->get('/?preview_theme=tema-aurora&preview_mode=multipage');

        $this->assertSame('tema-aurora', app(ThemeManager::class)->activeSlug());
        $this->assertSame('multipage', app(ThemeManager::class)->activeMode());
    }
}
