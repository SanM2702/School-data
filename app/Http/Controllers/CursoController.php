<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Curso;
use App\Models\Estudiante;
use App\Models\Activity;

class CursoController extends Controller
{
    /**
     * Mostrar listado de cursos (index).
     */
    public function index(Request $request)
    {
        $usuario = \Illuminate\Support\Facades\Auth::user();
        $rol = \App\Models\RolesModel::find($usuario->roles_id);
        
        // Si es estudiante, mostrar solo su curso
        if ($rol && $rol->nombre === 'Estudiante') {
            $estudiante = Estudiante::whereHas('persona', function($query) use ($usuario) {
                $query->where('email', $usuario->email);
            })->with('curso.estudiantes.persona')->first();
            
            if ($estudiante && $estudiante->curso) {
                $cursos = collect([$estudiante->curso]);
            } else {
                $cursos = collect([]);
            }
            
            $grados = [];
            return view('cursos.index', compact('cursos', 'grados'));
        }
        
        // Si es acudiente, mostrar solo el curso de su estudiante
        if ($rol && $rol->nombre === 'Acudiente') {
            $acudiente = \App\Models\Acudiente::whereHas('persona', function($query) use ($usuario) {
                $query->where('email', $usuario->email);
            })->with('estudiantes.curso.estudiantes.persona')->first();
            
            if ($acudiente && $acudiente->estudiantes->count() > 0) {
                $estudiante = $acudiente->estudiantes->first();
                if ($estudiante->curso) {
                    $cursos = collect([$estudiante->curso]);
                } else {
                    $cursos = collect([]);
                }
            } else {
                $cursos = collect([]);
            }
            
            $grados = [];
            return view('cursos.index', compact('cursos', 'grados'));
        }
        
        // Para otros roles, mostrar todos los cursos o filtrados
        $grado = $request->input('grado');
        
        $grados = Curso::query()->whereNotNull('grado')->distinct()->orderBy('grado')->pluck('grado');
        
        // Construir consulta de cursos a mostrar según filtro
        $cursosQuery = Curso::with(['estudiantes.persona']);
        if ($grado) {
            $cursosQuery->where('grado', $grado);
        }
        $cursos = $cursosQuery->get();

        return view('cursos.index', compact('cursos', 'grados', 'grado'));
    }

    public function edit($id)
    {
        $curso = Curso::with('estudiantes.persona')->findOrFail($id);
        // Filtrar estudiantes por el mismo grado (y sin asignar) para asignarlos a este grupo del grado
        $grado = $curso->grado;
        $cursoIdsMismoGrado = Curso::where('grado', $grado)->pluck('idCurso');
        $estudiantes = Estudiante::with('persona')
            ->where(function($q) use ($cursoIdsMismoGrado) {
                $q->whereNull('curso_id')
                  ->orWhereIn('curso_id', $cursoIdsMismoGrado);
            })
            ->get();
        return view('cursos.editar', compact('curso', 'estudiantes'));
    }

    public function update($id, \Illuminate\Http\Request $request)
    {
        $curso = Curso::findOrFail($id);
        $data = $request->validate([
            'estudiantes' => ['nullable','array'],
            'estudiantes.*' => ['integer'],
        ]);

        $selected = $data['estudiantes'] ?? [];

        \DB::transaction(function() use ($curso, $selected) {
            // Desasignar estudiantes que estaban en este curso pero no están seleccionados
            Estudiante::where('curso_id', $curso->idCurso)
                ->whereNotIn('idEstudiante', $selected)
                ->update(['curso_id' => null]);

            // Asignar curso a los seleccionados
            if (!empty($selected)) {
                Estudiante::whereIn('idEstudiante', $selected)->update(['curso_id' => $curso->idCurso]);
            }
        });

        Activity::create([
            'user_id' => Auth::id(),
            'type' => 'curso.estudiantes_actualizados',
            'subject_type' => 'curso',
            'subject_id' => $curso->idCurso,
            'description' => 'Asignaciones de estudiantes actualizadas en el curso',
            'metadata' => [
                'curso_id' => $curso->idCurso,
                'total_seleccionados' => count($selected),
            ],
        ]);

        return redirect()->route('cursos.index')->with('success', 'Asignaciones de estudiantes actualizadas.');
    }
}
