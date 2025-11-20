@extends('layouts.app')

@section('content')
@php
    $usuario = Auth::user();
    $rol = App\Models\RolesModel::find($usuario->roles_id);
@endphp

@section('title', 'Editar Estudiante - Colegio')

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
                <h1 class="mb-4">
                    {{ $estudiante->persona->primerNombre }} {{ $estudiante->persona->segundoNombre }} {{ $estudiante->persona->primerApellido }} {{ $estudiante->persona->segundoApellido }}
                </h1>

                <div class="row g-4 align-items-stretch">
                    <div class="col-md-4 d-flex flex-column">
                        <div class="border rounded bg-white w-100 flex-grow-1 d-flex justify-content-center align-items-center">
                            <div class="d-flex justify-content-center align-items-center" style="height: 100%; aspect-ratio: 3 / 4; overflow: hidden;">
                                @if($estudiante->foto)
                                    <img src="{{ route('estudiantes.foto', $estudiante->idEstudiante) }}" alt="Foto estudiante" style="max-width:100%; height:auto; display:block;">
                                @else
                                    <span class="text-muted">Sin imagen</span>
                                @endif
                            </div>
                        </div>
                        <div class="mt-3">
                            <form action="{{ route('estudiantes.updateFoto', $estudiante->idEstudiante) }}" method="POST" enctype="multipart/form-data">
                                @csrf
                                <div class="input-group">
                                    <input type="file" name="foto" accept="image/*" class="form-control" required>
                                    <button class="btn btn-outline-primary" type="submit"><i class="fas fa-upload me-1"></i> Subir</button>
                                </div>
                                @error('foto')
                                    <div class="text-danger small mt-1">{{ $message }}</div>
                                @enderror
                            </form>
                        </div>
                    </div>
                    <div class="col-md-8 d-flex">
                        <div class="card w-100 h-100">
                            <div class="card-body">
                                <h5 class="card-title mb-3">Información del Estudiante</h5>
                                <form method="POST" action="{{ url('estudiantes/'.$estudiante->idEstudiante.'/contacto') }}">
                                    @csrf
                                    <div class="row gy-3">
                                        <div class="col-sm-6">
                                            <label class="form-label">Documento</label>
                                            <input type="text" class="form-control" value="{{ $estudiante->persona->noDocumento }}" disabled>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label">Fecha de Nacimiento</label>
                                            <input type="text" class="form-control" value="{{ $estudiante->persona->fechaNacimiento }}" disabled>
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label">Teléfono</label>
                                            <input type="text" name="telefono" class="form-control" value="{{ $estudiante->persona->telefono }}">
                                        </div>
                                        <div class="col-sm-6">
                                            <label class="form-label">Email</label>
                                            <input type="email" name="email" class="form-control" value="{{ $estudiante->persona->email }}">
                                            <div class="form-text">Nota: No cambiara su correo de inicio de sesion</div>
                                        </div>
                                        <div class="col-12">
                                            <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>

                <h1 class="my-4">Acudiente</h1>
                <div class="card">
                    <div class="card-body">
                        @if(!empty($acudiente))
                            <h5 class="card-title mb-3">
                                {{ $acudiente->primerNombre }} {{ $acudiente->segundoNombre }} {{ $acudiente->primerApellido }} {{ $acudiente->segundoApellido }}
                            </h5>
                            <form method="POST" action="{{ url('estudiantes/'.$estudiante->idEstudiante.'/acudiente/contacto') }}">
                                @csrf
                                <div class="row gy-3">
                                    <div class="col-sm-6">
                                        <label class="form-label">Parentesco</label>
                                        <input type="text" class="form-control" value="{{ $acudiente->parentesco }}" disabled>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Documento</label>
                                        <input type="text" class="form-control" value="{{ $acudiente->noDocumento }}" disabled>
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Teléfono</label>
                                        <input type="text" name="telefono" class="form-control" value="{{ $acudiente->telefono }}">
                                    </div>
                                    <div class="col-sm-6">
                                        <label class="form-label">Email</label>
                                        <input type="email" name="email" class="form-control" value="{{ $acudiente->email }}">
                                        <div class="form-text">Nota: No cambiara su correo de inicio de sesion</div>
                                    </div>
                                    <div class="col-12">
                                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                                    </div>
                                </div>
                            </form>
                        @else
                            <div class="text-muted">No hay información del acudiente disponible.</div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection