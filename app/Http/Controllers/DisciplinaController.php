<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Disciplina;
use App\Models\Estudiante;
use App\Models\RolesModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DisciplinaController extends Controller
{
    public function index(Request $request)
    {
        $query = Disciplina::with(['estudiante.persona', 'estudiante.curso']);

        if ($request->filled('estudiante')) {
            $query->where('estudiante_id', $request->integer('estudiante'));
        }

        if ($request->filled('curso')) {
            $cursoNombre = $request->input('curso');
            $query->whereHas('estudiante.curso', function ($q) use ($cursoNombre) {
                $q->where('nombre', $cursoNombre);
            });
        }

        if ($request->filled('grupo')) {
            $grupo = $request->input('grupo');
            $query->whereHas('estudiante.curso', function ($q) use ($grupo) {
                $q->where('grupo', $grupo);
            });
        }

        $disciplinas = $query->orderByDesc('fecha')->paginate(15)->withQueryString();

        // Para colorear filas por recurrencia por estudiante
        $conteoPorEstudiante = Disciplina::selectRaw('estudiante_id, COUNT(*) as total')
            ->groupBy('estudiante_id')
            ->pluck('total', 'estudiante_id');

        // Listas para filtros
        $estudiantes = Estudiante::with('persona')
            ->get()
            ->map(function ($e) {
                $nombre = trim(($e->persona->primerNombre ?? '') . ' ' . ($e->persona->segundoNombre ?? '') . ' ' . ($e->persona->primerApellido ?? '') . ' ' . ($e->persona->segundoApellido ?? ''));
                return [
                    'id' => $e->idEstudiante,
                    'nombre' => $nombre ?: ('Estudiante #' . $e->idEstudiante),
                ];
            });

        $grupos = Curso::query()
            ->whereNotNull('grupo')
            ->distinct()
            ->orderBy('grupo')
            ->pluck('grupo');

        $cursos = Curso::query()
            ->whereNotNull('nombre')
            ->distinct()
            ->orderBy('nombre')
            ->pluck('nombre');

        return view('disciplina.index', compact('disciplinas', 'conteoPorEstudiante', 'estudiantes', 'grupos', 'cursos'));
    }

    public function show(Disciplina $disciplina)
    {
        $disciplina->load(['estudiante.persona', 'estudiante.curso']);

        // Si el usuario actual es Acudiente (o Admin), marcar notificado_acudiente
        $usuario = Auth::user();
        if ($usuario) {
            $rol = RolesModel::find($usuario->roles_id);
            $nombreRol = $rol->nombre ?? '';
            $permisos = $rol->permisos ?? [];
            $isAdmin = in_array('acceso_total', $permisos) || $nombreRol === 'Admin';
            if (($nombreRol === 'Acudiente' || $isAdmin) && !$disciplina->notificado_acudiente) {
                $disciplina->forceFill(['notificado_acudiente' => true])->save();
            }
        }
        return view('disciplina.mostrar', compact('disciplina'));
    }

    public function edit(Disciplina $disciplina)
    {
        $disciplina->load(['estudiante.persona', 'estudiante.curso']);

        $usuario = Auth::user();
        $rol = $usuario ? RolesModel::find($usuario->roles_id) : null;
        $nombreRol = $rol->nombre ?? '';
        $permisos = $rol->permisos ?? [];

        $isAdmin = in_array('acceso_total', $permisos) || $nombreRol === 'Admin';
        $canStaff = $isAdmin || in_array($nombreRol, ['Rector', 'CoordinadorDisciplina', 'Coordinador de convivencia', 'CoordinadorConvivencia']) || ($rol && $rol->tienePermiso('gestionar_disciplina'));
        $canAcudiente = $isAdmin || $nombreRol === 'Acudiente';

        $estados = ['Registrado','Revisado','Sancionado','Cerrado','En apelación'];

        return view('disciplina.editar', compact('disciplina', 'canStaff', 'canAcudiente', 'estados'));
    }

    public function update(Request $request, Disciplina $disciplina)
    {
        $usuario = Auth::user();
        $rol = $usuario ? RolesModel::find($usuario->roles_id) : null;
        $nombreRol = $rol->nombre ?? '';
        $permisos = $rol->permisos ?? [];

        $isAdmin = in_array('acceso_total', $permisos) || $nombreRol === 'Admin';
        $canStaff = $isAdmin || in_array($nombreRol, ['Rector', 'CoordinadorDisciplina', 'Coordinador de convivencia', 'CoordinadorConvivencia']) || ($rol && $rol->tienePermiso('gestionar_disciplina'));
        $canAcudiente = $isAdmin || $nombreRol === 'Acudiente';

        $rules = [];
        if ($canStaff) {
            $rules['estado'] = 'required|string|in:Registrado,Revisado,Sancionado,Cerrado,En apelación';
            $rules['observaciones'] = 'nullable|string';
        }
        if ($canAcudiente) {
            $rules['respuesta_acudiente'] = 'nullable|string';
        }

        $data = $request->validate($rules);

        // Asegurar que solo se actualicen los campos permitidos
        $update = [];
        if ($canStaff) {
            if (array_key_exists('estado', $data)) $update['estado'] = $data['estado'];
            if (array_key_exists('observaciones', $data)) $update['observaciones'] = $data['observaciones'];
        }
        if ($canAcudiente && array_key_exists('respuesta_acudiente', $data)) {
            $update['respuesta_acudiente'] = $data['respuesta_acudiente'];
            // Si el acudiente responde, marcamos confirmación
            if (isset($data['respuesta_acudiente'])) {
                $update['confirmacion_acudiente'] = true;
            }
        }

        if (!empty($update)) {
            $disciplina->update($update);
        }

        return redirect()->route('disciplina.mostrar', $disciplina)->with('status', 'Caso actualizado');
    }

    public function create()
    {
        $usuario = Auth::user();
        $rol = $usuario ? RolesModel::find($usuario->roles_id) : null;
        $nombreRol = $rol->nombre ?? '';
        $permisos = $rol->permisos ?? [];
        $isAdmin = in_array('acceso_total', $permisos) || $nombreRol === 'Admin';
        $canStaff = $isAdmin || in_array($nombreRol, ['Rector', 'CoordinadorDisciplina', 'Coordinador de convivencia', 'CoordinadorConvivencia']) || ($rol && $rol->tienePermiso('gestionar_disciplina'));

        abort_unless($canStaff, 403);

        $estudiantes = Estudiante::with(['persona','curso'])->get();
        $estados = ['Registrado','Revisado','Sancionado','Cerrado','En apelación'];
        $cargos = ['Docente','Coordinador de convivencia','CoordinadorDisciplina','Rector'];
        $tiposFalta = ['leve','moderado','grave','muy_grave'];

        return view('disciplina.agregar', compact('estudiantes','estados','cargos','tiposFalta'));
    }

    public function store(Request $request)
    {
        $usuario = Auth::user();
        $rol = $usuario ? RolesModel::find($usuario->roles_id) : null;
        $nombreRol = $rol->nombre ?? '';
        $permisos = $rol->permisos ?? [];
        $isAdmin = in_array('acceso_total', $permisos) || $nombreRol === 'Admin';
        $canStaff = $isAdmin || in_array($nombreRol, ['Rector', 'CoordinadorDisciplina', 'Coordinador de convivencia', 'CoordinadorConvivencia']) || ($rol && $rol->tienePermiso('gestionar_disciplina'));

        abort_unless($canStaff, 403);

        $data = $request->validate([
            'estudiante_id' => 'required|exists:estudiantes,idEstudiante',
            'tipo_falta' => 'required|in:leve,moderado,grave,muy_grave',
            'descripcion' => 'required|string',
            'fecha' => 'required|date',
            'notificado_estudiante' => 'nullable|boolean',
            'presentador_nombre' => 'required|string|max:255',
            'presentador_cargo' => 'required|string|max:255',
            'estado' => 'required|in:Registrado,Revisado,Sancionado,Cerrado,En apelación',
            'tipo_sancion' => 'nullable|string|max:255',
            'observaciones' => 'nullable|string',
        ]);

        $data['notificado_acudiente'] = false;
        $data['confirmacion_acudiente'] = false;

        $disciplina = Disciplina::create($data);

        return redirect()->route('disciplina.mostrar', $disciplina)->with('status', 'Sanción creada');
    }
}
