<?php

namespace App\Http\Controllers;

use App\Models\Estudiante;
use Illuminate\Http\Request;

class EstudianteController extends Controller
{
    /**
     * Display a listing of the students.
     */
    public function index()
    {
        $estudiantes = Estudiante::with('persona')->get();

        return view('estudiantes.index', compact('estudiantes'));
    }

    /**
     * Show the form to create a new student.
     */
    public function nuevo()
    {
        return view('estudiantes.nuevo');
    }
}
