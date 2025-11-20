<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RolesModel;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;


class RolController extends Controller
{

    /**
     * Mostrar lista de todos los roles
     */
    public function index()
    {
        // Solo administradores y rectores pueden ver todos los roles
        if (!$this->usuarioPuedeGestionarRoles()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para acceder a esta sección.');
        }

        // Obtener todos los roles con el conteo de usuarios
        $roles = RolesModel::withCount('usuarios')->get();
        
        return view('roles.index', compact('roles'));
    }

    /**
     * Mostrar formulario para crear nuevo rol
     */
    public function crear()
    {
        if (!$this->usuarioPuedeGestionarRoles()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para crear roles.');
        }

        [$gruposPermisos, $permisosPlano] = $this->obtenerPermisosDisponibles();
        
        return view('roles.crear', [
            'gruposPermisos' => $gruposPermisos,
            'permisosDisponibles' => $permisosPlano,
        ]);
    }

    /**
     * Almacenar nuevo rol en la base de datos
     */
    public function guardar(Request $request)
    {
        if (!$this->usuarioPuedeGestionarRoles()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para crear roles.');
        }

        $datosValidados = $request->validate([
            'nombre' => 'required|string|max:255|unique:roles,nombre',
            'descripcion' => 'required|string|max:500',
            'permisos' => 'array'
        ], [
            'nombre.required' => 'El nombre del rol es obligatorio.',
            'nombre.unique' => 'Ya existe un rol con este nombre.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.'
        ]);

        $rol = RolesModel::create([
            'nombre' => $datosValidados['nombre'],
            'descripcion' => $datosValidados['descripcion'],
            'permisos' => $datosValidados['permisos'] ?? []
        ]);

        return redirect()->route('roles.index')
            ->with('success', 'Rol creado exitosamente.');
    }

    /**
     * Mostrar detalles de un rol específico
     */
    public function mostrar(RolesModel $rol)
    {
        if (!$this->usuarioPuedeGestionarRoles()) {
            return redirect('/dashboard')->with('error', 'No tienes permisos para ver esta información.');
        }

        // Cargar usuarios asociados con paginación
        $usuarios = $rol->usuarios()->paginate(10);
        [$gruposPermisos, $permisosPlano] = $this->obtenerPermisosDisponibles();
        
        return view('roles.mostrar', [
            'rol' => $rol,
            'usuarios' => $usuarios,
            'permisosDisponibles' => $permisosPlano,
            'gruposPermisos' => $gruposPermisos,
        ]);
    }

    /**
     * Mostrar formulario de edición de un rol
     */
    public function editar(RolesModel $rol)
    {
        if (!$this->usuarioPuedeGestionarRoles()) {
            return redirect()->route('roles.index')
                ->with('error', 'No tienes permisos para editar roles.');
        }
        [$gruposPermisos, $permisosPlano] = $this->obtenerPermisosDisponibles();
        $esRolSistema = $this->esRolDelSistema($rol->nombre);
        
        return view('roles.editar', [
            'rol' => $rol,
            'permisosDisponibles' => $permisosPlano,
            'gruposPermisos' => $gruposPermisos,
            'esRolSistema' => $esRolSistema,
        ]);
    }

    /**
     * Actualizar un rol existente
     */
    public function actualizar(Request $request, RolesModel $rol)
    {
        if (!$this->usuarioPuedeGestionarRoles()) {
            return redirect()->route('roles.index')
                ->with('error', 'No tienes permisos para actualizar roles.');
        }

        $esRolSistema = $this->esRolDelSistema($rol->nombre);

        // Reglas de validación
        $reglas = [
            'permisos' => 'array',
        ];

        // Los roles del sistema no pueden cambiar nombre ni descripción
        if (!$esRolSistema) {
            $reglas['nombre'] = 'required|string|max:255|unique:roles,nombre,' . $rol->id;
            $reglas['descripcion'] = 'required|string|max:500';
        } else {
            // Para roles del sistema, estos campos deben existir pero no se modifican
            $reglas['nombre'] = 'required|string';
            $reglas['descripcion'] = 'required|string';
        }

        $datosValidados = $request->validate($reglas, [
            'nombre.required' => 'El nombre del rol es obligatorio.',
            'nombre.unique' => 'Ya existe un rol con este nombre.',
            'descripcion.required' => 'La descripción es obligatoria.',
            'descripcion.max' => 'La descripción no puede exceder 500 caracteres.'
        ]);

        // Actualizar según el tipo de rol
        if ($esRolSistema) {
            // Solo actualizar permisos en roles del sistema
            $rol->permisos = $request->input('permisos', []);
        } else {
            // Actualizar todos los campos en roles personalizados
            $rol->nombre = $request->input('nombre');
            $rol->descripcion = $request->input('descripcion');
            $rol->permisos = $request->input('permisos', []);
        }

        $rol->save();

        return redirect()->route('roles.mostrar', $rol->id)
            ->with('success', 'Rol actualizado correctamente.');
    }

    /**
     * Eliminar un rol
     */
    public function eliminar(RolesModel $rol)
    {
        if (!$this->usuarioPuedeGestionarRoles()) {
            return redirect()->route('roles.index')
                ->with('error', 'No tienes permisos para eliminar roles.');
        }

        // Verificar si es un rol del sistema
        if ($this->esRolDelSistema($rol->nombre)) {
            return redirect()->route('roles.index')
                ->with('error', 'No se pueden eliminar roles del sistema.');
        }

        // Verificar si tiene usuarios asignados
        if ($rol->usuarios()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'No se puede eliminar un rol con usuarios asignados. Por favor, reasigne los usuarios primero.');
        }

        $nombreRol = $rol->nombre;
        $rol->delete();

        return redirect()->route('roles.index')
            ->with('success', "El rol '{$nombreRol}' fue eliminado correctamente.");
    }

    /**
     * Asignar un rol a un usuario (AJAX)
     */
    public function asignarRol(Request $request)
    {
        if (!$this->usuarioPuedeGestionarRoles()) {
            return response()->json(['exito' => false, 'error' => 'No autorizado'], 403);
        }

        $datos = $request->validate([
            'usuario_id' => 'required|exists:users,id',
            'rol_id' => 'required|exists:roles,id',
        ]);

        $usuario = User::find($datos['usuario_id']);
        $usuario->roles_id = $datos['rol_id'];
        $usuario->save();

        $rol = RolesModel::find($datos['rol_id']);

        return response()->json([
            'exito' => true,
            'mensaje' => "Rol '{$rol->nombre}' asignado exitosamente a {$usuario->name}."
        ]);
    }

    /**
     * Remover rol de un usuario (AJAX)
     */
    public function removerRol(Request $request)
    {
        if (!$this->usuarioPuedeGestionarRoles()) {
            return response()->json(['exito' => false, 'error' => 'No autorizado'], 403);
        }

        $datos = $request->validate([
            'usuario_id' => 'required|exists:users,id',
        ]);

        $usuario = User::find($datos['usuario_id']);
        $usuario->roles_id = null;
        $usuario->save();

        return response()->json([
            'exito' => true,
            'mensaje' => "Rol removido exitosamente de {$usuario->name}."
        ]);
    }

    /**
     * Obtener usuarios sin rol asignado (AJAX)
     */
    public function obtenerUsuariosSinRol()
    {
        if (!$this->usuarioPuedeGestionarRoles()) {
            return response()->json(['exito' => false, 'error' => 'No autorizado'], 403);
        }

        $usuarios = User::whereNull('roles_id')
            ->select('id', 'name', 'email')
            ->orderBy('name')
            ->get();

        return response()->json([
            'exito' => true,
            'datos' => $usuarios
        ]);
    }

    /**
     * Obtener lista de roles del sistema (AJAX)
     */
    public function obtenerRolesSistema()
    {
        if (!$this->usuarioPuedeGestionarRoles()) {
            return response()->json(['exito' => false, 'error' => 'No autorizado'], 403);
        }

        $rolesSistema = RolesModel::obtenerRolesSistema();
        
        return response()->json([
            'exito' => true,
            'datos' => $rolesSistema
        ]);
    }

    /**
     * Verificar si el usuario puede gestionar roles
     */
    private function usuarioPuedeGestionarRoles()
    {
        $usuario = Auth::user();
        
        if (!$usuario || !$usuario->roles_id) {
            return false;
        }

        $rol = RolesModel::find($usuario->roles_id);
        
        if (!$rol) {
            return false;
        }

        // Solo Admin y Rector pueden gestionar roles
        return in_array($rol->nombre, ['Admin', 'Rector']);
    }

    /**
     * Verificar si un rol es del sistema (no se puede eliminar)
     */
    private function esRolDelSistema($nombreRol)
    {
        $rolesSistema = [
            'Admin',
            'Rector',
            'CoordinadorDisciplina',
            'CoordinadorAcademico',
            'Acudiente',
            'Estudiante',
            'Docente',
            'Tesoreria',
        ];

        return in_array($nombreRol, $rolesSistema);
    }

    /**
     * Permisos disponibles agrupados y en plano
     */
    private function obtenerPermisosDisponibles()
    {
        $grupos = [
            'Usuarios' => [
                'gestionar_usuarios' => 'Gestionar usuarios',
                'gestionar_roles' => 'Gestionar roles y permisos',
            ],
            'Estudiantes' => [
                'gestionar_estudiantes' => 'Ver y gestionar estudiantes',
                'matricular_estudiantes' => 'Acceder a módulo de matrículas',
            ],
            'Docentes' => [
                'gestionar_docentes' => 'Ver y gestionar docentes',
            ],
            'Cursos' => [
                'gestionar_cursos' => 'Ver y gestionar cursos',
            ],
            'Materias' => [
                'gestionar_materias' => 'Ver y gestionar materias',
            ],
            'Disciplina' => [
                'gestionar_disciplina' => 'Ver y gestionar disciplina',
            ],
            'Notas' => [
                'gestionar_notas' => 'Ver y gestionar notas',
            ],
            'Reportes' => [
                'ver_reportes_generales' => 'Ver reportes generales del colegio',
                'ver_reportes_academicos' => 'Ver reportes académicos',
                'ver_reportes_financieros' => 'Ver reportes financieros',
                'generar_reportes' => 'Generar reportes personalizados',
                'exportar_reportes' => 'Exportar reportes a Excel/PDF',
            ],
            'Matriculas' => [
                'cambiar_estado_matricula' => 'Cambiar estado de la matrícula (Tesorería)',
                'subir_documentos_matricula' => 'Subir documentos requeridos (Acudiente)',
            ],
            'Finanzas' => [
                'gestionar_pagos' => 'Registrar y gestionar pagos',
                'ver_pagos' => 'Ver información de pagos',
                'generar_recibos' => 'Generar recibos de pago',
                'configurar_pensiones' => 'Configurar valores de pensión',
            ],
            'Configuracion' => [
                'configurar_sistema' => 'Configurar sistema',
            ],
            'Seguridad' => [
                'acceso_total' => 'Acceso total al sistema (Super Admin)',
            ],
            'Personales' => [
                'ver_perfil_propio' => 'Ver su propio perfil',
                'editar_perfil_propio' => 'Editar su propio perfil',
                'cambiar_contrasena' => 'Cambiar su propia contraseña',
                'ver_notificaciones' => 'Ver notificaciones personales',
            ],
            'Otros' => [
                'aprobar_permisos' => 'Aprobar solicitudes de permisos',
            ],
        ];

        $plano = [];
        foreach ($grupos as $grupo) {
            foreach ($grupo as $k => $v) {
                $plano[$k] = $v;
            }
        }

        return [$grupos, $plano];
    }
}
