<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Estudiante;
use App\Models\Docente;
use App\Models\Curso;
use App\Models\Activity;

class AuthController extends Controller
{
    // Mostrar formulario de login
    public function showLoginForm()
    {
        return view('auth.login');
    }

    // Procesar el login
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('dashboard');
        }

        return back()->withErrors([
            'email' => 'Las credenciales proporcionadas no coinciden con nuestros registros.',
        ])->onlyInput('email');
    }

    // Mostrar dashboard/menú principal
    public function dashboard()
    {
        $usuario = Auth::user();
        $rol = \App\Models\RolesModel::find($usuario->roles_id);
        
        // Datos generales del sistema
        $totalEstudiantes = Estudiante::count();
        $totalDocentes = Docente::count();
        $totalCursos = Curso::count();
        $totalUsuarios = User::count();
        $recentActivities = Activity::with('user')->orderByDesc('id')->limit(10)->get();
        
        // Datos específicos para estudiantes
        $estudianteData = null;
        $mejoresNotas = null;
        $cantidadFaltas = 0;
        $cursoEstudiante = null;
        
        // Datos específicos para docentes
        $docenteData = null;
        $numeroCursos = 0;
        $numeroEstudiantes = 0;
        $cantidadSanciones = 0;
        $numeroAreas = 0;
        
        // Datos específicos para acudientes
        $acudienteData = null;
        $estudianteAcudiente = null;
        $mejorNotaAcudiente = null;
        $cursoAcudiente = null;
        $faltasAcudiente = 0;
        $estadoMatricula = 'Sin matrícula';
        
        // Datos específicos para Coordinador de Disciplina
        $totalEstudiantesCoord = 0;
        $totalFaltasCoord = 0;
        $totalCursosCoord = 0;
        
        // Datos específicos para Coordinador Académico
        $totalEstudiantesAcad = 0;
        $totalDocentesAcad = 0;
        $totalCursosAcad = 0;
        $totalMateriasAcad = 0;
        
        if ($rol && $rol->nombre === 'Estudiante') {
            // Buscar el estudiante asociado al usuario
            $estudiante = Estudiante::whereHas('persona', function($query) use ($usuario) {
                $query->where('email', $usuario->email);
            })->with(['curso', 'notas.materia'])->first();
            
            if ($estudiante) {
                $estudianteData = $estudiante;
                $cursoEstudiante = $estudiante->curso;
                
                // Obtener las dos mejores notas
                $mejoresNotas = $estudiante->notas()
                    ->with('materia')
                    ->orderByDesc('valor')
                    ->limit(2)
                    ->get();
                
                // Contar faltas disciplinarias
                $cantidadFaltas = \App\Models\Disciplina::where('estudiante_id', $estudiante->idEstudiante)->count();
            }
        } elseif ($rol && $rol->nombre === 'Docente') {
            // Buscar el docente asociado al usuario
            $docente = Docente::whereHas('persona', function($query) use ($usuario) {
                $query->where('email', $usuario->email);
            })->with(['materias.curso'])->first();
            
            if ($docente) {
                $docenteData = $docente;
                
                // Número de materias que imparte
                $materias = $docente->materias;
                
                // Número de cursos únicos
                $cursosIds = $materias->pluck('curso_id')->unique()->filter();
                $numeroCursos = $cursosIds->count();
                
                // Número de estudiantes (suma de estudiantes de todos los cursos)
                $numeroEstudiantes = Estudiante::whereIn('curso_id', $cursosIds)->count();
                
                // Cantidad de sanciones/disciplinas que ha registrado
                $cantidadSanciones = \App\Models\Disciplina::where('presentador_nombre', 'LIKE', '%' . ($docente->persona->primerNombre ?? '') . '%')
                    ->orWhere('presentador_nombre', 'LIKE', '%' . ($docente->persona->primerApellido ?? '') . '%')
                    ->count();
                
                // Número de áreas únicas asignadas (basado en el área del docente)
                $numeroAreas = $docente->area ? 1 : 0;
            }
        } elseif ($rol && $rol->nombre === 'Acudiente') {
            // Buscar el acudiente asociado al usuario
            $acudiente = \App\Models\Acudiente::whereHas('persona', function($query) use ($usuario) {
                $query->where('email', $usuario->email);
            })->with(['estudiantes.curso', 'estudiantes.notas.materia'])->first();
            
            if ($acudiente && $acudiente->estudiantes->count() > 0) {
                $acudienteData = $acudiente;
                // Tomar el primer estudiante asociado
                $estudiante = $acudiente->estudiantes->first();
                $estudianteAcudiente = $estudiante;
                $cursoAcudiente = $estudiante->curso;
                
                // Obtener la mejor nota del estudiante
                $mejorNota = $estudiante->notas()
                    ->with('materia')
                    ->orderByDesc('valor')
                    ->first();
                $mejorNotaAcudiente = $mejorNota;
                
                // Contar faltas disciplinarias
                $faltasAcudiente = \App\Models\Disciplina::where('estudiante_id', $estudiante->idEstudiante)->count();
                
                // Estado de matrícula
                $matricula = \App\Models\Matricula::where('idEstudiante', $estudiante->idEstudiante)
                    ->orderByDesc('created_at')
                    ->first();
                $estadoMatricula = $matricula ? ($matricula->estado ?? 'Activa') : 'Sin matrícula';
            }
        } elseif ($rol && $rol->nombre === 'CoordinadorDisciplina') {
            // Estadísticas para Coordinador de Disciplina
            $totalEstudiantesCoord = Estudiante::count();
            $totalFaltasCoord = \App\Models\Disciplina::count();
            $totalCursosCoord = Curso::count();
        } elseif ($rol && $rol->nombre === 'CoordinadorAcademico') {
            // Estadísticas para Coordinador Académico
            $totalEstudiantesAcad = Estudiante::count();
            $totalDocentesAcad = Docente::count();
            $totalCursosAcad = Curso::count();
            $totalMateriasAcad = \App\Models\Materia::count();
        }

        return view('dashboard', compact(
            'totalEstudiantes',
            'totalDocentes',
            'totalCursos',
            'totalUsuarios',
            'recentActivities',
            'estudianteData',
            'mejoresNotas',
            'cantidadFaltas',
            'cursoEstudiante',
            'docenteData',
            'numeroCursos',
            'numeroEstudiantes',
            'cantidadSanciones',
            'numeroAreas',
            'acudienteData',
            'estudianteAcudiente',
            'mejorNotaAcudiente',
            'cursoAcudiente',
            'faltasAcudiente',
            'estadoMatricula',
            'totalEstudiantesCoord',
            'totalFaltasCoord',
            'totalCursosCoord',
            'totalEstudiantesAcad',
            'totalDocentesAcad',
            'totalCursosAcad',
            'totalMateriasAcad'
        ));
    }

    // Procesar logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }
}
