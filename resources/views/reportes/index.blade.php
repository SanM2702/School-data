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
                            <a class="nav-link active" href="{{ route('reportes.index') }}">
                                <i class="fas fa-chart-bar me-2"></i>Reportes
                            </a>
                        @endif
                        @if($rol->tienePermiso('matricular_estudiantes'))
                            <a class="nav-link" href="#">
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
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h4 class="mb-0">Reportes del Estudiante</h4>
            </div>

            <form method="GET" action="{{ route('reportes.index') }}" class="row g-3 mb-4">
                <div class="col-md-8">
                    <label for="estudiante" class="form-label">Seleccionar estudiante</label>
                    <select name="estudiante" id="estudiante" class="form-select" required>
                        <option value="">-- Selecciona --</option>
                        @foreach(($listaEstudiantes ?? []) as $opt)
                            <option value="{{ $opt['id'] }}" {{ (isset($estudiante) && $estudiante && $estudiante->idEstudiante == $opt['id']) ? 'selected' : '' }}>
                                {{ $opt['texto'] }} @if($opt['curso']) ({{ $opt['curso'] }}) @endif
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Ver reporte
                    </button>
                </div>
            </form>

            @if(isset($estudiante) && $estudiante)
                <div class="card mb-4">
                    <div class="card-body">
                        <div class="d-flex flex-wrap align-items-center">
                            <div class="me-3">
                                <div class="fw-semibold">Estudiante</div>
                                <div>
                                    @php $p = $estudiante->persona; @endphp
                                    {{ trim(($p->primerNombre ?? '').' '.($p->segundoNombre ?? '').' '.($p->primerApellido ?? '').' '.($p->segundoApellido ?? '')) }}
                                </div>
                            </div>
                            <div class="me-3">
                                <div class="fw-semibold">Curso</div>
                                <div>
                                    {{ $estudiante->curso ? ($estudiante->curso->nombre.' '.$estudiante->curso->grupo) : 'Sin curso' }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-3 mb-4">
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="d-flex align-items-center">
                                    <div class="flex-grow-1">
                                        <div class="text-muted">Total faltas</div>
                                        <div class="display-6">{{ $resumenFaltas['total'] ?? 0 }}</div>
                                    </div>
                                    <i class="fas fa-exclamation-triangle text-danger fs-1"></i>
                                </div>
                                <div class="small text-muted mt-2">Última: {{ isset($resumenFaltas['ultima_falta']) && $resumenFaltas['ultima_falta'] ? $resumenFaltas['ultima_falta']->format('Y-m-d') : 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-muted">Faltas por tipo</div>
                                @php $porTipo = $resumenFaltas['por_tipo'] ?? []; @endphp
                                @if($porTipo)
                                    @foreach($porTipo as $tipo => $cant)
                                        <div class="d-flex justify-content-between"><span class="text-capitalize">{{ str_replace('_',' ', $tipo) }}</span><span class="fw-semibold">{{ $cant }}</span></div>
                                    @endforeach
                                @else
                                    <div class="text-muted">Sin registros</div>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card h-100">
                            <div class="card-body">
                                <div class="text-muted">Faltas por estado</div>
                                @php $porEstado = $resumenFaltas['por_estado'] ?? []; @endphp
                                @if($porEstado)
                                    @foreach($porEstado as $estado => $cant)
                                        <div class="d-flex justify-content-between"><span>{{ $estado }}</span><span class="fw-semibold">{{ $cant }}</span></div>
                                    @endforeach
                                @else
                                    <div class="text-muted">Sin registros</div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row g-4">
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-gavel me-2"></i>Disciplina</span>
                                <span class="badge bg-secondary">{{ ($disciplinas ?? collect())->count() }}</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Fecha</th>
                                                <th>Tipo</th>
                                                <th>Estado</th>
                                                <th>Descripción</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @forelse(($disciplinas ?? collect()) as $d)
                                            <tr>
                                                <td>{{ optional($d->fecha)->format('Y-m-d') }}</td>
                                                <td class="text-capitalize">{{ str_replace('_',' ', $d->tipo_falta) }}</td>
                                                <td>{{ $d->estado }}</td>
                                                <td class="text-truncate" style="max-width: 260px">{{ $d->descripcion }}</td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="4" class="text-center text-muted">Sin registros</td></tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="card h-100">
                            <div class="card-header d-flex justify-content-between align-items-center">
                                <span><i class="fas fa-book me-2"></i>Notas</span>
                                <span class="badge bg-info text-dark">Promedio: {{ $resumenNotas['promedio'] !== null ? $resumenNotas['promedio'] : 'N/A' }}</span>
                            </div>
                            <div class="card-body p-0">
                                <div class="table-responsive">
                                    <table class="table table-sm align-middle mb-0">
                                        <thead class="table-light">
                                            <tr>
                                                <th>Materia</th>
                                                <th>Nota</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                        @forelse(($materias ?? collect()) as $m)
                                            @php $notaVal = $resumenNotas['por_materia'][$m->idMateria] ?? null; @endphp
                                            <tr>
                                                <td>{{ $m->nombre }}</td>
                                                <td>
                                                    @if($notaVal !== null)
                                                        <span class="fw-semibold {{ $notaVal < 3 ? 'text-danger' : 'text-success' }}">{{ number_format($notaVal, 2) }}</span>
                                                    @else
                                                        <span class="text-muted">Sin nota</span>
                                                    @endif
                                                </td>
                                            </tr>
                                        @empty
                                            <tr><td colspan="2" class="text-center text-muted">Sin materias/nota</td></tr>
                                        @endforelse
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <div class="alert alert-info">
                    Selecciona un estudiante para ver su reporte integral.
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
@endsection