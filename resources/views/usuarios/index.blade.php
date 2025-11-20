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
                    <a class="nav-link active" href="{{ route('dashboard') }}">
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
            <div class="container-fluid">
                @if(session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="alert alert-danger">{{ session('error') }}</div>
                @endif

                <div class="card shadow-sm mb-3">
                    <div class="card-body">
                        <form class="row g-2 align-items-end" method="GET" action="{{ route('usuarios.index') }}">
                            <div class="col-sm-6 col-md-4">
                                <label for="rol" class="form-label">Filtrar por rol</label>
                                <select class="form-select" id="rol" name="rol">
                                    <option value="" {{ empty($rolFiltro) ? 'selected' : '' }}>Todos</option>
                                    <option value="sin_rol" {{ ($rolFiltro === 'sin_rol') ? 'selected' : '' }}>Sin rol</option>
                                    @foreach($roles as $r)
                                        <option value="{{ $r->id }}" {{ ($rolFiltro == $r->id) ? 'selected' : '' }}>{{ $r->nombre }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-sm-6 col-md-4">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-filter me-1"></i>Filtrar</button>
                                <a href="{{ route('usuarios.index') }}" class="btn btn-outline-secondary ms-2">Limpiar</a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <i class="fas fa-users me-2"></i>Usuarios del sistema
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead>
                                    <tr>
                                        <th>Nombre</th>
                                        <th>Email</th>
                                        <th>Rol</th>
                                        <th class="text-end">Acciones</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($usuarios as $u)
                                        <tr>
                                            <td>{{ $u->name }}</td>
                                            <td>{{ $u->email }}</td>
                                            <td>{{ optional($u->rol)->nombre ?? 'Sin rol' }}</td>
                                            <td class="text-end">
                                                @if($u->rol)
                                                    <button class="btn btn-sm btn-outline-danger" onclick="removerRol({{ $u->id }})">
                                                        <i class="fas fa-user-times me-1"></i>Remover rol
                                                    </button>
                                                @else
                                                    <div class="d-flex justify-content-end gap-2">
                                                        <select class="form-select form-select-sm w-auto" id="rol_select_{{ $u->id }}">
                                                            <option value="">Seleccione rol</option>
                                                            @foreach($roles as $r)
                                                                <option value="{{ $r->id }}">{{ $r->nombre }}</option>
                                                            @endforeach
                                                        </select>
                                                        <button class="btn btn-sm btn-outline-primary" onclick="asignarRol({{ $u->id }})">
                                                            <i class="fas fa-user-check me-1"></i>Asignar
                                                        </button>
                                                    </div>
                                                @endif
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="4" class="text-center py-4">No hay usuarios para mostrar.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    @if($usuarios instanceof \Illuminate\Contracts\Pagination\Paginator || $usuarios instanceof \Illuminate\Contracts\Pagination\LengthAwarePaginator)
                        <div class="card-footer">
                            {{ $usuarios->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Logout Form -->
<form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
    @csrf
</form>
@push('scripts')
<script>
function csrfToken() {
  const el = document.querySelector('meta[name="csrf-token"]');
  return el ? el.getAttribute('content') : '';
}
async function asignarRol(usuarioId) {
  const select = document.getElementById('rol_select_' + usuarioId);
  const rolId = select ? select.value : '';
  if (!rolId) { alert('Seleccione un rol.'); return; }
  try {
    const res = await fetch("{{ route('roles.asignar') }}", {
      method: 'POST',
      headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken() },
      body: JSON.stringify({ usuario_id: usuarioId, rol_id: rolId })
    });
    const data = await res.json();
    if (data.exito) {
      location.reload();
    } else {
      alert(data.error || 'No se pudo asignar el rol');
    }
  } catch (e) {
    alert('Error de red al asignar rol');
  }
}
async function removerRol(usuarioId) {
  if (!confirm('¿Remover el rol de este usuario?')) return;
  try {
    const res = await fetch("{{ route('roles.remover') }}", {
      method: 'POST',
      headers: { 'Content-Type':'application/json', 'X-CSRF-TOKEN': csrfToken() },
      body: JSON.stringify({ usuario_id: usuarioId })
    });
    const data = await res.json();
    if (data.exito) {
      location.reload();
    } else {
      alert(data.error || 'No se pudo remover el rol');
    }
  } catch (e) {
    alert('Error de red al remover rol');
  }
}
</script>
@endpush
@endsection