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
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use App\Models\Activity;

class EstudianteController extends Controller
{
    /**
     * Display a listing of the students.
     */
    public function index()
    {
        $grado = request('grado');

        $query = Estudiante::with('persona');
        if ($grado) {
            $courseIds = \App\Models\Curso::where('grado', $grado)->pluck('idCurso');
            $query->whereIn('curso_id', $courseIds);
        }

        $estudiantes = $query->get();

        // Pasar datos para el select de filtro
        $grados = \App\Models\Curso::whereNotNull('grado')->distinct()->orderBy('grado')->pluck('grado');

        return view('estudiantes.index', compact('estudiantes', 'grados', 'grado'));
    }

    public function updateContacto($idEstudiante, Request $request)
    {
        $estudiante = Estudiante::with('persona')->findOrFail($idEstudiante);
        $data = $request->validate([
            'telefono' => ['nullable','string','max:50'],
            'email'    => ['nullable','email','max:255'],
        ]);

        DB::table('personas')
            ->where('idPersona', $estudiante->persona->idPersona)
            ->update([
                'telefono'   => $data['telefono'] ?? null,
                'email'      => $data['email'] ?? null,
                'updated_at' => now(),
            ]);

        Activity::create([
            'user_id' => Auth::id(),
            'type' => 'estudiante.contacto_actualizado',
            'subject_type' => 'estudiante',
            'subject_id' => $estudiante->idEstudiante,
            'description' => 'Contacto del estudiante actualizado',
            'metadata' => [
                'idEstudiante' => $estudiante->idEstudiante,
            ],
        ]);

        return back()->with('status', 'Contacto del estudiante actualizado. Nota: No cambiara su correo de inicio de sesion');
    }

    public function updateContactoAcudiente($idEstudiante, Request $request)
    {
        $data = $request->validate([
            'telefono' => ['nullable','string','max:50'],
            'email'    => ['nullable','email','max:255'],
        ]);

        $acudiente = DB::table('estudiante_acudiente as ea')
            ->join('acudientes as a', 'a.idAcudiente', '=', 'ea.idAcudiente')
            ->join('personas as p', 'p.idPersona', '=', 'a.idPersona')
            ->select('p.idPersona')
            ->where('ea.idEstudiante', $idEstudiante)
            ->first();

        if ($acudiente) {
            DB::table('personas')
                ->where('idPersona', $acudiente->idPersona)
                ->update([
                    'telefono'   => $data['telefono'] ?? null,
                    'email'      => $data['email'] ?? null,
                    'updated_at' => now(),
                ]);

            Activity::create([
                'user_id' => Auth::id(),
                'type' => 'acudiente.contacto_actualizado',
                'subject_type' => 'estudiante',
                'subject_id' => $idEstudiante,
                'description' => 'Contacto de acudiente actualizado',
                'metadata' => [
                    'idEstudiante' => $idEstudiante,
                ],
            ]);
        }

        return back()->with('status', 'Contacto del acudiente actualizado. Nota: No cambiara su correo de inicio de sesion');
    }


    /**
     * Show the form to create a new student.
     */
    public function nuevo()
    {
        // Opciones únicas por grado: un curso representativo por cada grado
        $cursos = \App\Models\Curso::query()
            ->whereNotNull('grado')
            ->selectRaw('grado, MIN(idCurso) as idCurso')
            ->groupBy('grado')
            ->orderBy('grado')
            ->get();
        return view('estudiantes.nuevo', compact('cursos'));
    }

    public function mostrar($idEstudiante)
    {
        $estudiante = Estudiante::with(['persona','curso'])->findOrFail($idEstudiante);

        $acudiente = DB::table('estudiante_acudiente as ea')
            ->join('acudientes as a', 'a.idAcudiente', '=', 'ea.idAcudiente')
            ->join('personas as p', 'p.idPersona', '=', 'a.idPersona')
            ->select(
                'a.idAcudiente',
                'a.parentesco',
                'p.primerNombre',
                'p.segundoNombre',
                'p.primerApellido',
                'p.segundoApellido',
                'p.telefono',
                'p.email',
                'p.noDocumento',
                'p.fechaNacimiento',
                'p.idPersona'
            )
            ->where('ea.idEstudiante', $estudiante->idEstudiante)
            ->first();

        return view('estudiantes.mostrar', compact('estudiante', 'acudiente'));
    }

    public function editar($idEstudiante)
    {
        $estudiante = Estudiante::with('persona')->findOrFail($idEstudiante);

        $acudiente = DB::table('estudiante_acudiente as ea')
            ->join('acudientes as a', 'a.idAcudiente', '=', 'ea.idAcudiente')
            ->join('personas as p', 'p.idPersona', '=', 'a.idPersona')
            ->select(
                'a.idAcudiente',
                'a.parentesco',
                'p.primerNombre',
                'p.segundoNombre',
                'p.primerApellido',
                'p.segundoApellido',
                'p.telefono',
                'p.email',
                'p.noDocumento',
                'p.fechaNacimiento',
                'p.idPersona'
            )
            ->where('ea.idEstudiante', $estudiante->idEstudiante)
            ->first();

        return view('estudiantes.editar', compact('estudiante', 'acudiente'));
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

            Activity::create([
                'user_id' => Auth::id(),
                'type' => 'estudiante.creado',
                'subject_type' => 'estudiante',
                'subject_id' => $estudianteId,
                'description' => 'Nuevo estudiante creado',
                'metadata' => [
                    'idPersona' => $personaId,
                ],
            ]);

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

            Activity::create([
                'user_id' => Auth::id(),
                'type' => 'acudiente.creado',
                'subject_type' => 'acudiente',
                'subject_id' => $acudienteId,
                'description' => 'Acudiente creado y asociado a estudiante',
                'metadata' => [
                    'idEstudiante' => $estudiante->idEstudiante,
                ],
            ]);

            return back()->with('status', 'Acudiente creado y asociado correctamente.' . $userMsg);
        });
    }

    public function storeMatricula($noDocumento, Request $request)
    {
        $data = $request->validate([
            'estado'         => ['nullable','in:en_proceso,activo,inactivo'], // será forzado a en_proceso
            'fechaMatricula' => ['nullable','date'],
            'curso_id'       => ['required','exists:cursos,idCurso'],
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

            // Actualizar el curso del estudiante al seleccionado
            $estudiante->curso_id = $data['curso_id'];
            $estudiante->save();

            $matriculaId = DB::table('matricula')->insertGetId([
                'idEstudiante'   => $estudiante->idEstudiante,
                'estado'         => 'en_proceso',
                'fechaMatricula' => $fecha,
                'created_at'     => $now,
                'updated_at'     => $now,
            ]);

            Activity::create([
                'user_id' => Auth::id(),
                'type' => 'matricula.creada',
                'subject_type' => 'matricula',
                'subject_id' => $matriculaId,
                'description' => 'Matrícula creada para estudiante',
                'metadata' => [
                    'idEstudiante' => $estudiante->idEstudiante,
                    'curso_id' => $estudiante->curso_id,
                ],
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

    /**
     * Serve student photo stored on FTP (or fallback placeholder).
     */
    public function foto($idEstudiante)
    {
        $estudiante = Estudiante::findOrFail($idEstudiante);

        if ($estudiante->foto && Storage::disk('ftp')->exists($estudiante->foto)) {
            $content = Storage::disk('ftp')->get($estudiante->foto);
            // try to get mime, fall back to jpeg
            $mime = 'image/jpeg';
            try {
                $mime = Storage::disk('ftp')->mimeType($estudiante->foto) ?: $mime;
            } catch (\Exception $e) {
                // ignore
            }
            return response($content, 200)->header('Content-Type', $mime);
        }

        // Fallback: return a simple placeholder image from public assets if exists
        if (file_exists(public_path('images/placeholder-3x4.png'))) {
            return response()->file(public_path('images/placeholder-3x4.png'));
        }

        abort(404);
    }

    /**
     * Handle foto upload, resize to 3x4 (fit) and store on FTP.
     */
    public function updateFoto($idEstudiante, Request $request)
    {
        $estudiante = Estudiante::with('persona')->findOrFail($idEstudiante);

        $data = $request->validate([
            // Accept any image type supported by PHP upload (jpeg/png/gif/webp etc.)
            'foto' => ['required', 'image', 'max:8192'], // max 8MB
        ]);

        $file = $request->file('foto');

        // Keep original extension
        $extension = $file->getClientOriginalExtension() ?: 'jpg';
        $filename = 'estudiante_' . $estudiante->idEstudiante . '_' . time() . '.' . $extension;
        $path = 'estudiantes/' . $filename;

        // Store file directly to FTP without server-side image processing
        try {
            // Use stream to avoid loading entire file into memory
            Storage::disk('ftp')->put($path, fopen($file->getPathname(), 'r'));
        } catch (\Exception $e) {
            return back()->with('error', 'Error subiendo la imagen al servidor FTP: ' . $e->getMessage());
        }

        // Save path in DB
        $estudiante->foto = $path;
        $estudiante->save();

        return back()->with('status', 'Foto actualizada correctamente.');
    }
}
