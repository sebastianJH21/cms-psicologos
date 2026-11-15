<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Gasto;
use App\Models\IngresoManual;
use Illuminate\Database\Seeder;

/**
 * Datos ficticios para probar el panel de Facturación (IMP-002) sin tocar
 * ni vaciar ninguna tabla existente: solo inserta filas nuevas.
 */
class DemoFacturacionSeeder extends Seeder
{
    private const CATEGORIAS_GASTO = [
        'Arriendo consultorio' => ['Arriendo del consultorio', 'Arriendo sala por horas'],
        'Software y herramientas' => ['Plataforma de videollamadas', 'Licencia de pruebas psicométricas', 'Software de historia clínica'],
        'Servicios' => ['Internet y telefonía', 'Energía del consultorio', 'Acueducto y aseo'],
        'Formación y supervisión' => ['Supervisión de casos', 'Curso de actualización profesional'],
        'Publicidad' => ['Pauta en redes sociales', 'Diseño de material gráfico'],
        'Otros' => ['Papelería y material terapéutico', 'Seguro de responsabilidad profesional'],
    ];

    private const CONCEPTOS_INGRESO = [
        'Taller de manejo del estrés',
        'Informe psicológico',
        'Supervisión clínica a colega',
        'Evaluación psicológica',
        'Charla corporativa de bienestar',
        'Taller de habilidades de comunicación',
    ];

    private const NOMBRES = ['Valentina', 'Santiago', 'Mariana', 'Juan Pablo', 'Camila', 'Andrés', 'Laura', 'Sebastián', 'Isabella', 'Daniel', 'Sara', 'Tomás', 'Gabriela', 'Nicolás', 'Valeria'];
    private const APELLIDOS = ['Gómez', 'Restrepo', 'Ochoa', 'Arango', 'Zapata', 'Mejía', 'Cardona', 'Vélez', 'Montoya', 'Muñoz', 'Londoño', 'Henao'];

    public function run(): void
    {
        if (!app()->environment('local')) {
            $this->command?->warn('DemoFacturacionSeeder solo se ejecuta en entorno local.');

            return;
        }

        $this->crearCitasPagadas(90);
        $this->crearIngresosManuales(55);
        $this->crearGastos(55);
    }

    private function fechaAleatoria(): \Illuminate\Support\Carbon
    {
        $fecha = now()->copy()->subMonths(random_int(0, 11))->subDays(random_int(0, 27));

        return $fecha->greaterThan(now()) ? now()->copy()->subDays(random_int(1, 5)) : $fecha;
    }

    private function crearCitasPagadas(int $cantidad): void
    {
        for ($i = 0; $i < $cantidad; $i++) {
            $fecha = $this->fechaAleatoria()->setTime(random_int(8, 18), [0, 15, 30, 45][random_int(0, 3)]);
            $modalidad = random_int(0, 1) ? 'presencial' : 'online';
            $monto = $modalidad === 'presencial' ? random_int(140, 180) * 1000 : random_int(100, 140) * 1000;

            Cita::create([
                'nombre_provisional' => self::NOMBRES[array_rand(self::NOMBRES)] . ' ' . self::APELLIDOS[array_rand(self::APELLIDOS)],
                'telefono_provisional' => '3' . random_int(100000000, 299999999),
                'modalidad' => $modalidad,
                'fecha_inicio' => $fecha,
                'fecha_fin' => $fecha->copy()->addMinutes(50),
                'motivo' => 'Sesión de seguimiento (dato ficticio de prueba).',
                'estado' => 'realizada',
                'pagado' => true,
                'monto' => $monto,
                'pagado_at' => $fecha->copy()->addHours(random_int(0, 6)),
                'origen' => 'manual',
            ]);
        }
    }

    private function crearIngresosManuales(int $cantidad): void
    {
        for ($i = 0; $i < $cantidad; $i++) {
            IngresoManual::create([
                'descripcion' => self::CONCEPTOS_INGRESO[array_rand(self::CONCEPTOS_INGRESO)] . ' (dato ficticio de prueba)',
                'monto' => random_int(150, 900) * 1000,
                'fecha' => $this->fechaAleatoria(),
            ]);
        }
    }

    private function crearGastos(int $cantidad): void
    {
        $categorias = array_keys(self::CATEGORIAS_GASTO);

        for ($i = 0; $i < $cantidad; $i++) {
            $categoria = $categorias[array_rand($categorias)];
            $conceptos = self::CATEGORIAS_GASTO[$categoria];

            Gasto::create([
                'descripcion' => $conceptos[array_rand($conceptos)] . ' (dato ficticio de prueba)',
                'monto' => random_int(40, 1500) * 1000,
                'fecha' => $this->fechaAleatoria(),
                'categoria' => $categoria,
            ]);
        }
    }
}
