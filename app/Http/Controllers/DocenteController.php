<?php

namespace App\Http\Controllers;

use App\Models\Docente;
use App\Models\Persona;
use Illuminate\Support\Facades\DB;
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

    public function agregar()
    {
        return view('docentes.agregar');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'primerNombre'   => ['required','string','max:100'],
            'segundoNombre'  => ['nullable','string','max:100'],
            'primerApellido' => ['required','string','max:100'],
            'segundoApellido'=> ['nullable','string','max:100'],
            'noDocumento'    => ['required','string','max:50','unique:personas,noDocumento'],
            'telefono'       => ['nullable','string','max:50'],
            'email'          => ['nullable','email','max:255'],
            'fechaNacimiento'=> ['nullable','date'],
            'estado'         => ['nullable','string','max:50'],
            'area'           => ['nullable','string','max:150'],
            'linkedin_url'   => ['nullable','url','max:255'],
        ]);

        DB::transaction(function() use ($data) {
            $persona = Persona::create([
                'primerNombre'   => $data['primerNombre'],
                'segundoNombre'  => $data['segundoNombre'] ?? null,
                'primerApellido' => $data['primerApellido'],
                'segundoApellido'=> $data['segundoApellido'] ?? null,
                'telefono'       => $data['telefono'] ?? null,
                'email'          => $data['email'] ?? null,
                'noDocumento'    => $data['noDocumento'],
                'fechaNacimiento'=> $data['fechaNacimiento'] ?? null,
                'estado'         => $data['estado'] ?? 'activo',
            ]);

            Docente::create([
                'idPersona' => $persona->idPersona,
                'area'      => $data['area'] ?? null,
                'linkedin_url' => $data['linkedin_url'] ?? null,
            ]);
        });

        return redirect()->route('docentes.index')->with('status', 'Docente creado correctamente.');
    }

    public function mostrar($idDocente)
    {
        $docente = Docente::with('persona')->findOrFail($idDocente);
        return view('docentes.mostrar', compact('docente'));
    }
}
