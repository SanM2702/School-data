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
        ]);
    }
}
