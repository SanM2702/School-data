<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\CrearUsuario;
use App\Http\Controllers\RolController;
use App\Http\Controllers\DisciplinaController;

// Ruta raíz redirige al login
Route::get('/', function () {
    return redirect('/login');
});

// Rutas de autenticación
Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Rutas Crear usuarios
Route::get('/register', [CrearUsuario::class, 'showRegistrationForm'])->name('register');
Route::post('/register', [CrearUsuario::class, 'register']);


// Rutas protegidas por autenticación
Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', [AuthController::class, 'dashboard'])->name('dashboard');
        
    // Rutas de estudiantes
    Route::get('/estudiantes', [App\Http\Controllers\EstudianteController::class, 'index'])->name('estudiantes.index');
    // Formulario de nuevo estudiante (debe ir antes de las rutas con parámetro)
    Route::get('/estudiantes/nuevo', [App\Http\Controllers\EstudianteController::class, 'nuevo'])->name('estudiantes.nuevo');
    // Student photo serve and upload
    Route::get('/estudiantes/{id}/foto', [App\Http\Controllers\EstudianteController::class, 'foto'])->name('estudiantes.foto');
    Route::post('/estudiantes/{id}/foto', [App\Http\Controllers\EstudianteController::class, 'updateFoto'])->name('estudiantes.updateFoto');
        
    // Docentes
    Route::get('/docentes', [App\Http\Controllers\DocenteController::class, 'index'])->name('docentes.index');
    Route::get('/docentes/agregar', [App\Http\Controllers\DocenteController::class, 'agregar'])->name('docentes.agregar');
    Route::post('/docentes', [App\Http\Controllers\DocenteController::class, 'store'])->name('docentes.store');
    Route::get('/docentes/{idDocente}', [App\Http\Controllers\DocenteController::class, 'mostrar'])->name('docentes.mostrar');
    
    // Cursos
    Route::get('/cursos', [App\Http\Controllers\CursoController::class, 'index'])->name('cursos.index');
    Route::get('/cursos/{curso}/edit', [App\Http\Controllers\CursoController::class, 'edit'])->name('cursos.edit');
    Route::put('/cursos/{curso}', [App\Http\Controllers\CursoController::class, 'update'])->name('cursos.update');
    
    // Materias
    Route::get('/materias', [App\Http\Controllers\MateriaController::class, 'index'])->name('materias.index');
    Route::get('/materias/agregar', [App\Http\Controllers\MateriaController::class, 'create'])->name('materias.agregar');
    Route::post('/materias', [App\Http\Controllers\MateriaController::class, 'store'])->name('materias.store');
    Route::get('/materias/{materia}/editar', [App\Http\Controllers\MateriaController::class, 'edit'])->name('materias.editar');
    Route::put('/materias/{materia}', [App\Http\Controllers\MateriaController::class, 'update'])->name('materias.update');
    Route::delete('/materias/{materia}', [App\Http\Controllers\MateriaController::class, 'destroy'])->name('materias.eliminar');
    
    // Disciplina
    Route::get('/disciplina', [DisciplinaController::class, 'index'])->name('disciplina.index');
    Route::get('/disciplina/agregar', [DisciplinaController::class, 'create'])->name('disciplina.agregar');
    Route::post('/disciplina', [DisciplinaController::class, 'store'])->name('disciplina.store');
    Route::get('/disciplina/{disciplina}', [DisciplinaController::class, 'show'])->name('disciplina.mostrar');
    Route::get('/disciplina/{disciplina}/editar', [DisciplinaController::class, 'edit'])->name('disciplina.editar');
    Route::put('/disciplina/{disciplina}', [DisciplinaController::class, 'update'])->name('disciplina.actualizar');
    
    // Notas
    Route::get('/notas', [App\Http\Controllers\NotasController::class, 'index'])->name('notas.index');
    Route::get('/notas/curso/{curso}', [App\Http\Controllers\NotasController::class, 'mostrar'])->name('notas.mostrar');
    Route::get('/notas/curso/{curso}/editar', [App\Http\Controllers\NotasController::class, 'editar'])->name('notas.editar');
    Route::post('/notas/curso/{curso}', [App\Http\Controllers\NotasController::class, 'actualizar'])->name('notas.actualizar');
    
    // Estudiantes
    Route::get('/estudiantes/{idEstudiante}', [App\Http\Controllers\EstudianteController::class, 'mostrar'])->name('estudiantes.mostrar');
    Route::get('/estudiantes/{idEstudiante}/editar', [App\Http\Controllers\EstudianteController::class, 'editar'])->name('estudiantes.editar');
    Route::post('/estudiantes', [App\Http\Controllers\EstudianteController::class, 'store'])->name('estudiantes.store');
    Route::post('/estudiantes/documento/{noDocumento}/acudiente', [App\Http\Controllers\EstudianteController::class, 'storeAcudiente'])->name('estudiantes.acudientes.store');
    Route::post('/estudiantes/documento/{noDocumento}/matricula', [App\Http\Controllers\EstudianteController::class, 'storeMatricula'])->name('estudiantes.matricula.store');
    Route::get('/estudiantes/documento/{noDocumento}', [App\Http\Controllers\EstudianteController::class, 'info'])->name('estudiantes.info');
    

    
    // Actualización de contacto
    Route::post('/estudiantes/{idEstudiante}/contacto', [App\Http\Controllers\EstudianteController::class, 'updateContacto'])->name('estudiantes.contacto.update');
    Route::post('/estudiantes/{idEstudiante}/acudiente/contacto', [App\Http\Controllers\EstudianteController::class, 'updateContactoAcudiente'])->name('estudiantes.acudiente.contacto.update');
    
    // Rutas de gestión de roles
    Route::prefix('roles')->name('roles.')->group(function () {
        // Rutas web principales
        Route::get('/', [RolController::class, 'index'])->name('index');
        Route::get('/crear', [RolController::class, 'crear'])->name('crear');
        Route::post('/', [RolController::class, 'guardar'])->name('guardar');
        Route::get('/{rol}', [RolController::class, 'mostrar'])->name('mostrar');
        Route::get('/{rol}/editar', [RolController::class, 'editar'])->name('editar');
        Route::put('/{rol}', [RolController::class, 'actualizar'])->name('actualizar');
        Route::delete('/{rol}', [RolController::class, 'eliminar'])->name('eliminar');
        
        // Rutas AJAX para gestión de roles
        Route::post('/asignar-rol', [RolController::class, 'asignarRol'])->name('asignar');
        Route::post('/remover-rol', [RolController::class, 'removerRol'])->name('remover');
        Route::get('/usuarios-sin-rol', [RolController::class, 'obtenerUsuariosSinRol'])->name('usuarios-sin-rol');
        Route::get('/roles-sistema', [RolController::class, 'obtenerRolesSistema'])->name('roles-sistema');
    });
});