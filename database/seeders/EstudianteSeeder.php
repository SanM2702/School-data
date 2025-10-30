<?php

namespace Database\Seeders;

use App\Models\Persona;
use App\Models\Estudiante;
use App\Models\User;
use App\Models\RolesModel;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EstudianteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear o recuperar la persona Santiago (ahora la creamos aquí)
        $persona = Persona::firstOrCreate(
            ['email' => 'santiago.martinez@edu.co'],
            [
                'primerNombre' => 'Santiago',
                'segundoNombre' => null,
                'primerApellido' => 'Martinez',
                'segundoApellido' => 'Contreras',
                'telefono' => '3022492712',
                'noDocumento' => '1031645454',
                'fechaNacimiento' => '2000-01-15',
                'estado' => true,
            ]
        );

        // Crear estudiante si no existe para la persona encontrada
        $estudiante = Estudiante::firstOrCreate([
            'idPersona' => $persona->idPersona,
        ], [
            'fechaIngreso' => now()->toDateString(),
            //'curso_id' => null,
        ]);

        // Crear o actualizar el usuario asociado a la persona
        $rolEstudiante = RolesModel::where('nombre', 'Estudiante')->first();
        $defaultPassword = 'estudiante123';

        $user = User::updateOrCreate(
            ['email' => $persona->email],
            [
                'name' => trim($persona->primerNombre . ' ' . $persona->primerApellido),
                'email' => $persona->email,
                'password' => Hash::make($defaultPassword),
                'roles_id' => $rolEstudiante ? $rolEstudiante->id : null,
                'email_verified_at' => now(),
            ]
        );

        $this->command->info("Usuario creado/actualizado para persona: {$persona->email} (contraseña: {$defaultPassword})");
    }
}
