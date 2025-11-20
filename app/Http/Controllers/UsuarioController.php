<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\RolesModel;

class UsuarioController extends Controller
{
    /**
     * Listado de usuarios con filtro por rol.
     */
    public function index(Request $request)
    {
        $usuario = Auth::user();
        if (!$usuario) {
            return redirect()->route('login');
        }

        $rolUsuario = RolesModel::find($usuario->roles_id);
        if (!$rolUsuario || !$rolUsuario->tienePermiso('gestionar_usuarios')) {
            return redirect()->route('dashboard')->with('error', 'No tienes permisos para gestionar usuarios.');
        }

        $rolFiltro = $request->query('rol');

        $query = User::with('rol')->orderBy('name');
        if ($rolFiltro === 'sin_rol') {
            $query->whereNull('roles_id');
        } elseif (!empty($rolFiltro)) {
            $query->where('roles_id', $rolFiltro);
        }

        $usuarios = $query->paginate(10)->appends($request->query());

        $roles = RolesModel::orderBy('nombre')->get(['id', 'nombre']);

        return view('usuarios.index', [
            'usuarios' => $usuarios,
            'roles' => $roles,
            'rolFiltro' => $rolFiltro,
        ]);
    }
}
