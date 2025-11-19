<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Curso;
use App\Models\Estudiante;

class CursoController extends Controller
{
    /**
     * Mostrar listado de cursos (index).
     */
    public function index()
    {
        $usuario = Auth::user();
        // Filtro único por grado (nombre del curso)
        $grado = request('grado');

        // Listado de grados disponibles
        $grados = Curso::query()->whereNotNull('grado')->distinct()->orderBy('grado')->pluck('grado');

        // Construir consulta de cursos a mostrar según filtro
        $cursosQuery = Curso::with(['estudiantes.persona']);
        if ($grado) {
            $cursosQuery->where('grado', $grado);
        }
        $cursos = $cursosQuery->get();

        return view('cursos.index', compact('usuario', 'cursos', 'grados', 'grado'));
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

        return redirect()->route('cursos.index')->with('success', 'Asignaciones de estudiantes actualizadas.');
    }
}
