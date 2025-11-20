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
                            <a class="dropdown-item" href="#">
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
                            <a class="nav-link active" href="{{ route('matriculas.index') }}">
                                <i class="fas fa-user-check me-2"></i>Matriculas
                            </a>
                        @endif
                        @if($rol->tienePermiso('gestionar_roles'))
                            <a class="nav-link" href="{{ route('roles.index') }}">
                                <i class="fas fa-user-shield me-2"></i>Roles y Permisos
                            </a>
                        @endif
                        @if($rol->tienePermiso('gestionar_usuarios'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-users-cog me-2"></i>Gestión de Usuarios
                            </a>
                        @endif
                        @if($rol->tienePermiso('configurar_sistema'))
                            <a class="nav-link" href="#">
                                <i class="fas fa-cog me-2"></i>Configuración
                            </a>
                        @endif
                    @endif
                </nav>
            </div>
        </div>

        <!-- Main Content -->
        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0">Matrícula #{{ $matricula->idMatricula }}</h4>
                <a href="{{ route('matriculas.index') }}" class="btn btn-sm btn-outline-secondary">Volver</a>
            </div>

            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif

            @php
                $persona = optional(optional($matricula->estudiante)->persona);
                $curso = optional(optional($matricula->estudiante)->curso);
                $nombre = trim(($persona->primerNombre ?? '') . ' ' . ($persona->segundoNombre ?? '') . ' ' . ($persona->primerApellido ?? '') . ' ' . ($persona->segundoApellido ?? ''));
                $badge = $matricula->estado === 'activo' ? 'success' : ($matricula->estado === 'en_proceso' ? 'warning' : 'secondary');
            @endphp

            <div class="row g-3">
                <div class="col-lg-7">
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="row">
                                <div class="col-12">
                                    <h5 class="mb-3">Información del Estudiante</h5>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2"><strong>Nombre:</strong> {{ $nombre ?: 'N/A' }}</div>
                                    <div class="mb-2"><strong>Documento:</strong> {{ $persona->noDocumento ?? 'N/A' }}</div>
                                    <div class="mb-2"><strong>Fecha Matrícula:</strong> {{ $matricula->fechaMatricula ?: '—' }}</div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-2"><strong>Curso:</strong> {{ $curso->grado ?? 'Sin curso' }}</div>
                                    <div class="mb-2"><strong>Estado:</strong> <span class="badge bg-{{ $badge }} text-uppercase">{{ str_replace('_',' ',$matricula->estado) }}</span></div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="mb-3">Proceso</h5>
                            <div class="d-flex flex-wrap gap-2">
                                @foreach(($proceso ?? []) as $step)
                                    <span class="badge {{ ($step['current'] ?? false) ? 'bg-primary' : 'bg-light text-dark' }}">{{ $step['label'] }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    @php
                        $usuario = Auth::user();
                        $rol = App\Models\RolesModel::find($usuario->roles_id);
                    @endphp
                    @if($rol && ($rol->tienePermiso('cambiar_estado_matricula') || $rol->nombre === 'Tesoreria' || $rol->tienePermiso('acceso_total')))
                        <div class="card mb-3">
                            <div class="card-body">
                                <h5 class="mb-3">Cambiar estado (Tesorería)</h5>
                                <form method="POST" action="{{ route('matriculas.estado', $matricula->idMatricula) }}">
                                    @csrf
                                    <div class="row g-2 align-items-end">
                                        <div class="col-sm-6 col-md-4">
                                            <label class="form-label">Estado</label>
                                            <select name="estado" class="form-select" required>
                                                <option value="en_proceso" @selected($matricula->estado==='en_proceso')>En proceso</option>
                                                <option value="activo" @selected($matricula->estado==='activo')>Activo</option>
                                                <option value="inactivo" @selected($matricula->estado==='inactivo')>Inactivo</option>
                                            </select>
                                        </div>
                                        <div class="col-auto">
                                            <button class="btn btn-primary">Actualizar</button>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    @endif
                </div>
                <div class="col-lg-5">
                    <div class="card mb-3">
                        <div class="card-body">
                            <h5 class="mb-3">Documentos</h5>
                            @if(!empty($documentos))
                                <ul class="list-group list-group-flush mb-3">
                                    @foreach($documentos as $doc)
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <span class="text-truncate" style="max-width:70%">{{ $doc['name'] }}</span>
                                            <a href="{{ $doc['url'] }}" target="_blank" class="btn btn-sm btn-outline-secondary">Ver</a>
                                        </li>
                                    @endforeach
                                </ul>
                            @else
                                <div class="text-muted mb-3">No hay documentos cargados.</div>
                            @endif

                            @if($rol && ($rol->tienePermiso('subir_documentos_matricula') || $rol->nombre === 'Acudiente' || $rol->tienePermiso('acceso_total')))
                                <form method="POST" action="{{ route('matriculas.documentos', $matricula->idMatricula) }}" enctype="multipart/form-data">
                                    @csrf
                                    <div class="mb-2">
                                        <label class="form-label">Subir documentos</label>
                                        <input type="file" name="documentos[]" class="form-control" multiple required>
                                        <div class="form-text">Max 5MB por archivo.</div>
                                    </div>
                                    <button class="btn btn-primary">Cargar</button>
                                </form>
                            @endif
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