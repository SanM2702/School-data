@extends('layouts.app')

@section('content')
@php
    $usuario = Auth::user();
    $rol = App\Models\RolesModel::find($usuario->roles_id);
@endphp

@section('title', 'Nuevo Estudiante - Colegio')

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
                @if(session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <h1 class="mb-4">Nuevo Estudiante</h1>

                <ul class="nav nav-tabs" id="nuevoEstudianteTabs" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="tab-estudiante" data-bs-toggle="tab" data-bs-target="#pane-estudiante" type="button" role="tab">Estudiante</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-acudiente" data-bs-toggle="tab" data-bs-target="#pane-acudiente" type="button" role="tab">Acudiente</button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="tab-matricula" data-bs-toggle="tab" data-bs-target="#pane-matricula" type="button" role="tab">Matrícula</button>
                    </li>
                </ul>

                <div class="tab-content border border-top-0 p-3" id="nuevoEstudianteTabsContent">
                    <div class="tab-pane fade show active" id="pane-estudiante" role="tabpanel" aria-labelledby="tab-estudiante">
                        <form action="{{ route('estudiantes.store') }}" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Primer Nombre</label>
                                    <input type="text" name="primerNombre" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Segundo Nombre</label>
                                    <input type="text" name="segundoNombre" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Primer Apellido</label>
                                    <input type="text" name="primerApellido" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Segundo Apellido</label>
                                    <input type="text" name="segundoApellido" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="telefono" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Documento</label>
                                    <input type="text" name="noDocumento" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha Nacimiento</label>
                                    <input type="date" name="fechaNacimiento" class="form-control">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha Ingreso</label>
                                    <input type="date" name="fechaIngreso" class="form-control">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Guardar Estudiante</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="pane-acudiente" role="tabpanel" aria-labelledby="tab-acudiente">
                        <form id="formAcudiente" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label">Documento del Estudiante</label>
                                    <div class="input-group">
                                        <input type="text" id="acudiente_noDocumento" name="noDocumentoEstudiante" class="form-control" placeholder="Ingrese documento" required>
                                        <span class="input-group-text" id="acudiente_nombre_estudiante">—</span>
                                    </div>
                                    <div class="form-text">Se muestra el nombre del estudiante si existe.</div>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Parentesco</label>
                                    <input type="text" name="parentesco" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Primer Nombre</label>
                                    <input type="text" name="primerNombre" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Segundo Nombre</label>
                                    <input type="text" name="segundoNombre" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Primer Apellido</label>
                                    <input type="text" name="primerApellido" class="form-control" required>
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Segundo Apellido</label>
                                    <input type="text" name="segundoApellido" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" name="email" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Teléfono</label>
                                    <input type="text" name="telefono" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Documento</label>
                                    <input type="text" name="noDocumento" class="form-control">
                                </div>
                                <div class="col-md-3">
                                    <label class="form-label">Fecha Nacimiento</label>
                                    <input type="date" name="fechaNacimiento" class="form-control">
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Guardar Acudiente</button>
                                </div>
                            </div>
                        </form>
                    </div>

                    <div class="tab-pane fade" id="pane-matricula" role="tabpanel" aria-labelledby="tab-matricula">
                        <form id="formMatricula" method="POST">
                            @csrf
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label class="form-label">Documento del Estudiante</label>
                                    <input type="text" id="matricula_noDocumento" name="noDocumentoEstudiante" class="form-control" required>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Estado</label>
                                    <input type="text" class="form-control" value="En proceso" disabled>
                                    <input type="hidden" name="estado" value="en_proceso">
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Fecha Matrícula</label>
                                    <input type="date" id="matricula_fecha" name="fechaMatricula" class="form-control" readonly>
                                    <div class="form-text">Se autocompleta con la fecha de ingreso del estudiante.</div>
                                </div>
                                <div class="col-md-4">
                                    <label class="form-label">Grado/Curso</label>
                                    <select name="curso_id" class="form-select" required>
                                        <option value="" selected disabled>Seleccione un curso</option>
                                        @foreach(($cursos ?? []) as $curso)
                                            <option value="{{ $curso->idCurso }}" @selected(old('curso_id') == $curso->idCurso)>
                                                {{ $curso->grado }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-12">
                                    <button type="submit" class="btn btn-primary">Generar Matrícula</button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                <script>
                    (function() {
                        const formA = document.getElementById('formAcudiente');
                        const formM = document.getElementById('formMatricula');
                        const docInputM = document.getElementById('matricula_noDocumento');
                        const fechaMatricula = document.getElementById('matricula_fecha');
                        const docInputA = document.getElementById('acudiente_noDocumento');
                        const nombreEstudianteA = document.getElementById('acudiente_nombre_estudiante');

                        if (formA) {
                            formA.addEventListener('submit', function(e) {
                                const doc = document.getElementById('acudiente_noDocumento').value;
                                if (!doc) { e.preventDefault(); return; }
                                this.action = '{{ url('/estudiantes/documento') }}' + '/' + encodeURIComponent(doc) + '/acudiente';
                            });
                        }
                        if (formM) {
                            formM.addEventListener('submit', function(e) {
                                const doc = docInputM.value;
                                if (!doc) { e.preventDefault(); return; }
                                this.action = '{{ url('/estudiantes/documento') }}' + '/' + encodeURIComponent(doc) + '/matricula';
                            });
                        }

                        async function fetchEstudiante(doc) {
                            if (!doc) { return null; }
                            try {
                                const res = await fetch('{{ url('/estudiantes/documento') }}' + '/' + encodeURIComponent(doc));
                                if (!res.ok) { return null; }
                                const data = await res.json();
                                return data;
                            } catch (err) {
                                return null;
                            }
                        }

                        async function actualizarNombreEstudianteAcudiente(doc) {
                            const data = await fetchEstudiante(doc);
                            if (nombreEstudianteA) {
                                if (data && data.found) {
                                    const p = data.persona || {};
                                    const nombre = [p.primerNombre, p.primerApellido].filter(Boolean).join(' ');
                                    nombreEstudianteA.textContent = nombre || 'Encontrado';
                                } else {
                                    nombreEstudianteA.textContent = 'No encontrado';
                                }
                            }
                        }

                        async function actualizarFechaMatricula(doc) {
                            const data = await fetchEstudiante(doc);
                            if (!fechaMatricula) return;
                            if (data && data.found && data.fechaIngreso) {
                                fechaMatricula.value = data.fechaIngreso;
                            } else {
                                fechaMatricula.value = '';
                            }
                        }

                        if (docInputM && fechaMatricula) {
                            docInputM.addEventListener('change', function() { actualizarFechaMatricula(this.value); });
                            docInputM.addEventListener('blur', function() { actualizarFechaMatricula(this.value); });
                        }
                        if (docInputA && nombreEstudianteA) {
                            docInputA.addEventListener('change', function() { actualizarNombreEstudianteAcudiente(this.value); });
                            docInputA.addEventListener('blur', function() { actualizarNombreEstudianteAcudiente(this.value); });
                        }
                    })();
                </script>
            </div>
        </div>
    </div>
</div>
@endsection