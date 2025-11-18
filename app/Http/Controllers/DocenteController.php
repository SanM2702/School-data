<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use Illuminate\Http\Request;

class DocenteController extends Controller
{
    public function index(Request $request)
    {
        $area = $request->query('area');

        $query = Docente::with('persona');

        if ($area) {
            $query->where('area', $area);
        }

        $docentes = $query->get();

        $areas = Docente::whereNotNull('area')
            ->where('area', '<>', '')
            ->distinct()
            ->pluck('area');

        return view('docentes.index', compact('docentes', 'areas', 'area'));
    }

    public function mostrar($idDocente)
    {
        $docente = Docente::with('persona')->findOrFail($idDocente);
        return view('docentes.mostrar', compact('docente'));
    }
}
