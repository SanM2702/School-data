@extends('layouts.app')

@section('title', 'Notas - Colegio')

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
                    <a class="nav-link" href="{{ route('dashboard') }}">
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
                            <a class="nav-link active" href="{{ route('notas.index') }}">
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
                <h3 class="mb-3">Notas</h3>
                <div class="card">
                    <div class="card-body">
                        <div class="d-flex align-items-center justify-content-between mb-3">
                            <h5 class="mb-0">Curso: {{ $curso->grado }}{{ $curso->grupo ? ' - '.$curso->grupo : '' }}{{ $curso->nombre ? ' - '.$curso->nombre : '' }}</h5>
                            <div>
                                <a href="{{ route('notas.editar', $curso) }}" class="btn btn-primary btn-sm">Editar notas</a>
                            </div>
                        </div>

                        @if(($materias ?? null) && ($estudiantes ?? null) && count($materias) && count($estudiantes))
                            <div class="table-responsive">
                                <table class="table table-bordered table-striped align-middle">
                                    <thead>
                                        <tr>
                                            <th>Estudiante</th>
                                            @foreach($materias as $materia)
                                                <th class="text-center">{{ $materia->nombre }}</th>
                                            @endforeach
                                            <th class="text-center">Promedio Est.</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($estudiantes as $est)
                                            <tr>
                                                <td>
                                                    {{ $est->persona->primerNombre ?? '' }} {{ $est->persona->segundoNombre ?? '' }} {{ $est->persona->primerApellido ?? '' }} {{ $est->persona->segundoApellido ?? '' }}
                                                </td>
                                                @foreach($materias as $materia)
                                                    <td class="text-center">
                                                        {{ $notaMap[$est->idEstudiante][$materia->idMateria] ?? '-' }}
                                                    </td>
                                                @endforeach
                                                <td class="text-center fw-semibold">{{ $promedioEstudiante[$est->idEstudiante] !== null ? number_format($promedioEstudiante[$est->idEstudiante], 2) : '-' }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr>
                                            <th>Promedio Materia</th>
                                            @foreach($materias as $materia)
                                                <th class="text-center">{{ $promedioMateria[$materia->idMateria] !== null ? number_format($promedioMateria[$materia->idMateria], 2) : '-' }}</th>
                                            @endforeach
                                            <th></th>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        @else
                            <p class="text-muted mb-0">No hay estudiantes o materias asignadas a este curso.</p>
                        @endif
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