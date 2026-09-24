<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class DemoDataSeeder extends Seeder
{
    public function run(): void
    {
        DB::statement('SET FOREIGN_KEY_CHECKS=0');

        // Limpiar
        DB::table('historias')->truncate();
        DB::table('citas')->truncate();
        DB::table('pacientes')->truncate();
        DB::table('articulos')->truncate();
        DB::table('categorias_blog')->truncate();
        DB::table('faqs')->truncate();
        DB::table('servicios')->truncate();
        DB::table('terapias')->truncate();

        $this->seedServicios();
        $this->seedTerapias();
        $this->seedFaqs();
        $this->seedCategorias();
        $this->seedArticulos();
        $this->seedPacientes();
        $this->seedCitas();
        $this->seedHistorias();

        DB::statement('SET FOREIGN_KEY_CHECKS=1');
    }

    private function seedServicios(): void
    {
        $items = [
            ['Terapia individual', 'Sesiones personalizadas para adultos.', 'fa-user'],
            ['Terapia de pareja', 'Acompañamiento para superar conflictos en la relación.', 'fa-people-arrows'],
            ['Terapia familiar', 'Resolución de dinámicas familiares complejas.', 'fa-people-roof'],
            ['Apoyo psicológico online', 'Atención profesional desde casa por videollamada.', 'fa-laptop'],
            ['Mindfulness y meditación', 'Técnicas de atención plena para reducir estrés.', 'fa-spa'],
            ['Terapia para adolescentes', 'Espacio seguro para jóvenes entre 12 y 18 años.', 'fa-user-graduate'],
            ['Duelo y pérdida', 'Acompañamiento en procesos de pérdida significativa.', 'fa-dove'],
            ['Coaching personal', 'Desarrollo de objetivos personales y profesionales.', 'fa-bullseye'],
        ];
        foreach ($items as $i => [$t, $d, $ic]) {
            DB::table('servicios')->insert([
                'titulo' => $t, 'descripcion' => $d, 'icono' => $ic,
                'orden' => $i, 'activo' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function seedTerapias(): void
    {
        $items = [
            'Ansiedad y estrés', 'Depresión', 'Trastornos de la alimentación',
            'Autoestima', 'Trauma y duelo', 'Terapia cognitivo-conductual',
            'Mindfulness terapéutico', 'Trastornos del sueño',
        ];
        foreach ($items as $i => $t) {
            DB::table('terapias')->insert([
                'titulo' => $t,
                'descripcion' => "Especialidad en {$t}: abordaje profesional con técnicas validadas científicamente para mejorar el bienestar emocional del paciente.",
                'orden' => $i, 'activo' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function seedFaqs(): void
    {
        $items = [
            ['¿Cuánto dura una sesión?', 'Las sesiones tienen una duración aproximada de 50 a 60 minutos.'],
            ['¿Puedo elegir entre presencial y online?', 'Sí, ofrecemos ambas modalidades para adaptarnos a tus necesidades.'],
            ['¿Cuántas sesiones necesitaré?', 'Depende de cada caso; lo evaluamos juntos en la primera sesión.'],
            ['¿Cómo puedo reservar una cita?', 'Puedes hacerlo a través del formulario web, por teléfono o WhatsApp.'],
            ['¿Cuál es la confidencialidad?', 'Todo lo tratado en consulta está protegido por el secreto profesional.'],
            ['¿Aceptáis tarjeta?', 'Sí, aceptamos pago con tarjeta, Bizum, transferencia y efectivo.'],
            ['¿Hay seguimiento entre sesiones?', 'Sí, podemos coordinar comunicación entre sesiones cuando sea necesario.'],
            ['¿La terapia online es igual de efectiva?', 'La evidencia muestra eficacia similar a la presencial en muchos casos.'],
            ['¿Cuándo veré resultados?', 'Cada proceso es único; suelen notarse cambios desde las primeras semanas.'],
            ['¿Trabajáis con adolescentes?', 'Sí, contamos con especialistas en terapia para adolescentes desde 12 años.'],
        ];
        foreach ($items as $i => [$q, $a]) {
            DB::table('faqs')->insert([
                'pregunta' => $q, 'respuesta' => $a,
                'orden' => $i, 'activa' => 1,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function seedCategorias(): void
    {
        $cats = ['Ansiedad', 'Depresión', 'Autoestima', 'Relaciones', 'Mindfulness', 'Bienestar'];
        foreach ($cats as $i => $c) {
            DB::table('categorias_blog')->insert([
                'nombre' => $c, 'slug' => Str::slug($c),
                'descripcion' => "Artículos sobre {$c}.",
                'orden' => $i,
                'created_at' => now(), 'updated_at' => now(),
            ]);
        }
    }

    private function seedArticulos(): void
    {
        $titulos = [
            'Cómo gestionar la ansiedad en el día a día',
            'Señales de alerta en la depresión',
            'Trabajar la autoestima paso a paso',
            'Comunicación efectiva en pareja',
            'Introducción al mindfulness para principiantes',
            'Hábitos diarios para el bienestar emocional',
            'Estrategias para superar el insomnio',
            'El poder de la respiración consciente',
            'Cómo afrontar una pérdida significativa',
            'Reconocer el estrés laboral',
            'Construir relaciones sanas',
            'Inteligencia emocional en niños',
            'Mitos sobre la psicoterapia',
            'Cuándo es el momento de pedir ayuda',
            'Diferencias entre tristeza y depresión',
            'Técnicas de relajación rápidas',
            'Cómo poner límites sin sentir culpa',
            'Vivir el presente y reducir la rumiación',
            'Apoyo emocional en momentos difíciles',
            'La importancia del autocuidado',
            'Manejo de la ira',
            'Cómo identificar pensamientos automáticos',
            'Convivir con incertidumbre',
            'Estrategias contra los ataques de pánico',
            'Recursos para padres primerizos',
            'Adolescencia y salud mental',
            'Cómo elegir un buen psicólogo',
            'El duelo en la familia',
            'Resiliencia: cultivar la fortaleza interior',
            'Despedida del año: cierre emocional',
        ];

        foreach ($titulos as $i => $titulo) {
            $slug = Str::slug($titulo) . '-' . ($i + 1);
            $estado = $i % 7 === 0 ? 'borrador' : 'publicado';
            $publishedAt = $estado === 'publicado' ? Carbon::now()->subDays(rand(1, 365)) : null;
            $categoriaId = ($i % 6) + 1;

            DB::table('articulos')->insert([
                'categoria_id' => $categoriaId,
                'titulo' => $titulo,
                'slug' => $slug,
                'extracto' => "Reflexiones y consejos sobre {$titulo}.",
                'contenido' => "<p>{$titulo}.</p><p>La salud mental es fundamental para el bienestar integral. En este artículo exploramos cómo abordar esta cuestión desde una perspectiva profesional y empática.</p><p>Si te identificas con alguno de los puntos descritos, no dudes en consultar a un profesional.</p>",
                'imagen_path' => null,
                'estado' => $estado,
                'published_at' => $publishedAt,
                'meta_title' => $titulo,
                'meta_description' => "Lee sobre {$titulo} en nuestro blog de psicología.",
                'created_at' => now()->subDays(rand(1, 400)),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedPacientes(): void
    {
        $nombres = ['Antonio','María','Carlos','Lucía','Javier','Carmen','Pedro','Laura','Miguel','Ana','Jorge','Elena','Pablo','Sara','David','Marta','Luis','Isabel','Daniel','Patricia','Alejandro','Sofía','Manuel','Cristina','Andrés','Beatriz','Raúl','Natalia','Adrián','Paula','Iván','Sandra','Sergio','Eva','Rubén','Silvia','Óscar','Rocío','Álvaro','Irene','Jesús','Mónica','Diego','Pilar','Fernando','Nuria','Hugo','Alicia','Mario','Verónica'];
        $apellidos = ['García','Martínez','López','Sánchez','Pérez','González','Rodríguez','Fernández','Ruiz','Jiménez','Hernández','Díaz','Moreno','Álvarez','Romero','Alonso','Gutiérrez','Navarro','Torres','Domínguez','Vázquez','Ramos','Gil','Ramírez','Serrano','Blanco','Suárez','Castro','Ortega','Rubio'];
        $generos = ['mujer','hombre','otro','prefiero_no_decir'];

        for ($i = 0; $i < 50; $i++) {
            $nombre = $nombres[$i % count($nombres)];
            $apellido = $apellidos[$i % count($apellidos)];
            $telefono = '+346' . str_pad((string) (10000000 + $i * 137 + rand(0, 999)), 8, '0', STR_PAD_LEFT);
            $email = strtolower(Str::ascii($nombre)) . '.' . strtolower(Str::ascii($apellido)) . $i . '@email.com';
            $fechaNac = Carbon::now()->subYears(rand(20, 70))->subDays(rand(0, 364))->format('Y-m-d');

            DB::table('pacientes')->insert([
                'nombre' => $nombre,
                'apellidos' => $apellido . ($i > count($apellidos) ? ' ' . $apellidos[rand(0, count($apellidos)-1)] : ''),
                'telefono' => $telefono,
                'email' => $email,
                'fecha_nacimiento' => $fechaNac,
                'genero' => $generos[array_rand($generos)],
                'direccion' => 'Calle ' . rand(1, 100) . ', ' . ['Madrid','Barcelona','Valencia','Sevilla','Zaragoza'][array_rand([0,1,2,3,4])],
                'motivo_inicial' => 'Motivo inicial: ansiedad y gestión del estrés.',
                'notas' => null,
                'origen' => $i % 3 === 0 ? 'publica' : 'manual',
                'created_at' => now()->subDays(rand(1, 600)),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedCitas(): void
    {
        $modalidades = ['online','presencial'];
        $estados = ['confirmada','realizada','no_asistio','pendiente','cancelada'];
        $totalPacientes = 50;

        for ($i = 0; $i < 200; $i++) {
            $pacienteId = ($i % $totalPacientes) + 1;
            $diasOffset = rand(-365, 90);
            $fecha = Carbon::now()->addDays($diasOffset)->setTime(rand(9, 19), [0, 30][rand(0, 1)], 0);
            $finFecha = (clone $fecha)->addMinutes(60);

            $estado = $diasOffset > 0 ? (rand(0, 10) > 1 ? 'confirmada' : 'pendiente') : $estados[array_rand([0,1,1,1,2,3])];
            $modalidad = $modalidades[array_rand($modalidades)];

            DB::table('citas')->insert([
                'paciente_id' => $pacienteId,
                'nombre_provisional' => DB::table('pacientes')->where('id', $pacienteId)->value('nombre') . ' ' . DB::table('pacientes')->where('id', $pacienteId)->value('apellidos'),
                'telefono_provisional' => DB::table('pacientes')->where('id', $pacienteId)->value('telefono'),
                'email_provisional' => null,
                'modalidad' => $modalidad,
                'fecha_inicio' => $fecha,
                'fecha_fin' => $finFecha,
                'motivo' => $i % 5 === 0 ? 'Seguimiento mensual.' : null,
                'estado' => $estado,
                'notas_internas' => null,
                'origen' => 'manual',
                'created_at' => $fecha->copy()->subDays(rand(1, 30)),
                'updated_at' => now(),
            ]);
        }
    }

    private function seedHistorias(): void
    {
        for ($i = 0; $i < 100; $i++) {
            $pacienteId = ($i % 50) + 1;
            $fechaSesion = Carbon::now()->subDays(rand(1, 500))->format('Y-m-d');
            DB::table('historias')->insert([
                'paciente_id' => $pacienteId,
                'fecha_sesion' => $fechaSesion,
                'titulo' => 'Sesión nº ' . ($i + 1),
                'contenido' => "<p>Resumen de la sesión nº " . ($i+1) . " del paciente.</p><p>Se trataron temas relacionados con la ansiedad, autoestima y dinámica familiar. El paciente muestra una evolución positiva.</p><p><strong>Tareas asignadas:</strong> ejercicios de respiración diaria, registro de pensamientos.</p>",
                'created_at' => $fechaSesion,
                'updated_at' => now(),
            ]);
        }
    }
}
