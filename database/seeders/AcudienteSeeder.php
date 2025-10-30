<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Carbon\Carbon;

class AcudienteSeeder extends Seeder
{
    public function run(): void
    {
        // Datos únicos para evitar colisiones con restricciones unique(email)
        $suffix = Str::random(6);

        // 1) Persona del estudiante
        $personaEstId = DB::table('personas')->insertGetId([
            'primerNombre'    => 'Estudiante',
            'segundoNombre'   => 'Demo',
            'primerApellido'  => 'Demo',
            'segundoApellido' => null,
            'telefono'        => null,
            'noDocumento'     => 'DOC' . strtoupper(Str::random(8)),
            'fechaNacimiento' => null,
            'estado'          => true,
            'email'           => 'estudiante.demo.' . $suffix . '@example.com',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // 2) Crear estudiante asociado
        $estudianteId = DB::table('estudiantes')->insertGetId([
            'idPersona'   => $personaEstId,
            'fechaIngreso'=> Carbon::now()->toDateString(),
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // 3) Persona del acudiente
        $personaAcuId = DB::table('personas')->insertGetId([
            'primerNombre'    => 'Acudiente',
            'segundoNombre'   => 'Demo',
            'primerApellido'  => 'Demo',
            'segundoApellido' => null,
            'telefono'        => null,
            'noDocumento'     => 'DOC' . strtoupper(Str::random(8)),
            'fechaNacimiento' => null,
            'estado'          => true,
            'email'           => 'acudiente.demo.' . $suffix . '@example.com',
            'created_at'      => now(),
            'updated_at'      => now(),
        ]);

        // 4) Crear acudiente y vincular en la tabla pivote
        $acudienteId = DB::table('acudientes')->insertGetId([
            'idPersona'   => $personaAcuId,
            'parentesco'  => 'Padre',
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        // 5) Relación many-to-many en tabla pivote
        DB::table('estudiante_acudiente')->insert([
            'idEstudiante' => $estudianteId,
            'idAcudiente'  => $acudienteId,
        ]);
    }
}
