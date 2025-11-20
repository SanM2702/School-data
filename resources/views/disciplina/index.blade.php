
@extends('layouts.app')

@section('title', 'Disciplina - Colegio')

@section('content')
@php
    $usuario = Auth::user();
    $rol = App\Models\RolesModel::find($usuario->roles_id);
@endphp
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
                <h4 class="mb-0"><i class="fas fa-gavel me-2"></i>Registro disciplinario</h4>
                @if($rol && $rol->tienePermiso('gestionar_disciplina'))
                    <a href="{{ route('disciplina.agregar') }}" class="btn btn-success">
                        <i class="fas fa-plus me-1"></i>Nueva sanción
                    </a>
                @endif
            </div>

            <div class="card mb-3">
                <div class="card-body">
                    <form method="GET" action="{{ route('disciplina.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label">Estudiante</label>
                            <select name="estudiante" class="form-select">
                                <option value="">Todos</option>
                                @if(isset($estudiantes))
                                    @foreach($estudiantes as $e)
                                        <option value="{{ $e['id'] }}" {{ request('estudiante') == $e['id'] ? 'selected' : '' }}>
                                            {{ $e['nombre'] }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Curso</label>
                            <select name="curso" class="form-select">
                                <option value="">Todos</option>
                                @if(isset($cursos))
                                    @foreach($cursos as $c)
                                        <option value="{{ $c }}" {{ request('curso') == $c ? 'selected' : '' }}>
                                            {{ $c }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Grupo</label>
                            <select name="grupo" class="form-select">
                                <option value="">Todos</option>
                                @if(isset($grupos))
                                    @foreach($grupos as $g)
                                        <option value="{{ $g }}" {{ request('grupo') == $g ? 'selected' : '' }}>
                                            {{ $g }}
                                        </option>
                                    @endforeach
                                @endif
                            </select>
                        </div>
                        <div class="col-12 d-flex gap-2">
                            <button class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filtrar</button>
                            <a href="{{ route('disciplina.index') }}" class="btn btn-secondary"><i class="fas fa-undo me-1"></i>Limpiar</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0 align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th>Estudiante</th>
                                    <th>Tipo de falta</th>
                                    <th>Descripción</th>
                                    <th>Fecha</th>
                                    <th>Acudiente notificado</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($disciplinas ?? [] as $d)
                                    @php
                                        $count = $conteoPorEstudiante[$d->estudiante_id] ?? 0;
                                        $rowClass = '';
                                        if ($count > 5) { $rowClass = 'table-danger'; }
                                        elseif ($count > 3) { $rowClass = 'table-warning'; }
                                        $persona = $d->estudiante->persona ?? null;
                                        $nombre = $persona ? trim(($persona->primerNombre.' '.($persona->segundoNombre ?? '').' '.$persona->primerApellido.' '.($persona->segundoApellido ?? ''))) : '—';
                                    @endphp
                                    <tr class="{{ $rowClass }}">
                                        <td>
                                            <div class="fw-semibold">{{ $nombre }}</div>
                                            <div class="text-muted small">Curso: {{ optional($d->estudiante->curso)->nombre ?? '—' }}</div>
                                        </td>
                                        <td>
                                            @php
                                                $map = ['leve'=>'Leve','moderado'=>'Moderado','grave'=>'Grave','muy_grave'=>'Muy grave'];
                                                $badge = ['leve'=>'bg-success','moderado'=>'bg-info','grave'=>'bg-warning text-dark','muy_grave'=>'bg-danger'];
                                                $tipo = strtolower($d->tipo_falta);
                                            @endphp
                                            <span class="badge {{ $badge[$tipo] ?? 'bg-secondary' }}">{{ $map[$tipo] ?? $d->tipo_falta }}</span>
                                        </td>
                                        <td class="text-truncate" style="max-width: 360px;" title="{{ $d->descripcion }}">{{ $d->descripcion }}</td>
                                        <td>{{ \Carbon\Carbon::parse($d->fecha)->format('Y-m-d') }}</td>
                                        <td>
                                            @if($d->notificado_acudiente)
                                                <i class="fas fa-check text-success"></i>
                                            @else
                                                <i class="fas fa-times text-danger"></i>
                                            @endif
                                        </td>
                                        <td class="text-center">
                                            <a href="{{ route('disciplina.mostrar', $d->id) }}" class="btn btn-sm btn-outline-primary" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('disciplina.editar', $d->id) }}" class="btn btn-sm btn-outline-secondary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center text-muted p-4">No hay registros disciplinarios.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
                @if(isset($disciplinas))
                    <div class="card-footer">{{ $disciplinas->links() }}</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
