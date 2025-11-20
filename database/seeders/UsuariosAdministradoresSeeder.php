<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\RolesModel;
use Illuminate\Support\Facades\Hash;

class UsuariosAdministradoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar roles
        $rolAdmin = RolesModel::where('nombre', 'Admin')->first();
        $rolRector = RolesModel::where('nombre', 'Rector')->first();
        
        if (!$rolAdmin || !$rolRector) {
            $this->command->error('Los roles necesarios no existen. Ejecuta primero RolesSeeder.');
            return;
        }

        // Crear usuario administrador
        $admin = User::updateOrCreate(
            ['email' => 'admin@colegio.edu.co'],
            [
                'name' => 'Administrador del Sistema',
                'email' => 'admin@colegio.edu.co',
                'password' => Hash::make('admin123'),
                'roles_id' => $rolAdmin->id,
                'email_verified_at' => now(),
            ]
        );

        // Crear usuario rector
        $rector = User::updateOrCreate(
            ['email' => 'rector@colegio.edu.co'],
            [
                'name' => 'Rector del Colegio',
                'email' => 'rector@colegio.edu.co',
                'password' => Hash::make('rector123'),
                'roles_id' => $rolRector->id,
                'email_verified_at' => now(),
            ]
        );

        // Crear Coordinador Académico
        $rolCoordAcad = RolesModel::where('nombre', 'CoordinadorAcademico')->first();
        if ($rolCoordAcad) {
            User::updateOrCreate(
                ['email' => 'coordinador.academico@colegio.edu.co'],
                [
                    'name' => 'Coordinador Académico',
                    'email' => 'coordinador.academico@colegio.edu.co',
                    'password' => Hash::make('academico123'),
                    'roles_id' => $rolCoordAcad->id,
                    'email_verified_at' => now(),
                ]
            );
        }

        // Crear Coordinador de Convivencia
        $rolCoordDisc = RolesModel::where('nombre', 'CoordinadorDisciplina')->first();
        if ($rolCoordDisc) {
            User::updateOrCreate(
                ['email' => 'coordinador.convivencia@colegio.edu.co'],
                [
                    'name' => 'Coordinador de Convivencia',
                    'email' => 'coordinador.convivencia@colegio.edu.co',
                    'password' => Hash::make('convivencia123'),
                    'roles_id' => $rolCoordDisc->id,
                    'email_verified_at' => now(),
                ]
            );
        }

        // Crear Tesorería
        $rolTesoreria = RolesModel::where('nombre', 'Tesoreria')->first();
        if ($rolTesoreria) {
            User::updateOrCreate(
                ['email' => 'tesoreria@colegio.edu.co'],
                [
                    'name' => 'Tesorería',
                    'email' => 'tesoreria@colegio.edu.co',
                    'password' => Hash::make('tesoreria123'),
                    'roles_id' => $rolTesoreria->id,
                    'email_verified_at' => now(),
                ]
            );
        }

        $this->command->info('✓ Usuarios administradores creados exitosamente:');
        $this->command->newLine();
        $this->command->info('ADMINISTRADOR: admin@colegio.edu.co / admin123');
        $this->command->info('RECTOR: rector@colegio.edu.co / rector123');
        $this->command->info('COORD. ACADÉMICO: coordinador.academico@colegio.edu.co / academico123');
        $this->command->info('COORD. CONVIVENCIA: coordinador.convivencia@colegio.edu.co / convivencia123');
        $this->command->info('TESORERÍA: tesoreria@colegio.edu.co / tesoreria123');
        $this->command->newLine();
        $this->command->warn('⚠ IMPORTANTE: Cambia estas contraseñas después del primer login por seguridad.');
    }
}
