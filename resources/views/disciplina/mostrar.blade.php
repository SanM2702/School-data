
@extends('layouts.app')

@section('content')
@php
    $usuario = Auth::user();
    $rol = App\Models\RolesModel::find($usuario->roles_id);
@endphp

@section('title', 'Mostrar Disciplina - Colegio')

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
                            <a class="nav-link active" href="{{ route('disciplina.index') }}">
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
        <div class="col-md-9 col-lg-10 p-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0"><i class="fas fa-eye me-2"></i>Detalle de caso disciplinario</h4>
                <a href="{{ route('disciplina.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Volver
                </a>
            </div>

            <div class="card">
                <div class="card-body">
                    @php
                        $p = optional(optional($disciplina->estudiante)->persona);
                        $curso = optional(optional($disciplina->estudiante)->curso);
                        $nombre = trim(($p->primerNombre.' '.($p->segundoNombre ?? '').' '.$p->primerApellido.' '.($p->segundoApellido ?? '')));
                        $tipo = strtolower($disciplina->tipo_falta);
                        $map = ['leve'=>'Leve','moderado'=>'Moderado','grave'=>'Grave','muy_grave'=>'Muy grave'];
                        $badge = ['leve'=>'bg-success','moderado'=>'bg-info','grave'=>'bg-warning text-dark','muy_grave'=>'bg-danger'];
                    @endphp

                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="text-muted small">No Documento estudiante</label>
                            <div class="fw-semibold">{{ $p->noDocumento ?? '—' }}</div>
                        </div>
                        <div class="col-md-8">
                            <label class="text-muted small">Nombre del estudiante</label>
                            <div class="fw-semibold">{{ $nombre ?: '—' }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="text-muted small">Curso</label>
                            <div class="fw-semibold">{{ $curso->nombre ?? '—' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Tipo de falta</label>
                            <div>
                                <span class="badge {{ $badge[$tipo] ?? 'bg-secondary' }}">{{ $map[$tipo] ?? $disciplina->tipo_falta }}</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Fecha de falta</label>
                            <div class="fw-semibold">{{ \Carbon\Carbon::parse($disciplina->fecha)->format('Y-m-d') }}</div>
                        </div>

                        <div class="col-12">
                            <label class="text-muted small">Descripción</label>
                            <div class="border rounded p-3">{{ $disciplina->descripcion }}</div>
                        </div>

                        <div class="col-md-4">
                            <label class="text-muted small">Notificación al estudiante</label>
                            <div>
                                @if($disciplina->notificado_estudiante)
                                    <span class="text-success"><i class="fas fa-check me-1"></i>Notificado</span>
                                @else
                                    <span class="text-danger"><i class="fas fa-times me-1"></i>No notificado</span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-8">
                            <label class="text-muted small">Reportado por</label>
                            <div class="fw-semibold">{{ $disciplina->presentador_nombre ?? '—' }} <span class="text-muted">({{ $disciplina->presentador_cargo ?? '—' }})</span></div>
                        </div>

                        <div class="col-md-4">
                            <label class="text-muted small">Estado del caso</label>
                            <div class="fw-semibold">{{ $disciplina->estado }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Tipo de sanción</label>
                            <div class="fw-semibold">{{ $disciplina->tipo_sancion ?? '—' }}</div>
                        </div>
                        <div class="col-md-4">
                            <label class="text-muted small">Confirmación del acudiente</label>
                            <div>
                                @if($disciplina->confirmacion_acudiente)
                                    <span class="text-success"><i class="fas fa-check me-1"></i>Confirmado</span>
                                @else
                                    <span class="text-danger"><i class="fas fa-times me-1"></i>No confirmado</span>
                                @endif
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="text-muted small">Respuesta del acudiente</label>
                            <div class="border rounded p-3">{{ $disciplina->respuesta_acudiente ?? '—' }}</div>
                        </div>
                        <div class="col-12">
                            <label class="text-muted small">Observaciones</label>
                            <div class="border rounded p-3">{{ $disciplina->observaciones ?? '—' }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
