<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use App\Models\Persona;
use App\Models\User;
use App\Models\RolesModel;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class EstudianteController extends Controller
{
    /**
     * Display a listing of the students.
     */
    public function index()
    {
        $estudiantes = Estudiante::with('persona')->get();

        return view('estudiantes.index', compact('estudiantes'));
    }

    /**
     * Show the form to create a new student.
     */
    public function nuevo()
    {
        return view('estudiantes.nuevo');
    }

    public function mostrar($idEstudiante)
    {
        $estudiante = Estudiante::with('persona')->findOrFail($idEstudiante);
        return view('estudiantes.mostrar', compact('estudiante'));
    }

    public function editar($idEstudiante)
    {
        $estudiante = Estudiante::with('persona')->findOrFail($idEstudiante);
        return view('estudiantes.editar', compact('estudiante'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'primerNombre'    => ['required','string','max:255'],
            'segundoNombre'   => ['nullable','string','max:255'],
            'primerApellido'  => ['required','string','max:255'],
            'segundoApellido' => ['nullable','string','max:255'],
            'email'           => ['nullable','email','max:255','unique:personas,email'],
            'telefono'        => ['nullable','string','max:50'],
            'noDocumento'     => ['nullable','string','max:100','unique:personas,noDocumento'],
            'fechaNacimiento' => ['nullable','date'],
            'fechaIngreso'    => ['nullable','date'],
        ]);

        $now = now();
        $fechaIngreso = $data['fechaIngreso'] ?? $now->toDateString();

        return DB::transaction(function () use ($data, $fechaIngreso, $now) {
            $personaId = DB::table('personas')->insertGetId([
                'primerNombre'    => $data['primerNombre'],
                'segundoNombre'   => $data['segundoNombre'] ?? null,
                'primerApellido'  => $data['primerApellido'],
                'segundoApellido' => $data['segundoApellido'] ?? null,
                'telefono'        => $data['telefono'] ?? null,
                'email'           => $data['email'] ?? null,
                'noDocumento'     => $data['noDocumento'] ?? null,
                'fechaNacimiento' => $data['fechaNacimiento'] ?? null,
                'estado'          => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);

            $estudianteId = DB::table('estudiantes')->insertGetId([
                'idPersona'    => $personaId,
                'fechaIngreso' => $fechaIngreso,
                'created_at'   => $now,
                'updated_at'   => $now,
            ]);

            // Crear usuario para el estudiante si hay email y documento
            $userMsg = '';
            if (!empty($data['email']) && !empty($data['noDocumento'])) {
                $rolEstId = RolesModel::where('nombre', 'Estudiante')->value('id');
                $user = User::updateOrCreate(
                    ['email' => $data['email']],
                    [
                        'name'       => trim($data['primerNombre'] . ' ' . $data['primerApellido']),
                        'password'   => Hash::make($data['noDocumento']),
                        'roles_id'   => $rolEstId,
                        'persona_id' => $personaId,
                    ]
                );
                $userMsg = $user->wasRecentlyCreated ? ' Usuario creado.' : ' Usuario actualizado.';
            } else {
                $userMsg = ' (Sin usuario: email y/o documento faltante)';
            }

            return back()->with('status', "Estudiante creado (ID: {$estudianteId})." . $userMsg);
        });
    }

    public function storeAcudiente($noDocumento, Request $request)
    {
        $data = $request->validate([
            'parentesco'      => ['required','string','max:100'],
            'primerNombre'    => ['required','string','max:255'],
            'segundoNombre'   => ['nullable','string','max:255'],
            'primerApellido'  => ['required','string','max:255'],
            'segundoApellido' => ['nullable','string','max:255'],
            'email'           => ['nullable','email','max:255','unique:personas,email'],
            'telefono'        => ['nullable','string','max:50'],
            'noDocumento'     => ['nullable','string','max:100','unique:personas,noDocumento'],
            'fechaNacimiento' => ['nullable','date'],
        ]);

        $now = now();

        return DB::transaction(function () use ($noDocumento, $data, $now) {
            $personaEst = Persona::where('noDocumento', $noDocumento)->first();
            if (!$personaEst) {
                return back()->with('status', 'Estudiante no encontrado por documento.');
            }
            $estudiante = Estudiante::where('idPersona', $personaEst->idPersona)->first();
            if (!$estudiante) {
                return back()->with('status', 'La persona con ese documento no es un estudiante.');
            }

            $personaAcuId = DB::table('personas')->insertGetId([
                'primerNombre'    => $data['primerNombre'],
                'segundoNombre'   => $data['segundoNombre'] ?? null,
                'primerApellido'  => $data['primerApellido'],
                'segundoApellido' => $data['segundoApellido'] ?? null,
                'telefono'        => $data['telefono'] ?? null,
                'email'           => $data['email'] ?? null,
                'noDocumento'     => $data['noDocumento'] ?? null,
                'fechaNacimiento' => $data['fechaNacimiento'] ?? null,
                'estado'          => true,
                'created_at'      => $now,
                'updated_at'      => $now,
            ]);

            $acudienteId = DB::table('acudientes')->insertGetId([
                'idPersona'  => $personaAcuId,
                'parentesco' => $data['parentesco'],
                'created_at' => $now,
                'updated_at' => $now,
            ]);

            DB::table('estudiante_acudiente')->updateOrInsert(
                ['idEstudiante' => $estudiante->idEstudiante, 'idAcudiente' => $acudienteId],
                []
            );

            // Crear usuario para el acudiente si hay email y documento
            $userMsg = '';
            if (!empty($data['email']) && !empty($data['noDocumento'])) {
                $rolAcuId = RolesModel::where('nombre', 'Acudiente')->value('id');
                $user = User::updateOrCreate(
                    ['email' => $data['email']],
                    [
                        'name'       => trim($data['primerNombre'] . ' ' . $data['primerApellido']),
                        'password'   => Hash::make($data['noDocumento']),
                        'roles_id'   => $rolAcuId,
                        'persona_id' => $personaAcuId,
                    ]
                );
                $userMsg = $user->wasRecentlyCreated ? ' Usuario de acudiente creado.' : ' Usuario de acudiente actualizado.';
            } else {
                $userMsg = ' (Acudiente sin usuario: email y/o documento faltante)';
            }

            return back()->with('status', 'Acudiente creado y asociado correctamente.' . $userMsg);
        });
    }

    public function storeMatricula($noDocumento, Request $request)
    {
        $data = $request->validate([
            'estado'         => ['nullable','in:en_proceso,activo,inactivo'], // será forzado a en_proceso
            'fechaMatricula' => ['nullable','date'],
        ]);

        $now = now();

        return DB::transaction(function () use ($noDocumento, $data, $now) {
            $personaEst = Persona::where('noDocumento', $noDocumento)->first();
            if (!$personaEst) {
                return back()->with('status', 'Estudiante no encontrado por documento.');
            }
            $estudiante = Estudiante::where('idPersona', $personaEst->idPersona)->first();
            if (!$estudiante) {
                return back()->with('status', 'La persona con ese documento no es un estudiante.');
            }

            $fecha = $data['fechaMatricula'] ?? ($estudiante->fechaIngreso ?: $now->toDateString());

            DB::table('matricula')->insert([
                'idEstudiante'   => $estudiante->idEstudiante,
                'estado'         => 'en_proceso',
                'fechaMatricula' => $fecha,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);

            return back()->with('status', 'Matrícula creada correctamente.');
        });
    }

    public function info(string $noDocumento): JsonResponse
    {
        $persona = Persona::where('noDocumento', $noDocumento)->first();
        if (!$persona) {
            return response()->json(['found' => false], 404);
        }
        $estudiante = Estudiante::where('idPersona', $persona->idPersona)->first();
        if (!$estudiante) {
            return response()->json([
                'found' => false,
                'persona' => [
                    'idPersona' => $persona->idPersona,
                    'primerNombre' => $persona->primerNombre,
                    'primerApellido' => $persona->primerApellido,
                ],
            ], 404);
        }

        return response()->json([
            'found' => true,
            'idEstudiante' => $estudiante->idEstudiante,
            'fechaIngreso' => $estudiante->fechaIngreso,
            'persona' => [
                'idPersona' => $persona->idPersona,
                'primerNombre' => $persona->primerNombre,
                'primerApellido' => $persona->primerApellido,
            ],
        ]);
    }
}
