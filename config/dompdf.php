<?php

/*
|--------------------------------------------------------------------------
| Configuración de dompdf
|--------------------------------------------------------------------------
|
| Heredamos la configuración por defecto del paquete y solo sobreescribimos
| "public_path". En producción la carpeta pública puede llamarse
| "public_html" y estar fuera del proyecto, por lo que base_path('public')
| no existe y dompdf lanza "Cannot resolve public path". Apuntamos a un
| directorio que siempre se puede resolver (no usamos assets externos en
| los PDF, así que el valor solo necesita existir).
|
*/

$config = require base_path('vendor/barryvdh/laravel-dompdf/config/dompdf.php');

$config['public_path'] = is_dir(base_path('public'))
    ? base_path('public')
    : base_path();

return $config;
