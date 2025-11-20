<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Nota;
use App\Models\Materia;
use App\Models\Estudiante;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class NotasController extends Controller
{
    public function index()
    {
        $cursos = Curso::withCount('estudiantes')->get();

        // calcular promedio por curso
        $promedios = [];
        foreach ($cursos as $curso) {
            $avg = DB::table('notas')
                ->join('estudiantes', 'estudiantes.idEstudiante', '=', 'notas.estudiante_id')
                ->where('estudiantes.curso_id', $curso->idCurso)
                ->avg('valor');
            $promedios[$curso->idCurso] = $avg ? round($avg, 2) : null;
        }

        return view('notas.index', compact('cursos', 'promedios'));
    }

    public function mostrar(Curso $curso)
    {
        $curso->load(['estudiantes.persona', 'materias']);
        $materias = $curso->materias;
        $estudiantes = $curso->estudiantes;

        $estIds = $estudiantes->pluck('idEstudiante')->all();
        $matIds = $materias->pluck('idMateria')->all();

        $notas = Nota::whereIn('estudiante_id', $estIds)
            ->whereIn('materia_id', $matIds)
            ->get();

        // Mapear notas por [estudiante_id][materia_id] => valor
        $notaMap = [];
        foreach ($notas as $n) {
            $notaMap[$n->estudiante_id][$n->materia_id] = $n->valor;
        }

        // Promedios por estudiante
        $promedioEstudiante = [];
        foreach ($estudiantes as $e) {
            $sum = 0; $count = 0;
            foreach ($materias as $m) {
                $v = $notaMap[$e->idEstudiante][$m->idMateria] ?? null;
                if ($v !== null) { $sum += (float)$v; $count++; }
            }
            $promedioEstudiante[$e->idEstudiante] = $count ? round($sum / $count, 2) : null;
        }

        // Promedios por materia
        $promedioMateria = [];
        foreach ($materias as $m) {
            $sum = 0; $count = 0;
            foreach ($estudiantes as $e) {
                $v = $notaMap[$e->idEstudiante][$m->idMateria] ?? null;
                if ($v !== null) { $sum += (float)$v; $count++; }
            }
            $promedioMateria[$m->idMateria] = $count ? round($sum / $count, 2) : null;
        }

        return view('notas.mostrar', compact('curso', 'materias', 'estudiantes', 'notaMap', 'promedioEstudiante', 'promedioMateria'));
    }

    public function editar(Curso $curso)
    {
        $curso->load(['estudiantes.persona', 'materias']);
        $materias = $curso->materias;
        $estudiantes = $curso->estudiantes;

        $estIds = $estudiantes->pluck('idEstudiante')->all();
        $matIds = $materias->pluck('idMateria')->all();

        $notas = Nota::whereIn('estudiante_id', $estIds)
            ->whereIn('materia_id', $matIds)
            ->get();

        $notaMap = [];
        foreach ($notas as $n) {
            $notaMap[$n->estudiante_id][$n->materia_id] = $n->valor;
        }

        return view('notas.editar', compact('curso', 'materias', 'estudiantes', 'notaMap'));
    }

    public function actualizar(Curso $curso, Request $request)
    {
        $data = $request->validate([
            'notas' => ['nullable','array'],
        ]);

        $payload = $data['notas'] ?? [];

        DB::transaction(function () use ($payload) {
            foreach ($payload as $idEstudiante => $porMateria) {
                foreach ($porMateria as $idMateria => $valorRaw) {
                    $valor = is_string($valorRaw) ? trim($valorRaw) : $valorRaw;
                    if ($valor === '' || $valor === null) {
                        Nota::where('estudiante_id', $idEstudiante)
                            ->where('materia_id', $idMateria)
                            ->delete();
                        continue;
                    }
                    // Normalizar a número y limitar 0-5 (ajusta escala si tu colegio usa otra)
                    $num = (float) $valor;
                    if ($num < 0) $num = 0;
                    if ($num > 5) $num = 5;

                    Nota::updateOrCreate(
                        [
                            'estudiante_id' => $idEstudiante,
                            'materia_id' => $idMateria,
                        ],
                        [
                            'valor' => $num,
                        ]
                    );
                }
            }
        });

        return redirect()->route('notas.mostrar', $curso)->with('status', 'Notas actualizadas correctamente');
    }
}
