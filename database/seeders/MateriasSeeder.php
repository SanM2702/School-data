<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Curso;
use App\Models\Materia;
use App\Models\Docente;

class MateriasSeeder extends Seeder
{
    public function run(): void
    {
        // Buscar cursos del grado "Primero" y crear Materia "Matematicas" por cada grupo
        $cursosPrimero = Curso::where('grado', 'Primero')->get();
        if ($cursosPrimero->isEmpty()) {
            $this->command?->warn('No hay cursos de "Primero". Ejecuta CursosSeeder primero.');
            return;
        }

        // Tomar algún docente disponible para asignar
        $docente = Docente::with('persona')->first();

        foreach ($cursosPrimero as $curso) {
            Materia::updateOrCreate(
                [
                    'nombre' => 'Matematicas',
                    'curso_id' => $curso->idCurso,
                ],
                [
                    'nombre' => 'Matematicas',
                    'curso_id' => $curso->idCurso,
                    'docente_id' => $docente?->idDocente,
                    'codigo' => 'MAT-'.($curso->codigo ?? ($curso->idCurso)),
                    'descripcion' => 'Matemáticas para '.$curso->grado.' '.$curso->grupo,
                ]
            );

            $this->command?->info("Materia Matematicas creada/actualizada para {$curso->grado} {$curso->grupo}");
        }
    }
}
