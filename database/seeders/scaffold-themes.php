<?php
// Scaffold de 5 nuevos temas - renombra clases t-clinica → prefix de cada tema
// y aplica paletas distintivas.

$themes = [
    'tema-violeta' => [
        'prefix' => 't-violeta',
        'palette' => [
            '--color-primary' => '#7c5cff',
            '--color-primary-dark' => '#5f3fd4',
            '--color-secondary' => '#9d80ff',
            '--color-accent' => '#ede8ff',
            '--color-bg' => '#ffffff',
            '--color-bg-alt' => '#f6f5fb',
            '--color-text' => '#1d1b27',
            '--color-text-light' => '#6a6679',
            '--color-border' => '#e5e0f8',
            '--shadow-sm' => '0 2px 12px rgba(124,92,255,0.08)',
            '--shadow-md' => '0 8px 32px rgba(124,92,255,0.12)',
            '--shadow-lg' => '0 20px 60px rgba(124,92,255,0.18)',
        ],
    ],
    'tema-sage' => [
        'prefix' => 't-sage',
        'palette' => [
            '--color-primary' => '#5b6f5a',
            '--color-primary-dark' => '#3e503e',
            '--color-secondary' => '#8aa28b',
            '--color-accent' => '#dfe6dd',
            '--color-bg' => '#f5f1e8',
            '--color-bg-alt' => '#eae4d3',
            '--color-text' => '#2b2e2a',
            '--color-text-light' => '#6c6f6a',
            '--color-border' => '#ddd6c6',
            '--shadow-sm' => '0 2px 10px rgba(91,111,90,0.10)',
            '--shadow-md' => '0 6px 24px rgba(91,111,90,0.14)',
            '--shadow-lg' => '0 16px 44px rgba(91,111,90,0.20)',
        ],
    ],
    'tema-natural' => [
        'prefix' => 't-natural',
        'palette' => [
            '--color-primary' => '#3e5c41',
            '--color-primary-dark' => '#284029',
            '--color-secondary' => '#74937a',
            '--color-accent' => '#d6e4d2',
            '--color-bg' => '#ffffff',
            '--color-bg-alt' => '#f3f5ef',
            '--color-text' => '#1f2a20',
            '--color-text-light' => '#5e6b5e',
            '--color-border' => '#dce5d9',
            '--shadow-sm' => '0 2px 10px rgba(62,92,65,0.08)',
            '--shadow-md' => '0 8px 28px rgba(62,92,65,0.12)',
            '--shadow-lg' => '0 18px 50px rgba(62,92,65,0.16)',
        ],
    ],
    'tema-organico' => [
        'prefix' => 't-organico',
        'palette' => [
            '--color-primary' => '#56796a',
            '--color-primary-dark' => '#3c5849',
            '--color-secondary' => '#84a896',
            '--color-accent' => '#fce8e1',
            '--color-bg' => '#fef7f4',
            '--color-bg-alt' => '#f9ddd4',
            '--color-text' => '#2d3530',
            '--color-text-light' => '#6b716e',
            '--color-border' => '#f0d8cc',
            '--shadow-sm' => '0 2px 14px rgba(86,121,106,0.10)',
            '--shadow-md' => '0 8px 30px rgba(86,121,106,0.14)',
            '--shadow-lg' => '0 20px 60px rgba(86,121,106,0.18)',
        ],
    ],
    'tema-bold' => [
        'prefix' => 't-bold',
        'palette' => [
            '--color-primary' => '#000000',
            '--color-primary-dark' => '#000000',
            '--color-secondary' => '#ffd233',
            '--color-accent' => '#ec4899',
            '--color-bg' => '#ffffff',
            '--color-bg-alt' => '#fff8d6',
            '--color-text' => '#000000',
            '--color-text-light' => '#3d3d3d',
            '--color-border' => '#000000',
            '--shadow-sm' => '6px 6px 0 #000000',
            '--shadow-md' => '8px 8px 0 #000000',
            '--shadow-lg' => '12px 12px 0 #000000',
        ],
    ],
];

$themesRoot = __DIR__ . '/../../themes';

$renameRecursive = function (string $dir, callable $cb) use (&$renameRecursive) {
    foreach (scandir($dir) as $entry) {
        if ($entry === '.' || $entry === '..') continue;
        $path = $dir . DIRECTORY_SEPARATOR . $entry;
        if (is_dir($path)) {
            $renameRecursive($path, $cb);
        } elseif (preg_match('/\.(css|blade\.php|json|js)$/', $entry)) {
            $cb($path);
        }
    }
};

foreach ($themes as $slug => $cfg) {
    $prefix = $cfg['prefix'];
    $themeDir = $themesRoot . '/' . $slug;
    if (!is_dir($themeDir)) {
        echo "  [SKIP] {$slug} no existe\n";
        continue;
    }

    // Renombrar prefijos de clase t-clinica → prefix
    $renameRecursive($themeDir, function ($file) use ($prefix) {
        $content = file_get_contents($file);
        $new = str_replace('t-clinica', $prefix, $content);
        $new = str_replace('body class="t-clinica"', 'body class="' . $prefix . '"', $new);
        if ($new !== $content) file_put_contents($file, $new);
    });

    // Aplicar paleta
    $cssFile = $themeDir . '/assets/css/styles.css';
    if (file_exists($cssFile)) {
        $css = file_get_contents($cssFile);
        foreach ($cfg['palette'] as $var => $value) {
            $css = preg_replace(
                '/(' . preg_quote($var, '/') . ':\s*)[^;]+;/',
                $var . ': ' . $value . ';',
                $css
            );
        }
        file_put_contents($cssFile, $css);
    }

    echo "  [OK] {$slug}\n";
}
echo "Listo.\n";
