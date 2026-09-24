<?php
// Script para generar seed-demo.sql en la raíz del proyecto
// Ejecutar: php cms/database/seeders/gen-sql.php

$out = "-- PsicoCMS · Datos demo (50 pacientes, 200 citas, 100 historias, 30 articulos, 6 categorias, 8 servicios, 8 terapias, 10 faqs)\n";
$out .= "-- Ejecutar: mysql -u USUARIO -p NOMBRE_BBDD < seed-demo.sql\n";
$out .= "-- O importar via phpMyAdmin\n\n";
$out .= "SET FOREIGN_KEY_CHECKS=0;\n";
$out .= "SET SQL_MODE='';\n\n";
$out .= "-- Limpiar con DELETE (compatible con tablas referenciadas por FK)\n";
$out .= "DELETE FROM historia_archivos;\n";
$out .= "DELETE FROM historias;\n";
$out .= "DELETE FROM citas;\n";
$out .= "DELETE FROM pacientes;\n";
$out .= "DELETE FROM articulos;\n";
$out .= "DELETE FROM categorias_blog;\n";
$out .= "DELETE FROM faqs;\n";
$out .= "DELETE FROM servicios;\n";
$out .= "DELETE FROM terapias;\n\n";
$out .= "-- Reiniciar AUTO_INCREMENT\n";
$out .= "ALTER TABLE historia_archivos AUTO_INCREMENT = 1;\n";
$out .= "ALTER TABLE historias AUTO_INCREMENT = 1;\n";
$out .= "ALTER TABLE citas AUTO_INCREMENT = 1;\n";
$out .= "ALTER TABLE pacientes AUTO_INCREMENT = 1;\n";
$out .= "ALTER TABLE articulos AUTO_INCREMENT = 1;\n";
$out .= "ALTER TABLE categorias_blog AUTO_INCREMENT = 1;\n";
$out .= "ALTER TABLE faqs AUTO_INCREMENT = 1;\n";
$out .= "ALTER TABLE servicios AUTO_INCREMENT = 1;\n";
$out .= "ALTER TABLE terapias AUTO_INCREMENT = 1;\n\n";

$now = '2026-05-11 12:00:00';
$q = function ($s) {
    return "'" . str_replace("'", "''", (string) $s) . "'";
};
$asciiSlug = function ($s) {
    $map = ['á' => 'a','é' => 'e','í' => 'i','ó' => 'o','ú' => 'u','ñ' => 'n','Á' => 'a','É' => 'e','Í' => 'i','Ó' => 'o','Ú' => 'u','Ñ' => 'n',' ' => '-'];
    return strtolower(strtr($s, $map));
};

// Servicios
$servicios = [
    ['Terapia individual','Sesiones personalizadas para adultos.','fa-user'],
    ['Terapia de pareja','Acompañamiento para superar conflictos.','fa-people-arrows'],
    ['Terapia familiar','Resolución de dinámicas familiares.','fa-people-roof'],
    ['Apoyo psicológico online','Atención desde casa por videollamada.','fa-laptop'],
    ['Mindfulness y meditación','Técnicas de atención plena.','fa-spa'],
    ['Terapia para adolescentes','Espacio seguro para jóvenes 12-18.','fa-user-graduate'],
    ['Duelo y pérdida','Acompañamiento en procesos de pérdida.','fa-dove'],
    ['Coaching personal','Desarrollo de objetivos personales.','fa-bullseye'],
];
$rows = [];
foreach ($servicios as $i => [$t,$d,$ic]) {
    $rows[] = '(' . $q($t) . ',' . $q($d) . ',' . $q($ic) . ',' . $i . ',1,' . $q($now) . ',' . $q($now) . ')';
}
$out .= "INSERT INTO servicios (titulo,descripcion,icono,orden,activo,created_at,updated_at) VALUES\n" . implode(",\n", $rows) . ";\n\n";

// Terapias / especialidades
$terapias = ['Ansiedad y estrés','Depresión','Trastornos de alimentación','Autoestima','Trauma y duelo','Terapia cognitivo-conductual','Mindfulness terapéutico','Trastornos del sueño'];
$rows = [];
foreach ($terapias as $i => $t) {
    $rows[] = '(' . $q($t) . ',' . $q("Especialidad en {$t}: abordaje profesional con técnicas validadas.") . ',' . $i . ',1,' . $q($now) . ',' . $q($now) . ')';
}
$out .= "INSERT INTO terapias (titulo,descripcion,orden,activo,created_at,updated_at) VALUES\n" . implode(",\n", $rows) . ";\n\n";

// FAQs
$faqs = [
    ['¿Cuánto dura una sesión?','Las sesiones duran entre 50 y 60 minutos.'],
    ['¿Online o presencial?','Ofrecemos ambas modalidades.'],
    ['¿Cuántas sesiones necesito?','Depende del caso, lo evaluamos en la primera sesión.'],
    ['¿Cómo reservar una cita?','Por web, teléfono o WhatsApp.'],
    ['¿Cuál es la confidencialidad?','Protegida por secreto profesional.'],
    ['¿Aceptáis tarjeta?','Tarjeta, Bizum, transferencia y efectivo.'],
    ['¿Hay seguimiento entre sesiones?','Sí, comunicación cuando sea necesario.'],
    ['¿La online es efectiva?','Eficacia similar a presencial según estudios.'],
    ['¿Cuándo veré resultados?','Cambios notables desde las primeras semanas.'],
    ['¿Trabajáis con adolescentes?','Sí, desde los 12 años.'],
];
$rows = [];
foreach ($faqs as $i => [$p,$r]) {
    $rows[] = '(' . $q($p) . ',' . $q($r) . ',' . $i . ',1,' . $q($now) . ',' . $q($now) . ')';
}
$out .= "INSERT INTO faqs (pregunta,respuesta,orden,activa,created_at,updated_at) VALUES\n" . implode(",\n", $rows) . ";\n\n";

// Categorías blog
$cats = ['Ansiedad','Depresión','Autoestima','Relaciones','Mindfulness','Bienestar'];
$rows = [];
foreach ($cats as $i => $c) {
    $rows[] = '(' . ($i+1) . ',' . $q($c) . ',' . $q($asciiSlug($c)) . ',' . $q("Artículos sobre {$c}.") . ',' . $i . ',' . $q($now) . ',' . $q($now) . ')';
}
$out .= "INSERT INTO categorias_blog (id,nombre,slug,descripcion,orden,created_at,updated_at) VALUES\n" . implode(",\n", $rows) . ";\n\n";

// Artículos (30)
$titulos = ['Cómo gestionar la ansiedad','Señales de la depresión','Trabajar la autoestima','Comunicación en pareja','Mindfulness para principiantes','Hábitos para el bienestar','Superar el insomnio','Respiración consciente','Afrontar una pérdida','Estrés laboral','Construir relaciones sanas','Inteligencia emocional infantil','Mitos sobre la psicoterapia','Cuándo pedir ayuda','Tristeza vs depresión','Técnicas de relajación','Poner límites sin culpa','Reducir la rumiación','Apoyo emocional','La importancia del autocuidado','Manejo de la ira','Pensamientos automáticos','Convivir con incertidumbre','Ataques de pánico','Padres primerizos','Salud mental adolescente','Elegir un buen psicólogo','Duelo familiar','Resiliencia interior','Cierre emocional del año'];
$rows = [];
foreach ($titulos as $i => $t) {
    $slug = $asciiSlug($t) . '-' . ($i+1);
    $estado = ($i % 7 === 0) ? 'borrador' : 'publicado';
    $pub = $estado === 'publicado' ? date('Y-m-d H:i:s', strtotime('-' . rand(1, 365) . ' days')) : null;
    $catId = ($i % 6) + 1;
    $contenido = "<p>{$t}.</p><p>La salud mental es fundamental para el bienestar. En este artículo abordamos cómo afrontar esta cuestión.</p><p>Si te identificas con algún punto, consulta a un profesional.</p>";
    $pubSql = $pub === null ? 'NULL' : $q($pub);
    $rows[] = '(' . $catId . ',' . $q($t) . ',' . $q($slug) . ',' . $q("Reflexiones sobre {$t}.") . ',' . $q($contenido) . ',' . $q($estado) . ',' . $pubSql . ',' . $q($t) . ',' . $q("Artículo sobre {$t}.") . ',' . $q($now) . ',' . $q($now) . ')';
}
$out .= "INSERT INTO articulos (categoria_id,titulo,slug,extracto,contenido,estado,published_at,meta_title,meta_description,created_at,updated_at) VALUES\n" . implode(",\n", $rows) . ";\n\n";

// Pacientes (50)
$nombres = ['Antonio','María','Carlos','Lucía','Javier','Carmen','Pedro','Laura','Miguel','Ana','Jorge','Elena','Pablo','Sara','David','Marta','Luis','Isabel','Daniel','Patricia','Alejandro','Sofía','Manuel','Cristina','Andrés','Beatriz','Raúl','Natalia','Adrián','Paula','Iván','Sandra','Sergio','Eva','Rubén','Silvia','Óscar','Rocío','Álvaro','Irene','Jesús','Mónica','Diego','Pilar','Fernando','Nuria','Hugo','Alicia','Mario','Verónica'];
$apellidos = ['García','Martínez','López','Sánchez','Pérez','González','Rodríguez','Fernández','Ruiz','Jiménez','Hernández','Díaz','Moreno','Álvarez','Romero','Alonso','Gutiérrez','Navarro','Torres','Domínguez'];
$generos = ['mujer','hombre','otro','prefiero_no_decir'];
$ciudades = ['Madrid','Barcelona','Valencia','Sevilla','Zaragoza'];
$rows = [];
for ($i = 0; $i < 50; $i++) {
    $n = $nombres[$i];
    $ap = $apellidos[$i % count($apellidos)];
    $tel = '+346' . str_pad((string) (10000000 + $i * 137), 8, '0', STR_PAD_LEFT);
    $emailN = strtolower(strtr($n, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n','Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ú'=>'u','Ñ'=>'n']));
    $emailAp = strtolower(strtr($ap, ['á'=>'a','é'=>'e','í'=>'i','ó'=>'o','ú'=>'u','ñ'=>'n','Á'=>'a','É'=>'e','Í'=>'i','Ó'=>'o','Ú'=>'u','Ñ'=>'n']));
    $email = $emailN . '.' . $emailAp . $i . '@email.com';
    $nac = date('Y-m-d', strtotime('-' . rand(20, 70) . ' years'));
    $gen = $generos[$i % 4];
    $dir = 'Calle ' . rand(1, 100) . ', ' . $ciudades[$i % 5];
    $origen = ($i % 3 === 0) ? 'publica' : 'manual';
    $rows[] = '(' . ($i+1) . ',' . $q($n) . ',' . $q($ap) . ',' . $q($tel) . ',' . $q($email) . ',' . $q($nac) . ',' . $q($gen) . ',' . $q($dir) . ',' . $q('Motivo inicial: ansiedad y gestión del estrés.') . ',' . $q($origen) . ',' . $q($now) . ',' . $q($now) . ')';
}
$out .= "INSERT INTO pacientes (id,nombre,apellidos,telefono,email,fecha_nacimiento,genero,direccion,motivo_inicial,origen,created_at,updated_at) VALUES\n" . implode(",\n", $rows) . ";\n\n";

// Citas (200)
$modalidades = ['online','presencial'];
$estadosPasado = ['confirmada','realizada','realizada','no_asistio','cancelada','realizada'];
$rows = [];
for ($i = 0; $i < 200; $i++) {
    $pid = ($i % 50) + 1;
    $offset = rand(-365, 90);
    $h = rand(9, 18);
    $m = (rand(0, 1) ? 0 : 30);
    $fecha = date('Y-m-d', strtotime("$offset days")) . sprintf(' %02d:%02d:00', $h, $m);
    $fin = date('Y-m-d H:i:s', strtotime("$fecha +60 minutes"));
    $estado = ($offset > 0) ? 'confirmada' : $estadosPasado[$i % count($estadosPasado)];
    $modalidad = $modalidades[$i % 2];
    $motivo = ($i % 5 === 0) ? 'Seguimiento mensual.' : null;
    $nombre = $nombres[($pid-1) % count($nombres)] . ' ' . $apellidos[($pid-1) % count($apellidos)];
    $tel = '+346' . str_pad((string) (10000000 + ($pid-1) * 137), 8, '0', STR_PAD_LEFT);
    $motivoSql = $motivo === null ? 'NULL' : $q($motivo);
    $rows[] = '(' . $pid . ',' . $q($nombre) . ',' . $q($tel) . ',' . $q($modalidad) . ',' . $q($fecha) . ',' . $q($fin) . ',' . $motivoSql . ',' . $q($estado) . ',' . "'manual'" . ',' . $q($now) . ',' . $q($now) . ')';
}
$out .= "INSERT INTO citas (paciente_id,nombre_provisional,telefono_provisional,modalidad,fecha_inicio,fecha_fin,motivo,estado,origen,created_at,updated_at) VALUES\n" . implode(",\n", $rows) . ";\n\n";

// Historias (100)
$rows = [];
for ($i = 0; $i < 100; $i++) {
    $pid = ($i % 50) + 1;
    $fecha = date('Y-m-d', strtotime('-' . rand(1, 500) . ' days'));
    $contenido = '<p>Resumen de la sesión nº ' . ($i+1) . ' del paciente.</p><p>Se trataron temas de ansiedad, autoestima y dinámica familiar. Evolución positiva.</p>';
    $rows[] = '(' . $pid . ',' . $q($fecha) . ',' . $q('Sesión nº ' . ($i+1)) . ',' . $q($contenido) . ',' . $q($now) . ',' . $q($now) . ')';
}
$out .= "INSERT INTO historias (paciente_id,fecha_sesion,titulo,contenido,created_at,updated_at) VALUES\n" . implode(",\n", $rows) . ";\n\n";

$out .= "SET FOREIGN_KEY_CHECKS=1;\n";

$outPath = __DIR__ . '/../../../seed-demo.sql';
file_put_contents($outPath, $out);
echo "Escrito " . realpath($outPath) . " (" . strlen($out) . " bytes)\n";
