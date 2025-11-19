@extends('layouts.app')

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
                        @if($rol->tienePermiso('ver_reportes_generales'))
                            <a class="nav-link" href="#">
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
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h4 class="mb-0"><i class="fas fa-plus me-2"></i>Nueva sanción disciplinaria</h4>
                <a href="{{ route('disciplina.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Volver
                </a>
            </div>

            <form method="POST" action="{{ route('disciplina.store') }}" class="card">
                @csrf
                <div class="card-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Estudiante</label>
                            <select name="estudiante_id" class="form-select" required>
                                <option value="">Seleccione...</option>
                                @foreach(($estudiantes ?? []) as $e)
                                    @php 
                                        $p = $e->persona; 
                                        $label = trim(($p->primerNombre.' '.($p->segundoNombre ?? '').' '.$p->primerApellido.' '.($p->segundoApellido ?? '')));
                                        $curso = $e->curso; 
                                    @endphp
                                    <option value="{{ $e->idEstudiante }}" {{ old('estudiante_id') == $e->idEstudiante ? 'selected' : '' }}>
                                        {{ $label }} {{ $curso ? ' - '.$curso->nombre : '' }}
                                    </option>
                                @endforeach
                            </select>
                            @error('estudiante_id')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Fecha</label>
                            <input type="date" name="fecha" class="form-control" value="{{ old('fecha', now()->format('Y-m-d')) }}" required>
                            @error('fecha')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Tipo de falta</label>
                            <select name="tipo_falta" class="form-select" required>
                                @foreach(($tiposFalta ?? ['leve','moderado','grave','muy_grave']) as $t)
                                    <option value="{{ $t }}" {{ old('tipo_falta') == $t ? 'selected' : '' }}>{{ ucfirst(str_replace('_',' ', $t)) }}</option>
                                @endforeach
                            </select>
                            @error('tipo_falta')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-12">
                            <label class="form-label">Descripción</label>
                            <textarea name="descripcion" rows="3" class="form-control" required>{{ old('descripcion') }}</textarea>
                            @error('descripcion')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label">Reportado por (Nombre)</label>
                            <input type="text" name="presentador_nombre" class="form-control" value="{{ old('presentador_nombre', Auth::user()->name) }}" required>
                            @error('presentador_nombre')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cargo de quien reporta</label>
                            <select name="presentador_cargo" class="form-select" required>
                                @foreach(($cargos ?? ['Docente','Coordinador de convivencia','CoordinadorDisciplina','Rector']) as $c)
                                    <option value="{{ $c }}" {{ old('presentador_cargo') == $c ? 'selected' : '' }}>{{ $c }}</option>
                                @endforeach
                            </select>
                            @error('presentador_cargo')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Estado del caso</label>
                            <select name="estado" class="form-select" required>
                                @foreach(($estados ?? ['Registrado','Revisado','Sancionado','Cerrado','En apelación']) as $e)
                                    <option value="{{ $e }}" {{ old('estado') == $e ? 'selected' : '' }}>{{ $e }}</option>
                                @endforeach
                            </select>
                            @error('estado')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Tipo de sanción (opcional)</label>
                            <input type="text" name="tipo_sancion" class="form-control" value="{{ old('tipo_sancion') }}">
                            @error('tipo_sancion')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-4 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="notiEst" name="notificado_estudiante" {{ old('notificado_estudiante') ? 'checked' : '' }}>
                                <label class="form-check-label" for="notiEst">
                                    Notificado al estudiante
                                </label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label">Observaciones (opcional)</label>
                            <textarea name="observaciones" rows="3" class="form-control">{{ old('observaciones') }}</textarea>
                            @error('observaciones')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-end gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Guardar sanción
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
