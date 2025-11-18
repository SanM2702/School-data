<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Ejecutar seeders en orden correcto
        $this->call([
            RolesSeeder::class,      // Primero crear los roles
            RectorSeeder::class,     // Luego crear el usuario rector
            PersonaSeeder::class,    // Seed de personas de ejemplo
            CursosSeeder::class,     // Seed de cursos con estudiantes de ejemplo
            EstudianteSeeder::class, // Seed de estudiantes (Santiago)
            DocenteSeeder::class,    // Seed de docentes de ejemplo
            AcudienteSeeder::class,  // Seed de un acudiente con su estudiante
            UsuariosAdministradoresSeeder::class,
            MateriasSeeder::class,   // Seed de materias (Matemáticas por curso/grupo)
        ]);
        
        // Comentado el usuario de prueba para evitar conflictos
        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
    }
}