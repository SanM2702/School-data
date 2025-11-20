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
        $totalEstudiantes = Estudiante::count();
        $totalDocentes = Docente::count();
        $totalCursos = Curso::count();
        $totalUsuarios = User::count();
        $recentActivities = Activity::with('user')->orderByDesc('id')->limit(10)->get();

        return view('dashboard', compact(
            'totalEstudiantes',
            'totalDocentes',
            'totalCursos',
            'totalUsuarios',
            'recentActivities'
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
