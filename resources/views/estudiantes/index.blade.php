@extends('layouts.app')

@section('content')
@php
    $usuario = Auth::user();
    $rol = App\Models\RolesModel::find($usuario->roles_id);
@endphp

@section('title', 'Estudiante - Colegio')

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
                    <a class="nav-link" href="{{ route('dashboard') }}">
                        <i class="fas fa-tachometer-alt me-2"></i>Dashboard
                    </a>
                    @if($rol)
                        @if($rol->tienePermiso('gestionar_estudiantes'))
                            <a class="nav-link active" href="{{ route('estudiantes.index') }}">
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
                <h1 class="mb-4">Estudiantes</h1>
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif
                <a href="{{ route('estudiantes.nuevo') }}" class="btn btn-primary mb-3">Nuevo estudiante</a>
                <div class="mb-3">
                    <form method="GET" class="d-flex align-items-center">
                        <label class="me-2">Filtrar por grado:</label>
                        <select name="grado" class="form-select me-2" style="max-width:300px;">
                            <option value="">-- Todos los grados --</option>
                            @foreach($grados as $g)
                                <option value="{{ $g }}" {{ (isset($grado) && $grado == $g) ? 'selected' : '' }}>{{ $g }}</option>
                            @endforeach
                        </select>
                        <button class="btn btn-outline-primary">Filtrar</button>
                    </form>
                </div>
                @if($estudiantes->isEmpty())
                    <p>No hay estudiantes registrados.</p>
                @else
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Documento</th>
                                <th>Fecha de ingreso</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($estudiantes as $estudiante)
                                <tr>
                                    <td>{{ $estudiante->idEstudiante }}</td>
                                    <td>
                                        @if($estudiante->persona)
                                            {{ $estudiante->persona->primerNombre }} {{ $estudiante->persona->segundoNombre }} {{ $estudiante->persona->primerApellido }} {{ $estudiante->persona->segundoApellido }}
                                        @else
                                            (Persona no asociada)
                                        @endif
                                    </td>
                                    <td>{{ optional($estudiante->persona)->noDocumento }}</td>
                                    <td>{{ $estudiante->fechaIngreso ?? '-' }}</td>
                                    <td>
                                        <a href="{{ route('estudiantes.mostrar', $estudiante->idEstudiante) }}" class="btn btn-info btn-sm" title="Ver" aria-label="Ver">
                                            <i class="fas fa-magnifying-glass"></i>
                                        </a>
                                        <a href="{{ route('estudiantes.editar', $estudiante->idEstudiante) }}" class="btn btn-warning btn-sm" title="Editar" aria-label="Editar">
                                            <i class="fas fa-pen"></i>
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
