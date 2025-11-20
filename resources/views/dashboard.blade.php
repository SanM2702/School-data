@extends('layouts.app')

@section('title', 'Dashboard - Colegio')

@section('content')
<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="{{ route('dashboard') }}">
            <i class="fas fa-file-invoice-dollar me-2"></i>Colegio
        </a>
        
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user me-1"></i>{{ Auth::user()->name }}
                    </a>
                    <ul class="dropdown-menu">
                        <li>
                            <a class="dropdown-item" href="#">
                                <i class="fas fa-user-cog me-1"></i>Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="{{ route('configuracion.index') }}">
                                <i class="fas fa-cog me-1"></i>Configuración
                            </a>
                        </li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item" href="{{ route('logout') }}" 
                               onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                <i class="fas fa-sign-out-alt me-1"></i>Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </li>
            </ul>
        </div>
    </div>
</nav>

<div class="container-fluid">
    @php
        $usuario = Auth::user();
        $rol = App\Models\RolesModel::find($usuario->roles_id);
    @endphp
    <div class="row">
        <!-- Sidebar -->
        <div class="col-md-3 col-lg-2 p-0">
            <div class="sidebar">
                <div class="p-3">
                    <h6 class="text-white-50 text-uppercase">Menú Principal</h6>
                </div>
                <nav class="nav flex-column px-3">
                    <a class="nav-link active" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    @if($rol)
                        @if($rol->tienePermiso('gestionar_estudiantes'))
                            <a class="nav-link" href="{{ route('estudiantes.index') }}">
                                <i class="fas fa-user-graduate me-2"></i>Estudiantes
                            </a>
                        @endif
                        @if($rol->tienePermiso('gestionar_docentes'))
                            <a class="nav-link" href="{{ route('docentes.index') }}">
                                <i class="fas fa-chalkboard-teacher me-2"></i>Docentes
                            </a>
                        @endif
                        @if($rol->tienePermiso('gestionar_cursos'))
                            <a class="nav-link" href="{{ route('cursos.index') }}">
                                <i class="fas fa-layer-group me-2"></i>Cursos
                            </a>
                        @endif
                        @if($rol->tienePermiso('gestionar_materias'))
                            <a class="nav-link" href="{{ route('materias.index') }}">
                                <i class="fas fa-book-open me-2"></i>Materias
                            </a>
                        @endif
                        @if($rol->tienePermiso('gestionar_disciplina'))
                            <a class="nav-link" href="{{ route('disciplina.index') }}">
                                <i class="fas fa-gavel me-2"></i>Disciplina
                            </a>
                        @endif
                        @if($rol->tienePermiso('gestionar_notas'))
                            <a class="nav-link" href="{{ route('notas.index') }}">
                                <i class="fas fa-book me-2"></i>Notas
                            </a>
                        @endif
                        @if($rol->tienePermiso('ver_reportes_generales'))
                            <a class="nav-link" href="{{ route('reportes.index') }}">
                                <i class="fas fa-chart-bar me-2"></i>Reportes
                            </a>
                        @endif
                        @if($rol->tienePermiso('matricular_estudiantes'))
                            <a class="nav-link" href="{{ route('matriculas.index') }}">
                                <i class="fas fa-user-check me-2"></i>Matriculas
                            </a>
                        @endif
                        @if($rol->tienePermiso('gestionar_roles'))
                            <a class="nav-link" href="{{ route('roles.index') }}">
                                <i class="fas fa-user-shield me-2"></i>Roles y Permisos
                            </a>
                        @endif
                        @if($rol->tienePermiso('gestionar_usuarios'))
                            <a class="nav-link" href="{{ route('usuarios.index') }}">
                                <i class="fas fa-users-cog me-2"></i>Gestión de Usuarios
                            </a>
                        @endif
                        @if($rol->tienePermiso('configurar_sistema'))
                            <a class="nav-link" href="{{ route('configuracion.index') }}">
                                <i class="fas fa-cog me-2"></i>Configuración
                            </a>
                        @endif
                    @endif
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10">
            <div class="main-content p-4">
                <!-- Welcome Section -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h1 class="h3 text-dark">¡Bienvenido, {{ Auth::user()->name }}!</h1>
                        <p class="text-muted">Gestiona tus Colegio de manera eficiente</p>
                    </div>
                </div>

                <!-- Statistics Cards -->
                <div class="row mb-4">
                    @if($rol && $rol->nombre === 'Estudiante')
                        <!-- Tarjetas para Estudiantes -->
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-primary mb-2">
                                        <i class="fas fa-graduation-cap fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Mi Curso</h5>
                                    <h3 class="text-primary">{{ $cursoEstudiante->nombre ?? 'N/A' }}</h3>
                                    <small class="text-muted">{{ $cursoEstudiante->grado ?? '' }} {{ $cursoEstudiante->grupo ?? '' }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-success mb-2">
                                        <i class="fas fa-star fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Mejores Notas</h5>
                                    @if($mejoresNotas && $mejoresNotas->count() > 0)
                                        @foreach($mejoresNotas as $nota)
                                            <div class="mb-2">
                                                <h5 class="text-success mb-0">{{ $nota->valor }}</h5>
                                                <small class="text-muted">{{ $nota->materia->nombre ?? 'N/A' }}</small>
                                            </div>
                                        @endforeach
                                    @else
                                        <h3 class="text-muted">-</h3>
                                        <small class="text-muted">Sin notas registradas</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-danger mb-2">
                                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Faltas Disciplinarias</h5>
                                    <h3 class="text-danger">{{ $cantidadFaltas ?? 0 }}</h3>
                                    <small class="text-muted">Registros disciplinarios</small>
                                </div>
                            </div>
                        </div>
                    @elseif($rol && $rol->nombre === 'Docente')
                        <!-- Tarjetas para Docentes -->
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-primary mb-2">
                                        <i class="fas fa-layer-group fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Mis Cursos</h5>
                                    <h3 class="text-primary">{{ $numeroCursos ?? 0 }}</h3>
                                    <small class="text-muted">Cursos asignados</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-success mb-2">
                                        <i class="fas fa-user-graduate fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Mis Estudiantes</h5>
                                    <h3 class="text-success">{{ $numeroEstudiantes ?? 0 }}</h3>
                                    <small class="text-muted">Total de estudiantes</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-warning mb-2">
                                        <i class="fas fa-gavel fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Sanciones Registradas</h5>
                                    <h3 class="text-warning">{{ $cantidadSanciones ?? 0 }}</h3>
                                    <small class="text-muted">Disciplinas reportadas</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-info mb-2">
                                        <i class="fas fa-book-open fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Áreas Asignadas</h5>
                                    <h3 class="text-info">{{ $numeroAreas ?? 0 }}</h3>
                                    <small class="text-muted">{{ $docenteData->area ?? 'Sin área' }}</small>
                                </div>
                            </div>
                        </div>
                    @elseif($rol && $rol->nombre === 'Acudiente')
                        <!-- Tarjetas para Acudientes -->
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-success mb-2">
                                        <i class="fas fa-star fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Mejor Nota</h5>
                                    @if($mejorNotaAcudiente)
                                        <h3 class="text-success">{{ $mejorNotaAcudiente->valor }}</h3>
                                        <small class="text-muted">{{ $mejorNotaAcudiente->materia->nombre ?? 'N/A' }}</small>
                                    @else
                                        <h3 class="text-muted">-</h3>
                                        <small class="text-muted">Sin notas</small>
                                    @endif
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-primary mb-2">
                                        <i class="fas fa-graduation-cap fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Curso del Estudiante</h5>
                                    <h3 class="text-primary">{{ $cursoAcudiente->nombre ?? 'N/A' }}</h3>
                                    <small class="text-muted">{{ $cursoAcudiente->grado ?? '' }} {{ $cursoAcudiente->grupo ?? '' }}</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-danger mb-2">
                                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Faltas Disciplinarias</h5>
                                    <h3 class="text-danger">{{ $faltasAcudiente ?? 0 }}</h3>
                                    <small class="text-muted">Registros disciplinarios</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-warning mb-2">
                                        <i class="fas fa-file-invoice fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Estado de Matrícula</h5>
                                    <h3 class="text-warning">{{ $estadoMatricula ?? 'Sin matrícula' }}</h3>
                                    <small class="text-muted">Estado actual</small>
                                </div>
                            </div>
                        </div>
                    @elseif($rol && $rol->nombre === 'CoordinadorDisciplina')
                        <!-- Tarjetas para Coordinador de Disciplina -->
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-primary mb-2">
                                        <i class="fas fa-user-graduate fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Total Estudiantes</h5>
                                    <h3 class="text-primary">{{ $totalEstudiantesCoord ?? 0 }}</h3>
                                    <small class="text-muted">Estudiantes registrados</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-danger mb-2">
                                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Total Faltas</h5>
                                    <h3 class="text-danger">{{ $totalFaltasCoord ?? 0 }}</h3>
                                    <small class="text-muted">Casos disciplinarios</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-warning mb-2">
                                        <i class="fas fa-layer-group fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Total Cursos</h5>
                                    <h3 class="text-warning">{{ $totalCursosCoord ?? 0 }}</h3>
                                    <small class="text-muted">Cursos activos</small>
                                </div>
                            </div>
                        </div>
                    @elseif($rol && $rol->nombre === 'CoordinadorAcademico')
                        <!-- Tarjetas para Coordinador Académico -->
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-primary mb-2">
                                        <i class="fas fa-user-graduate fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Total Estudiantes</h5>
                                    <h3 class="text-primary">{{ $totalEstudiantesAcad ?? 0 }}</h3>
                                    <small class="text-muted">Estudiantes registrados</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-success mb-2">
                                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Total Docentes</h5>
                                    <h3 class="text-success">{{ $totalDocentesAcad ?? 0 }}</h3>
                                    <small class="text-muted">Docentes activos</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-warning mb-2">
                                        <i class="fas fa-layer-group fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Total Cursos</h5>
                                    <h3 class="text-warning">{{ $totalCursosAcad ?? 0 }}</h3>
                                    <small class="text-muted">Cursos activos</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-info mb-2">
                                        <i class="fas fa-book fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Total Materias</h5>
                                    <h3 class="text-info">{{ $totalMateriasAcad ?? 0 }}</h3>
                                    <small class="text-muted">Materias registradas</small>
                                </div>
                            </div>
                        </div>
                    @elseif($rol && $rol->nombre === 'Tesoreria')
                        <!-- Tarjetas para Tesorería -->
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-primary mb-2">
                                        <i class="fas fa-user-graduate fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Total Estudiantes</h5>
                                    <h3 class="text-primary">{{ $totalEstudiantes ?? 0 }}</h3>
                                    <small class="text-muted">Estudiantes registrados</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-info mb-2">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Total Usuarios</h5>
                                    <h3 class="text-info">{{ $totalUsuarios ?? 0 }}</h3>
                                    <small class="text-muted">Usuarios del sistema</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-4 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-warning mb-2">
                                        <i class="fas fa-layer-group fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Total Cursos</h5>
                                    <h3 class="text-warning">{{ $totalCursos ?? 0 }}</h3>
                                    <small class="text-muted">Cursos activos</small>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Tarjetas para Administradores y otros roles -->
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-primary mb-2">
                                        <i class="fas fa-user-graduate fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Total Estudiantes</h5>
                                    <h3 class="text-primary">{{ $totalEstudiantes ?? 0 }}</h3>
                                    <small class="text-muted">Estudiantes registrados</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-success mb-2">
                                        <i class="fas fa-chalkboard-teacher fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Total Docentes</h5>
                                    <h3 class="text-success">{{ $totalDocentes ?? 0 }}</h3>
                                    <small class="text-muted">Docentes activos</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-warning mb-2">
                                        <i class="fas fa-layer-group fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Total Cursos</h5>
                                    <h3 class="text-warning">{{ $totalCursos ?? 0 }}</h3>
                                    <small class="text-muted">Cursos creados</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-3 mb-3">
                            <div class="card border-0 shadow-sm h-100">
                                <div class="card-body text-center">
                                    <div class="text-info mb-2">
                                        <i class="fas fa-users fa-2x"></i>
                                    </div>
                                    <h5 class="card-title">Total Usuarios</h5>
                                    <h3 class="text-info">{{ $totalUsuarios ?? 0 }}</h3>
                                    <small class="text-muted">Usuarios del sistema</small>
                                </div>
                            </div>
                        </div>
                    @endif
                </div>

                <!-- Quick Actions -->
                <div class="row mb-4">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-rocket me-2"></i>Acciones Rápidas
                                </h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    @if($rol && $rol->nombre === 'Estudiante')
                                        <!-- Acciones rápidas para Estudiantes -->
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('cursos.index') }}" class="btn btn-outline-primary btn-lg w-100">
                                                <i class="fas fa-graduation-cap me-2"></i>
                                                Ver Mi Curso
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('notas.index') }}" class="btn btn-outline-success btn-lg w-100">
                                                <i class="fas fa-book me-2"></i>
                                                Ver Mis Notas
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('reportes.index') }}" class="btn btn-outline-info btn-lg w-100">
                                                <i class="fas fa-chart-line me-2"></i>
                                                Ver Reportes
                                            </a>
                                        </div>
                                    @elseif($rol && $rol->nombre === 'Docente')
                                        <!-- Acciones rápidas para Docentes -->
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('notas.index') }}" class="btn btn-outline-success btn-lg w-100">
                                                <i class="fas fa-book me-2"></i>
                                                Ver Notas
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('reportes.index') }}" class="btn btn-outline-info btn-lg w-100">
                                                <i class="fas fa-chart-line me-2"></i>
                                                Ver Reportes
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('configuracion.index') }}" class="btn btn-outline-warning btn-lg w-100">
                                                <i class="fas fa-cog me-2"></i>
                                                Configuración
                                            </a>
                                        </div>
                                    @elseif($rol && $rol->nombre === 'Acudiente')
                                        <!-- Acciones rápidas para Acudientes -->
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('notas.index') }}" class="btn btn-outline-success btn-lg w-100">
                                                <i class="fas fa-book me-2"></i>
                                                Notas del Estudiante
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('reportes.index') }}" class="btn btn-outline-info btn-lg w-100">
                                                <i class="fas fa-chart-line me-2"></i>
                                                Ver Reportes
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('matriculas.index') }}" class="btn btn-outline-warning btn-lg w-100">
                                                <i class="fas fa-file-invoice me-2"></i>
                                                Ver Matrículas
                                            </a>
                                        </div>
                                    @elseif($rol && $rol->nombre === 'CoordinadorDisciplina')
                                        <!-- Acciones rápidas para Coordinador de Disciplina -->
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('disciplina.agregar') }}" class="btn btn-outline-danger btn-lg w-100">
                                                <i class="fas fa-gavel me-2"></i>
                                                Nueva Sanción
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('reportes.index') }}" class="btn btn-outline-info btn-lg w-100">
                                                <i class="fas fa-chart-line me-2"></i>
                                                Reportes
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('disciplina.index') }}" class="btn btn-outline-warning btn-lg w-100">
                                                <i class="fas fa-exclamation-triangle me-2"></i>
                                                Disciplina
                                            </a>
                                        </div>
                                    @elseif($rol && $rol->nombre === 'CoordinadorAcademico')
                                        <!-- Acciones rápidas para Coordinador Académico -->
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('cursos.index') }}" class="btn btn-outline-primary btn-lg w-100">
                                                <i class="fas fa-layer-group me-2"></i>
                                                Cursos
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('materias.index') }}" class="btn btn-outline-success btn-lg w-100">
                                                <i class="fas fa-book me-2"></i>
                                                Materias
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('notas.index') }}" class="btn btn-outline-info btn-lg w-100">
                                                <i class="fas fa-graduation-cap me-2"></i>
                                                Notas
                                            </a>
                                        </div>
                                    @elseif($rol && $rol->nombre === 'Tesoreria')
                                        <!-- Acciones rápidas para Tesorería -->
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('matriculas.index') }}" class="btn btn-outline-primary btn-lg w-100">
                                                <i class="fas fa-file-invoice-dollar me-2"></i>
                                                Matrículas
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('usuarios.index') }}" class="btn btn-outline-info btn-lg w-100">
                                                <i class="fas fa-users-cog me-2"></i>
                                                Gestión de Usuarios
                                            </a>
                                        </div>
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('configuracion') }}" class="btn btn-outline-secondary btn-lg w-100">
                                                <i class="fas fa-cogs me-2"></i>
                                                Configuración
                                            </a>
                                        </div>
                                    @else
                                        <!-- Acciones rápidas para Administradores y otros roles -->
                                        @if($rol && $rol->tienePermiso('gestionar_estudiantes'))
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('estudiantes.nuevo') }}" class="btn btn-outline-primary btn-lg w-100">
                                                <i class="fas fa-user-plus me-2"></i>
                                                Nuevo Estudiante
                                            </a>
                                        </div>
                                        @endif
                                        @if($rol && $rol->tienePermiso('gestionar_docentes'))
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('docentes.agregar') }}" class="btn btn-outline-primary btn-lg w-100">
                                                <i class="fas fa-chalkboard-teacher me-2"></i>
                                                Nuevo Docente
                                            </a>
                                        </div>
                                        @endif
                                        @if($rol && $rol->tienePermiso('ver_reportes_generales'))
                                        <div class="col-md-4 mb-3">
                                            <a href="{{ route('reportes.index') }}" class="btn btn-outline-primary btn-lg w-100">
                                                <i class="fas fa-chart-line me-2"></i>
                                                Ver Reportes
                                            </a>
                                        </div>
                                        @endif
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity -->
                <div class="row">
                    <div class="col-12">
                        <div class="card border-0 shadow-sm">
                            <div class="card-header bg-white">
                                <h5 class="card-title mb-0">
                                    <i class="fas fa-history me-2"></i>Actividad Reciente
                                </h5>
                            </div>
                            <div class="card-body">
                                @if(isset($recentActivities) && $recentActivities->count())
                                    <ul class="list-group list-group-flush">
                                        @foreach($recentActivities as $act)
                                            <li class="list-group-item d-flex align-items-start">
                                                <div class="me-3 text-primary">
                                                    <i class="fas fa-bolt"></i>
                                                </div>
                                                <div class="flex-grow-1">
                                                    <div class="fw-semibold">{{ $act->description }}</div>
                                                    <div class="small text-muted">
                                                        {{ $act->type }} · {{ optional($act->user)->name ?? 'Sistema' }} · {{ $act->created_at?->diffForHumans() }}
                                                    </div>
                                                </div>
                                            </li>
                                        @endforeach
                                    </ul>
                                @else
                                    <div class="text-center py-4">
                                        <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">No hay actividad reciente para mostrar.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
@endsection