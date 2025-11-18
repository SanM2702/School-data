<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Curso;
use App\Models\Persona;
use App\Models\Estudiante;
use App\Models\User;
use App\Models\RolesModel;
use Illuminate\Support\Facades\Hash;

class CursosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Configuración: grados, grupos y cantidad de estudiantes por curso
        $grados = ['Primero', 'Segundo', 'Tercero', 'Cuarto', 'Quinto'];
        $grupos = ['A', 'B', 'C'];
        $studentsPerCourse = 5;

        $rolEstudiante = RolesModel::where('nombre', 'Estudiante')->first();
        $defaultPassword = 'estudiante123';

        foreach ($grados as $gIndex => $grado) {
            foreach ($grupos as $grupo) {
                // Generar un codigo simple, p.ej. 1A, 2B, etc.
                $codigo = ($gIndex + 1) . $grupo;

                $curso = Curso::updateOrCreate(
                    ['nombre' => $grado, 'grupo' => $grupo],
                    ['nombre' => $grado, 'grado' => $grado, 'grupo' => $grupo, 'codigo' => $codigo, 'descripcion' => "$grado $grupo"]
                );

                // Crear estudiantes de ejemplo por curso
                for ($i = 1; $i <= $studentsPerCourse; $i++) {
                    $slugGrade = strtolower(str_replace(' ', '.', $grado));
                    $email = "{$slugGrade}.{$grupo}.est{$i}@example.local";

                    // Generar un número de documento único por persona para cumplir la restricción NOT NULL + UNIQUE
                    $generatedDocumento = 'ND' . mt_rand(10000000, 99999999) . $curso->idCurso . $i . time();

                    $persona = Persona::firstOrCreate(
                        ['email' => $email],
                        [
                            'primerNombre' => "Estudiante{$i}",
                            'segundoNombre' => null,
                            'primerApellido' => $curso->codigo,
                            'segundoApellido' => null,
                            'telefono' => null,
                            'noDocumento' => $generatedDocumento,
                            'fechaNacimiento' => null,
                            'estado' => true,
                        ]
                    );

                    $estudiante = Estudiante::firstOrCreate(
                        ['idPersona' => $persona->idPersona],
                        [
                            'fechaIngreso' => now()->toDateString(),
                            'curso_id' => $curso->idCurso,
                        ]
                    );

                    // Crear usuario asociado si no existe
                    $user = User::firstOrCreate(
                        ['email' => $persona->email],
                        [
                            'name' => trim($persona->primerNombre . ' ' . $persona->primerApellido),
                            'email' => $persona->email,
                            'password' => Hash::make($defaultPassword),
                            'roles_id' => $rolEstudiante ? $rolEstudiante->id : null,
                            'email_verified_at' => now(),
                        ]
                    );
                }

                $this->command->info("Curso creado/actualizado: {$curso->nombre} {$curso->grupo}");
            }
        }
    }
}
