<?php

namespace Database\Seeders;

use App\Models\Disciplina;
use App\Models\Estudiante;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;
use Carbon\Carbon;

class DisciplinaSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('es_ES');

        $tiposFalta = ['leve', 'moderado', 'grave', 'muy_grave'];
        $cargos = ['Docente', 'Rector', 'Coordinador de convivencia'];
        $estados = ['Registrado', 'Revisado', 'Sancionado', 'Cerrado', 'En apelación'];
        $sanciones = [null, 'Llamado de atención', 'Trabajo social', 'Suspensión 1 día', 'Suspensión 3 días', 'Compromiso escrito'];

        $estudiantes = Estudiante::with('persona', 'curso')->get();
        if ($estudiantes->isEmpty()) {
            $this->command?->warn('DisciplinaSeeder: No hay estudiantes para generar casos.');
            return;
        }

        foreach ($estudiantes as $est) {
            // crea entre 0 y 6 casos por estudiante
            $n = random_int(0, 6);
            for ($i = 0; $i < $n; $i++) {
                $tipo = $faker->randomElement($tiposFalta);
                $estado = $faker->randomElement($estados);
                $fecha = Carbon::now()->subDays(random_int(0, 120))->format('Y-m-d');
                $notiEst = $faker->boolean(70);
                $notiAcu = $faker->boolean(60);
                $confAcu = $notiAcu ? $faker->boolean(70) : false;

                Disciplina::create([
                    'estudiante_id' => $est->idEstudiante,
                    'tipo_falta' => $tipo,
                    'descripcion' => $faker->sentence(12) . ' ' . $faker->sentence(10),
                    'fecha' => $fecha,
                    'notificado_acudiente' => $notiAcu,
                    'notificado_estudiante' => $notiEst,
                    'presentador_nombre' => $faker->name(),
                    'presentador_cargo' => $faker->randomElement($cargos),
                    'estado' => $estado,
                    'tipo_sancion' => $faker->randomElement($sanciones),
                    'confirmacion_acudiente' => $confAcu,
                    'respuesta_acudiente' => $confAcu ? $faker->sentence(15) : null,
                    'observaciones' => $faker->boolean(40) ? $faker->sentence(10) : null,
                ]);
            }
        }

        $this->command?->info('DisciplinaSeeder: Casos disciplinarios generados.');
    }
}
