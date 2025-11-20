<?php

namespace App\Http\Controllers;

use App\Models\Matricula;
use App\Models\RolesModel;
use App\Models\Activity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MatriculaController extends Controller
{
    public function index()
    {
        $matriculas = Matricula::with(['estudiante.persona', 'estudiante.curso'])
            ->orderByDesc('idMatricula')
            ->paginate(15);

        return view('matriculas.index', compact('matriculas'));
    }

    public function mostrar(Matricula $matricula)
    {
        $matricula->load(['estudiante.persona', 'estudiante.curso']);
        $documentos = $this->obtenerDocumentos($matricula);
        $proceso = $this->mapearProceso($matricula->estado);
        return view('matriculas.mostrar', compact('matricula', 'documentos', 'proceso'));
    }

    public function actualizarEstado(Request $request, Matricula $matricula)
    {
        if (!$this->usuarioPuedeCambiarEstado()) {
            return back()->with('error', 'No tienes permisos para cambiar el estado de la matrícula.');
        }

        $data = $request->validate([
            'estado' => 'required|in:en_proceso,activo,inactivo',
        ]);

        $matricula->estado = $data['estado'];
        $matricula->save();

        Activity::create([
            'user_id' => Auth::id(),
            'type' => 'matricula.estado_actualizado',
            'subject_type' => 'matricula',
            'subject_id' => $matricula->idMatricula,
            'description' => 'Estado de matrícula actualizado a ' . $matricula->estado,
            'metadata' => [
                'idEstudiante' => $matricula->idEstudiante,
                'estado' => $matricula->estado,
            ],
        ]);

        return back()->with('success', 'Estado de matrícula actualizado.');
    }

    public function subirDocumentos(Request $request, Matricula $matricula)
    {
        if (!$this->usuarioPuedeSubirDocumentos()) {
            return back()->with('error', 'No tienes permisos para subir documentos de matrícula.');
        }

        $request->validate([
            'documentos.*' => 'required|file|max:5120',
        ]);

        $pathBase = 'matriculas/' . $matricula->idMatricula;
        if ($request->hasFile('documentos')) {
            foreach ($request->file('documentos') as $file) {
                $nombre = now()->format('Ymd_His') . '_' . str_replace(' ', '_', $file->getClientOriginalName());
                Storage::putFileAs($pathBase, $file, $nombre);
            }
        }

        Activity::create([
            'user_id' => Auth::id(),
            'type' => 'matricula.documentos_subidos',
            'subject_type' => 'matricula',
            'subject_id' => $matricula->idMatricula,
            'description' => 'Documentos de matrícula subidos',
            'metadata' => [
                'count' => $request->hasFile('documentos') ? count($request->file('documentos')) : 0,
            ],
        ]);

        return back()->with('success', 'Documentos cargados correctamente.');
    }

    private function obtenerDocumentos(Matricula $matricula): array
    {
        $pathBase = 'matriculas/' . $matricula->idMatricula;
        if (!Storage::exists($pathBase)) {
            return [];
        }
        $archivos = Storage::files($pathBase);
        return array_map(function ($p) {
            return [
                'path' => $p,
                'name' => basename($p),
                'url'  => Storage::url($p),
            ];
        }, $archivos);
    }

    private function usuarioPuedeCambiarEstado(): bool
    {
        $u = Auth::user();
        if (!$u || !$u->roles_id) return false;
        $rol = RolesModel::find($u->roles_id);
        if (!$rol) return false;
        return $rol->tienePermiso('cambiar_estado_matricula') || $rol->nombre === 'Tesoreria' || $rol->tienePermiso('acceso_total');
    }

    private function usuarioPuedeSubirDocumentos(): bool
    {
        $u = Auth::user();
        if (!$u || !$u->roles_id) return false;
        $rol = RolesModel::find($u->roles_id);
        if (!$rol) return false;
        return $rol->tienePermiso('subir_documentos_matricula') || $rol->nombre === 'Acudiente' || $rol->tienePermiso('acceso_total');
    }

    private function mapearProceso(string $estado): array
    {
        $steps = [
            ['key' => 'en_proceso', 'label' => 'En proceso'],
            ['key' => 'activo', 'label' => 'Activo'],
            ['key' => 'inactivo', 'label' => 'Inactivo'],
        ];
        foreach ($steps as &$s) {
            $s['done'] = ($estado === 'activo') ? true : ($estado === 'inactivo' ? ($s['key'] === 'inactivo') : ($s['key'] === 'en_proceso'));
            $s['current'] = $s['key'] === $estado;
        }
        return $steps;
    }
}
