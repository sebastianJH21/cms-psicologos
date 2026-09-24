<?php
// Aplica botones whatsapp + llamar + pedir cita en navs de temas
$themes = [
    ['slug' => 'tema-violeta', 'prefix' => 't-violeta'],
    ['slug' => 'tema-sage', 'prefix' => 't-sage'],
    ['slug' => 'tema-natural', 'prefix' => 't-natural'],
    ['slug' => 'tema-organico', 'prefix' => 't-organico'],
    ['slug' => 'tema-bold', 'prefix' => 't-bold'],
];

foreach ($themes as $t) {
    $path = __DIR__ . '/../../themes/' . $t['slug'] . '/views/partials/nav.blade.php';
    if (!file_exists($path)) { echo "  SKIP {$t['slug']}\n"; continue; }
    $c = file_get_contents($path);
    $px = $t['prefix'];
    $var = str_replace('-', '_', $t['slug']);

    $oldBlock = "            @if (\$features['faq'] ?? true)\n                <li><a href=\"{{ \$faqUrl }}\">FAQ</a></li>\n            @endif\n            @if (\$features['reservas'] ?? true)\n                <li><a href=\"{{ \$citaUrl }}\" class=\"{$px}__nav-cta\">Pedir cita</a></li>\n            @endif\n        </ul>\n    </div>\n</header>";

    $newBlock = "            @if (\$features['faq'] ?? true)\n                <li><a href=\"{{ \$faqUrl }}\">FAQ</a></li>\n            @endif\n        </ul>\n        @php\n            \$telLimpio_{$var} = preg_replace('/[^0-9+]/', '', \$profile?->telefono_publico ?? '');\n            \$telWa_{$var} = preg_replace('/[^0-9]/', '', \$profile?->telefono_publico ?? '');\n            \$waUrl_{$var} = !empty(\$social['whatsapp']) ? \$social['whatsapp'] : (\$telWa_{$var} ? 'https://wa.me/' . \$telWa_{$var} : null);\n        @endphp\n        <div class=\"{$px}__nav-actions\">\n            @if (\$telLimpio_{$var})<a href=\"tel:{{ \$telLimpio_{$var} }}\" class=\"{$px}__nav-icon\" aria-label=\"Llamar\"><i class=\"fa-solid fa-phone\"></i></a>@endif\n            @if (\$waUrl_{$var})<a href=\"{{ \$waUrl_{$var} }}\" target=\"_blank\" rel=\"noopener\" class=\"{$px}__nav-icon {$px}__nav-icon--wa\" aria-label=\"WhatsApp\"><i class=\"fa-brands fa-whatsapp\"></i></a>@endif\n            @if (\$features['reservas'] ?? true)<a href=\"{{ \$citaUrl }}\" class=\"{$px}__nav-cta\">Pedir cita</a>@endif\n        </div>\n    </div>\n</header>";

    if (str_contains($c, $oldBlock)) {
        $c = str_replace($oldBlock, $newBlock, $c);
        file_put_contents($path, $c);
        echo "  OK {$t['slug']}\n";
    } else {
        echo "  NO MATCH {$t['slug']}\n";
    }
}
echo "Done.\n";
