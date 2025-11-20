<?php

namespace App\Http\Controllers;

use App\Models\Materia;
use App\Models\Curso;
use App\Models\Docente;
use Illuminate\Http\Request;

class MateriaController extends Controller
{
    public function index()
    {
        $usuario = \Illuminate\Support\Facades\Auth::user();
        $rol = \App\Models\RolesModel::find($usuario->roles_id);
        
        // Si es estudiante, mostrar solo materias de su curso
        if ($rol && $rol->nombre === 'Estudiante') {
            $estudiante = \App\Models\Estudiante::whereHas('persona', function($query) use ($usuario) {
                $query->where('email', $usuario->email);
            })->with('curso')->first();
            
            if ($estudiante && $estudiante->curso) {
                $materias = Materia::with(['curso', 'docente.persona'])
                    ->where('curso_id', $estudiante->curso->idCurso)
                    ->orderBy('nombre')
                    ->get();
                $cursos = Curso::where('idCurso', $estudiante->curso->idCurso)->get();
            } else {
                $materias = collect([]);
                $cursos = collect([]);
            }
            
            return view('materias.index', compact('materias', 'cursos'));
        }
        
        // Para otros roles, mostrar todas las materias
        $query = Materia::with(['curso', 'docente.persona'])->orderBy('nombre');
        if (request('materia')) {
            $query->where('nombre', 'like', '%'.request('materia').'%');
        }
        $materias = $query->get();
        $cursos = Curso::orderBy('grado')->orderBy('grupo')->orderBy('nombre')->get();
        return view('materias.index', compact('materias', 'cursos'));
    }

    public function create()
    {
        $cursos = Curso::orderBy('grado')->orderBy('grupo')->orderBy('nombre')->get();
        $docentes = Docente::with('persona')->orderBy('idDocente')->get();
        $areas = Docente::query()->select('area')->whereNotNull('area')->distinct()->orderBy('area')->pluck('area');
        return view('materias.agregar', compact('cursos', 'docentes', 'areas'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nombre' => ['required', 'string', 'max:255'],
            'curso_id' => ['required', 'exists:cursos,idCurso'],
            'docente_id' => ['nullable', 'exists:docentes,idDocente'],
            'codigo' => ['nullable', 'string', 'max:50'],
            'descripcion' => ['nullable', 'string'],
        ]);

        Materia::create([
            'nombre' => $validated['nombre'],
            'curso_id' => $validated['curso_id'],
            'docente_id' => $validated['docente_id'] ?? null,
            'codigo' => $validated['codigo'] ?? null,
            'descripcion' => $validated['descripcion'] ?? null,
        ]);

        return redirect()->route('materias.index')->with('status', 'Materia creada correctamente');
    }

    public function edit(Materia $materia)
    {
        $materia->load(['curso', 'docente.persona']);
        $docentes = Docente::with('persona')->orderBy('idDocente')->get();
        return view('materias.editar', compact('materia', 'docentes'));
    }

    public function update(Request $request, Materia $materia)
    {
        $validated = $request->validate([
            'docente_id' => ['nullable', 'exists:docentes,idDocente'],
        ]);

        $materia->update([
            'docente_id' => $validated['docente_id'] ?? null,
        ]);

        return redirect()->route('materias.index')->with('status', 'Profesor asignado actualizado correctamente');
    }

    public function destroy(Materia $materia)
    {
        $materia->delete();
        return redirect()->route('materias.index')->with('status', 'Materia eliminada correctamente');
    }
}
