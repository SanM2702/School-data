<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    /**
     * Muestra la vista principal de configuración.
     */
    public function index()
    {
        return view('configuracion.index');
    }
}
