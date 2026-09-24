<?php

namespace App\View\Composers;

use Illuminate\View\View;

class SidebarComposer
{
    public function compose(View $view): void
    {
        $view->with('sidebarMenu', $this->buildMenu());
    }

    private function buildMenu(): array
    {
        return [
            ['type' => 'item', 'icon' => 'fa-house', 'label' => 'Inicio', 'route' => 'dashboard.home', 'match' => ['dashboard.home']],
            ['type' => 'item', 'icon' => 'fa-calendar-check', 'label' => 'Citas', 'route' => 'dashboard.citas.index', 'match' => ['dashboard.citas.*']],
            ['type' => 'item', 'icon' => 'fa-calendar-days', 'label' => 'Calendario', 'route' => 'dashboard.calendario.index', 'match' => ['dashboard.calendario.*']],
            ['type' => 'item', 'icon' => 'fa-users', 'label' => 'Pacientes', 'route' => 'dashboard.pacientes.index', 'match' => ['dashboard.pacientes.*']],
            ['type' => 'item', 'icon' => 'fa-folder-open', 'label' => 'Historias', 'route' => 'dashboard.historias.index', 'match' => ['dashboard.historias.*', 'dashboard.pacientes.historias.*']],
            [
                'type' => 'group',
                'icon' => 'fa-newspaper',
                'label' => 'Blog',
                'children' => [
                    ['label' => 'Artículos', 'route' => 'dashboard.blog.articulos.index', 'match' => ['dashboard.blog.articulos.*']],
                    ['label' => 'Categorías', 'route' => 'dashboard.blog.categorias.index', 'match' => ['dashboard.blog.categorias.*']],
                ],
            ],
            [
                'type' => 'group',
                'icon' => 'fa-globe',
                'label' => 'Gestión Web',
                'children' => [
                    ['label' => 'Información pública', 'route' => 'dashboard.configuracion.perfil.edit', 'match' => ['dashboard.configuracion.perfil.*']],
                    ['label' => 'Servicios', 'route' => 'dashboard.configuracion.servicios.index', 'match' => ['dashboard.configuracion.servicios.*']],
                    ['label' => 'Especialidades', 'route' => 'dashboard.configuracion.terapias.index', 'match' => ['dashboard.configuracion.terapias.*']],
                    ['label' => 'Planes y precios', 'route' => 'dashboard.configuracion.planes.index', 'match' => ['dashboard.configuracion.planes.*']],
                    ['label' => 'Preguntas frecuentes', 'route' => 'dashboard.faqs.index', 'match' => ['dashboard.faqs.*']],
                    ['label' => 'Frases públicas', 'route' => 'dashboard.frases-publicas.index', 'match' => ['dashboard.frases-publicas.*']],
                    ['label' => 'Imágenes', 'route' => 'dashboard.imagenes.index', 'match' => ['dashboard.imagenes.*']],
                    ['label' => 'Logo', 'route' => 'dashboard.logo.edit', 'match' => ['dashboard.logo.*']],
                    ['label' => 'Temas', 'route' => 'dashboard.temas.index', 'match' => ['dashboard.temas.*']],
                ],
            ],
            [
                'type' => 'group',
                'icon' => 'fa-gear',
                'label' => 'Configuración',
                'children' => [
                    ['label' => 'Disponibilidad', 'route' => 'dashboard.disponibilidad.edit', 'match' => ['dashboard.disponibilidad.*']],
                    ['label' => 'Protección de datos', 'route' => 'dashboard.configuracion.proteccion-datos.edit', 'match' => ['dashboard.configuracion.proteccion-datos.*']],
                    ['label' => 'Email y notificaciones', 'route' => 'dashboard.configuracion.email-notificaciones.edit', 'match' => ['dashboard.configuracion.email-notificaciones.*']],
                    ['label' => 'Redes sociales', 'route' => 'dashboard.configuracion.redes-sociales.edit', 'match' => ['dashboard.configuracion.redes-sociales.*']],
                    ['label' => 'General', 'route' => 'dashboard.configuracion.general.edit', 'match' => ['dashboard.configuracion.general.*']],
                ],
            ],
        ];
    }
}
