<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Disciplina;
use App\Models\Estudiante;
use App\Models\Materia;
use App\Models\Nota;
use Illuminate\Http\Request;

class ReportesController extends Controller
{
    public function index(Request $request)
    {
        $usuario = \Illuminate\Support\Facades\Auth::user();
        $rol = \App\Models\RolesModel::find($usuario->roles_id);
        
        // Si es estudiante, mostrar solo su propio reporte
        if ($rol && $rol->nombre === 'Estudiante') {
            $estudiante = Estudiante::whereHas('persona', function($query) use ($usuario) {
                $query->where('email', $usuario->email);
            })->with(['persona', 'curso'])->first();
            
            if (!$estudiante) {
                return view('reportes.index', [
                    'listaEstudiantes' => collect([]),
                    'estudiante' => null,
                    'disciplinas' => collect(),
                    'materias' => collect(),
                    'resumenFaltas' => ['total' => 0, 'por_tipo' => [], 'por_estado' => [], 'ultima_falta' => null],
                    'resumenNotas' => ['promedio' => null, 'por_materia' => []],
                    'esEstudiante' => true,
                ]);
            }
            
            // Cargar datos del estudiante
            $disciplinas = Disciplina::where('estudiante_id', $estudiante->idEstudiante)
                ->orderByDesc('fecha')
                ->get();

            $notas = Nota::with('materia')
                ->where('estudiante_id', $estudiante->idEstudiante)
                ->get();

            $curso = $estudiante->curso;
            $materias = collect();
            if ($curso) {
                $materias = $curso->materias()->get();
            }

            // Resumen de faltas
            $resumenFaltas = [
                'total' => $disciplinas->count(),
                'por_tipo' => $disciplinas->groupBy('tipo_falta')->map->count()->toArray(),
                'por_estado' => $disciplinas->groupBy('estado')->map->count()->toArray(),
                'ultima_falta' => $disciplinas->sortByDesc('fecha')->first()?->fecha,
            ];

            // Resumen de notas
            $resumenNotas = [
                'promedio' => $notas->avg('valor'),
                'por_materia' => $notas->groupBy('materia_id')->map(function ($notasMateria) {
                    return $notasMateria->avg('valor');
                }),
            ];
            
            return view('reportes.index', [
                'listaEstudiantes' => collect([$estudiante]),
                'estudiante' => $estudiante,
                'disciplinas' => $disciplinas,
                'materias' => $materias,
                'resumenFaltas' => $resumenFaltas,
                'resumenNotas' => $resumenNotas,
                'esEstudiante' => true,
            ]);
        }

        // Si es acudiente, mostrar solo el reporte de su estudiante
        if ($rol && $rol->nombre === 'Acudiente') {
            $acudiente = \App\Models\Acudiente::whereHas('persona', function($query) use ($usuario) {
                $query->where('email', $usuario->email);
            })->with('estudiantes.persona', 'estudiantes.curso')->first();
            
            if ($acudiente && $acudiente->estudiantes->count() > 0) {
                $estudiante = $acudiente->estudiantes->first();
                
                // Cargar datos del estudiante
                $disciplinas = Disciplina::where('estudiante_id', $estudiante->idEstudiante)
                    ->orderByDesc('fecha')
                    ->get();

                $notas = Nota::with('materia')
                    ->where('estudiante_id', $estudiante->idEstudiante)
                    ->get();

                $curso = $estudiante->curso;
                $materias = collect();
                if ($curso) {
                    $materias = $curso->materias()->get();
                }

                // Resumen de faltas
                $resumenFaltas = [
                    'total' => $disciplinas->count(),
                    'por_tipo' => $disciplinas->groupBy('tipo_falta')->map->count()->toArray(),
                    'por_estado' => $disciplinas->groupBy('estado')->map->count()->toArray(),
                    'ultima_falta' => $disciplinas->sortByDesc('fecha')->first()?->fecha,
                ];

                // Resumen de notas
                $resumenNotas = [
                    'promedio' => $notas->avg('valor'),
                    'por_materia' => $notas->groupBy('materia_id')->map(function ($notasMateria) {
                        return $notasMateria->avg('valor');
                    }),
                ];

                return view('reportes.index', [
                    'listaEstudiantes' => collect([$estudiante]),
                    'estudiante' => $estudiante,
                    'disciplinas' => $disciplinas,
                    'materias' => $materias,
                    'resumenFaltas' => $resumenFaltas,
                    'resumenNotas' => $resumenNotas,
                    'esEstudiante' => true, // Para ocultar selectores
                ]);
            } else {
                return view('reportes.index', [
                    'listaEstudiantes' => collect([]),
                    'estudiante' => null,
                    'disciplinas' => collect(),
                    'materias' => collect(),
                    'resumenFaltas' => ['total' => 0, 'por_tipo' => [], 'por_estado' => [], 'ultima_falta' => null],
                    'resumenNotas' => ['promedio' => null, 'por_materia' => []],
                    'esEstudiante' => true,
                ]);
            }
        }
        
        // Para otros roles, mostrar selector de estudiantes
        $estudiantes = Estudiante::with(['persona','curso'])->get();

        $selectedId = $request->has('estudiante') ? (int) $request->input('estudiante') : null;
        $estudiante = null;
        $disciplinas = collect();
        $notas = collect();
        $materias = collect();
        $resumenFaltas = [
            'total' => 0,
            'por_tipo' => [],
            'por_estado' => [],
            'ultima_falta' => null,
        ];
        $resumenNotas = [
            'promedio' => null,
            'por_materia' => [],
        ];

        if ($selectedId) {
            $estudiante = Estudiante::with(['persona','curso'])->find($selectedId);
            if ($estudiante) {
                $disciplinas = Disciplina::where('estudiante_id', $estudiante->idEstudiante)
                    ->orderByDesc('fecha')
                    ->get();

                $notas = Nota::with('materia')
                    ->where('estudiante_id', $estudiante->idEstudiante)
                    ->get();

                $curso = $estudiante->curso;
                if ($curso) {
                    $materias = $curso->materias()->get();
                }

                // Resumen de faltas
                $resumenFaltas['total'] = $disciplinas->count();
                $resumenFaltas['por_tipo'] = $disciplinas->groupBy('tipo_falta')->map->count()->toArray();
                $resumenFaltas['por_estado'] = $disciplinas->groupBy('estado')->map->count()->toArray();
                $ultima = $disciplinas->sortByDesc('fecha')->first();
                $resumenFaltas['ultima_falta'] = $ultima ? $ultima->fecha : null;

                // Resumen de notas
                $valores = $notas->pluck('valor')->filter(function ($v) { return $v !== null && $v !== '';})->map(fn($v)=>(float)$v);
                $resumenNotas['promedio'] = $valores->count() ? round($valores->avg(), 2) : null;
                foreach ($materias as $m) {
                    $n = $notas->firstWhere('materia_id', $m->idMateria);
                    $resumenNotas['por_materia'][$m->idMateria] = $n ? (float) $n->valor : null;
                }
            }
        }

        // Para selector de estudiantes (id + nombre amigable)
        $listaEstudiantes = $estudiantes->map(function ($e) {
            $p = $e->persona;
            $nombre = trim(($p->primerNombre ?? '').' '.($p->segundoNombre ?? '').' '.($p->primerApellido ?? '').' '.($p->segundoApellido ?? ''));
            return [
                'id' => $e->idEstudiante,
                'texto' => $nombre !== '' ? $nombre : ('Estudiante #'.$e->idEstudiante),
                'curso' => $e->curso ? ($e->curso->nombre.' '.$e->curso->grupo) : null,
            ];
        });

        return view('reportes.index', [
            'listaEstudiantes' => $listaEstudiantes,
            'estudiante' => $estudiante,
            'disciplinas' => $disciplinas,
            'materias' => $materias,
            'resumenFaltas' => $resumenFaltas,
            'resumenNotas' => $resumenNotas,
            'esEstudiante' => false,
        ]);
    }
}
