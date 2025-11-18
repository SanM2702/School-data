<?php

namespace Database\Seeders;

use App\Models\Persona;
use App\Models\Docente;
use App\Models\User;
use App\Models\RolesModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DocenteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear o recuperar una persona ejemplo para el docente
        $persona = Persona::firstOrCreate(
            ['email' => 'laura.gomez@edu.co'],
            [
                'primerNombre' => 'Laura',
                'segundoNombre' => 'María',
                'primerApellido' => 'Gómez',
                'segundoApellido' => 'Ruiz',
                'telefono' => '3105550101',
                'noDocumento' => '1098765432',
                'fechaNacimiento' => '1985-06-20',
                'estado' => true,
            ]
        );

        // Crear docente si no existe
        $docente = Docente::firstOrCreate([
            'idPersona' => $persona->idPersona,
        ], [
            'area' => 'Matemáticas',
        ]);

        // Crear o actualizar un usuario asociado y asignar rol 'Docente'
        $rolDocente = RolesModel::where('nombre', 'Docente')->first();
        $defaultPassword = 'docente123';

        $user = User::updateOrCreate(
            ['email' => $persona->email],
            [
                'name' => trim($persona->primerNombre . ' ' . $persona->primerApellido),
                'email' => $persona->email,
                'password' => Hash::make($defaultPassword),
                'roles_id' => $rolDocente ? $rolDocente->id : null,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info("Docente creado/actualizado: {$persona->email} (contraseña: {$defaultPassword})");
    }
}
