<?php

namespace Database\Seeders;

use App\Models\Cita;
use App\Models\Gasto;
use App\Models\IngresoManual;
use Illuminate\Database\Seeder;

/**
 * Refuerza con datos ficticios cada mes entre mayo y el mes actual, para
 * poder probar el selector de mes del panel de Facturación. Solo inserta
 * filas nuevas (no trunca ni modifica nada existente).
 */
class DemoFacturacionRangoSeeder extends Seeder
{
    private const POR_MES_INGRESOS = 20;
    private const POR_MES_GASTOS = 20;
    private const POR_MES_CITAS = 15;

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
        'Acompañamiento a empresa aliada',
    ];

    private const NOMBRES = ['Valentina', 'Santiago', 'Mariana', 'Juan Pablo', 'Camila', 'Andrés', 'Laura', 'Sebastián', 'Isabella', 'Daniel', 'Sara', 'Tomás', 'Gabriela', 'Nicolás', 'Valeria'];
    private const APELLIDOS = ['Gómez', 'Restrepo', 'Ochoa', 'Arango', 'Zapata', 'Mejía', 'Cardona', 'Vélez', 'Montoya', 'Muñoz', 'Londoño', 'Henao'];

    public function run(): void
    {
        if (!app()->environment('local')) {
            $this->command?->warn('DemoFacturacionRangoSeeder solo se ejecuta en entorno local.');

            return;
        }

        $mesInicio = \Carbon\Carbon::create(now()->year, 5, 1)->startOfMonth();
        $mesActual = now()->copy()->startOfMonth();

        $mes = $mesInicio->copy();
        while ($mes->lte($mesActual)) {
            $this->crearIngresosManuales($mes);
            $this->crearGastos($mes);
            $this->crearCitasPagadas($mes);
            $mes->addMonth();
        }
    }

    private function diaAleatorioDelMes(\Carbon\Carbon $mes): \Carbon\Carbon
    {
        $fin = $mes->isSameMonth(now()) ? now()->day : $mes->copy()->endOfMonth()->day;
        $dia = random_int(1, max($fin, 1));

        return $mes->copy()->day($dia);
    }

    private function crearIngresosManuales(\Carbon\Carbon $mes): void
    {
        for ($i = 0; $i < self::POR_MES_INGRESOS; $i++) {
            IngresoManual::create([
                'descripcion' => self::CONCEPTOS_INGRESO[array_rand(self::CONCEPTOS_INGRESO)] . ' (dato ficticio de prueba)',
                'monto' => random_int(150, 900) * 1000,
                'fecha' => $this->diaAleatorioDelMes($mes),
            ]);
        }
    }

    private function crearGastos(\Carbon\Carbon $mes): void
    {
        $categorias = array_keys(self::CATEGORIAS_GASTO);

        for ($i = 0; $i < self::POR_MES_GASTOS; $i++) {
            $categoria = $categorias[array_rand($categorias)];
            $conceptos = self::CATEGORIAS_GASTO[$categoria];

            Gasto::create([
                'descripcion' => $conceptos[array_rand($conceptos)] . ' (dato ficticio de prueba)',
                'monto' => random_int(40, 1500) * 1000,
                'fecha' => $this->diaAleatorioDelMes($mes),
                'categoria' => $categoria,
            ]);
        }
    }

    private function crearCitasPagadas(\Carbon\Carbon $mes): void
    {
        for ($i = 0; $i < self::POR_MES_CITAS; $i++) {
            $fecha = $this->diaAleatorioDelMes($mes)->setTime(random_int(8, 18), [0, 15, 30, 45][random_int(0, 3)]);
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
}
