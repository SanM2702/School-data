@extends('layouts.app')

@section('title', 'Agregar Materias - Colegio')

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
                            <a class="nav-link active" href="{{ route('materias.index') }}">
                                <i class="fas fa-book-open me-2"></i>Materias
                            </a>
                        @endif
                        @if($rol->tienePermiso('gestionar_disciplina'))
                            <a class="nav-link" href="{{ route('disciplina.index') }}">
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
        <div class="col-md-9 col-lg-10">
            <div class="p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h4 class="mb-0">Agregar materia</h4>
                    <a href="{{ route('materias.index') }}" class="btn btn-outline-secondary btn-sm">
                        <i class="fas fa-arrow-left me-1"></i>Volver
                    </a>
                </div>

                <div class="card shadow-sm">
                    <div class="card-body">
                        <form action="{{ route('materias.store') }}" method="POST">
                            @csrf

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label">Nombre de la materia</label>
                                    <input type="text" name="nombre" class="form-control" value="{{ old('nombre', 'Matematicas') }}" required>
                                    @error('nombre')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">Curso (Grado)</label>
                                    <select id="grado" class="form-select">
                                        <option value="">Seleccione</option>
                                        @php $grados = collect($cursos ?? [])->pluck('grado')->unique()->filter(); @endphp
                                        @foreach($grados as $g)
                                            <option value="{{ $g }}">{{ $g }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Grupo</label>
                                    <select id="grupo" class="form-select">
                                        <option value="">Seleccione</option>
                                        @php $grupos = collect($cursos ?? [])->pluck('grupo')->unique()->filter(); @endphp
                                        @foreach($grupos as $gr)
                                            <option value="{{ $gr }}">{{ $gr }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                <input type="hidden" name="curso_id" id="curso_id" value="{{ old('curso_id') }}">
                                @error('curso_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                <div class="col-12">
                                    <div id="cursoHelp" class="form-text">Seleccione grado y grupo para asignar el curso.</div>
                                    <div id="cursoError" class="text-danger small d-none">No se encontró un curso con el grado y grupo seleccionados.</div>
                                </div>

                                <div class="col-md-4">
                                    <label class="form-label">Área</label>
                                    <select id="area" class="form-select">
                                        <option value="">Todas</option>
                                        @foreach(($areas ?? []) as $area)
                                            <option value="{{ $area }}">{{ $area }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-8">
                                    <label class="form-label">Profesor</label>
                                    <select name="docente_id" id="docente_id" class="form-select">
                                        <option value="">Sin asignar</option>
                                        @foreach(($docentes ?? []) as $d)
                                            @php
                                                $p = $d->persona;
                                                $segundo = $p && $p->segundoNombre ? ($p->segundoNombre.' ') : '';
                                                $nombreDoc = $p ? trim($p->primerNombre.' '.$segundo.$p->primerApellido) : ('Docente #'.$d->idDocente);
                                                $areaDoc = $d->area ?? '';
                                            @endphp
                                            <option value="{{ $d->idDocente }}" data-area="{{ $areaDoc }}">
                                                {{ $nombreDoc }}@if(!empty($areaDoc)) - ({{ $areaDoc }}) @endif
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('docente_id')<div class="text-danger small">{{ $message }}</div>@enderror
                                </div>

                            </div>

                            <div class="mt-3 d-flex gap-2">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-1"></i>Crear materia
                                </button>
                                <a href="{{ route('materias.index') }}" class="btn btn-secondary">Cancelar</a>
                            </div>
                        </form>
                    </div>
                </div>

                @if ($errors->any())
                    <div class="alert alert-danger mt-3">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    (function(){
        @php
            $cursosJson = collect($cursos ?? [])->map(function ($c) {
                return [
                    'id'    => $c->idCurso,
                    'grado' => $c->grado ?? $c->nombre,
                    'grupo' => $c->grupo,
                ];
            })->values();
        @endphp
        const cursos = @json($cursosJson);

        const gradoSel  = document.getElementById('grado');
        const grupoSel  = document.getElementById('grupo');
        const cursoIdEl = document.getElementById('curso_id');
        const cursoErr  = document.getElementById('cursoError');

        function updateCursoId(){
            const g = gradoSel.value;
            const gr = grupoSel.value;
            cursoErr.classList.add('d-none');
            cursoIdEl.value = '';
            if(!g || !gr) return;
            const match = cursos.find(c => c.grado === g && c.grupo === gr);
            if(match){
                cursoIdEl.value = match.id;
            } else {
                cursoErr.classList.remove('d-none');
            }
        }

        gradoSel?.addEventListener('change', updateCursoId);
        grupoSel?.addEventListener('change', updateCursoId);

        const areaSel = document.getElementById('area');
        const docenteSel = document.getElementById('docente_id');
        function filterDocentes(){
            const area = areaSel.value;
            [...docenteSel.options].forEach(opt => {
                if(!opt.value) return; // keep "Sin asignar"
                const oa = opt.getAttribute('data-area') || '';
                const show = !area || oa === area;
                opt.hidden = !show;
            });
            // Si el seleccionado queda oculto, limpiar selección
            if (docenteSel.selectedOptions[0] && docenteSel.selectedOptions[0].hidden) {
                docenteSel.value = '';
            }
        }
        areaSel?.addEventListener('change', filterDocentes);
        filterDocentes();
    })();
</script>
@endpush

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
@endsection