<?php
// Script de sustitución de frases en los temas
// Ejecutar: php cms/database/seeders/apply-phrases.php

$rootBase = realpath(__DIR__ . '/../../..');
$themesRoot = $rootBase . '/cms/themes';

$repls = [
    // Servicios
    ['servicios', '¿En qué puedo ayudarte?', 'servicios_title', '¿En qué puedo ayudarte?'],
    ['servicios', 'Áreas de intervención', 'servicios_title', 'Áreas de intervención'],
    ['servicios', 'Acompañamiento <span>especializado</span>', null, null], // skip modern (has span)
    // Terapias
    ['terapias', 'Elige el enfoque que necesitas', 'especialidades_title', 'Elige el enfoque que necesitas'],
    ['terapias', 'Enfoques disponibles', 'especialidades_title', 'Enfoques disponibles'],
    ['terapias', 'Enfoques <span>terapéuticos</span>', null, null],
    // Blog
    ['blog', 'Lecturas recomendadas', 'blog_title', 'Lecturas recomendadas'],
    ['blog', 'Últimos artículos', 'blog_title', 'Últimos artículos'],
    ['blog', 'Lecturas <span>recomendadas</span>', null, null],
    // FAQ
    ['faq', 'Resolvemos tus dudas', 'faq_title', 'Resolvemos tus dudas'],
    ['faq', 'Resolvemos <span>tus dudas</span>', null, null],
    // Planes
    ['planes', 'Planes y precios', 'planes_title', 'Planes y precios'],
    ['planes', 'Planes y <span>precios</span>', null, null],
    // Cita
    ['cita', 'Pide tu cita', 'cita_title', 'Pide tu cita'],
    ['cita', 'Pide tu <span>cita</span>', null, null],
    // Sobre mí
    ['sobre-mi', 'Una mirada profesional, un trato humano', 'about_title', 'Una mirada profesional, un trato humano'],
    ['sobre-mi', 'Tu proceso terapéutico, paso a paso', 'about_title', 'Tu proceso terapéutico, paso a paso'],
    ['sobre-mi', 'Una mirada <span>cercana y profesional</span>', null, null],
];

$themes = ['tema-clinica', 'tema-minimal', 'tema-modern'];
$count = 0;

foreach ($themes as $theme) {
    foreach ($repls as [$seccion, $needle, $key, $fallback]) {
        if ($key === null) continue;
        $file = "{$themesRoot}/{$theme}/views/partials/section-{$seccion}.blade.php";
        if (!file_exists($file)) continue;
        $content = file_get_contents($file);
        $replacement = "{{ phrase('{$key}', '{$fallback}') }}";
        if (str_contains($content, $needle)) {
            $newContent = str_replace($needle, $replacement, $content);
            if ($newContent !== $content) {
                file_put_contents($file, $newContent);
                $count++;
                echo "  [$theme/$seccion] Reemplazado: {$needle}\n";
            }
        }
    }
}

echo "Total reemplazos: {$count}\n";
