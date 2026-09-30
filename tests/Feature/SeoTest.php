<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeoTest extends TestCase
{
    use RefreshDatabase;

    public function test_robots_txt_bloquea_el_panel_y_la_instalacion(): void
    {
        $response = $this->get('/robots.txt');

        $response->assertOk();
        $response->assertHeader('content-type', 'text/plain; charset=UTF-8');
        $response->assertSee('Disallow: /panel-psicologa', false);
        $response->assertSee('Disallow: /acceso-psicologa', false);
        $response->assertSee('Disallow: /instalacion', false);
        $response->assertSee('Sitemap:', false);
    }

    public function test_sitemap_xml_incluye_la_portada(): void
    {
        $response = $this->get('/sitemap.xml');

        $response->assertOk();
        $response->assertHeader('content-type', 'application/xml; charset=UTF-8');
        $response->assertSee('<loc>' . url('/') . '</loc>', false);
    }

    public function test_la_pagina_de_inicio_incluye_la_url_canonica(): void
    {
        $response = $this->get('/');

        $response->assertOk();
        $response->assertSee('rel="canonical"', false);
    }
}
