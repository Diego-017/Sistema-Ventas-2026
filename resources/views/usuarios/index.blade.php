@extends('layouts.main')
@section('title','Gestión de Usuarios')
@section('content')
<div class="page-header" style="margin-bottom:20px">
  <div>
    <h1 style="color:#38bdf8; font-size:1.75rem; font-weight:800; margin:0">
      <i class="fa-solid fa-users-gear"></i> Gestión de Usuarios
    </h1>
    <p class="page-sub" style="color:#94a3b8; margin-top:4px">Control de accesos, roles, sucursales y permisos del personal</p>
  </div>
  <a href="{{ route('usuarios.crear') }}" class="btn btn-primary" style="display:flex; align-items:center; gap:6px; font-weight:800">
    <i class="fa-solid fa-user-plus"></i> Nuevo Usuario
  </a>
</div>

<div class="card" style="border-radius:10px;">
  <div class="card-body p-0">
    <table class="table">
      <thead>
        <tr>
          <th style="width:40px"></th>
          <th>Nombre</th>
          <th>Email / Login</th>
          <th>Rol</th>
          <th>Sucursal</th>
          <th>Estado</th>
          <th>Creado</th>
          <th style="text-align:right">Acciones</th>
        </tr>
      </thead>
      <tbody>
        @foreach($usuarios as $u)
        <tr>
          <td style="text-align:center">
            <span class="user-avatar" style="width:34px; height:34px; font-size:0.85rem">
              {{ strtoupper(substr($u->nombre,0,1)) }}
            </span>
          </td>
          <td>
            <strong>{{ $u->nombre }}</strong>
            @if($u->cargo)
              <div style="font-size:0.75rem; color:#94a3b8">{{ $u->cargo }}</div>
            @endif
          </td>
          <td><code>{{ $u->email }}</code></td>
          <td>
            @php
              $roleMap = [
                'admin'      => ['label' => 'Admin', 'icon' => 'fa-crown', 'color' => '#eab308'],
                'vendedor'   => ['label' => 'Vendedor', 'icon' => 'fa-user-tie', 'color' => '#38bdf8'],
                'supervisor' => ['label' => 'Supervisor', 'icon' => 'fa-user-shield', 'color' => '#f97316'],
                'bodeguero'  => ['label' => 'Bodeguero', 'icon' => 'fa-boxes-stacked', 'color' => '#10b981'],
                'contador'   => ['label' => 'Contador', 'icon' => 'fa-calculator', 'color' => '#a855f7'],
              ];
              $rInfo = $roleMap[$u->rol] ?? ['label' => ucfirst($u->rol), 'icon' => 'fa-user', 'color' => '#94a3b8'];
            @endphp
            <span style="display:inline-flex; align-items:center; gap:6px; font-weight:700; color:{{ $rInfo['color'] }}">
              <i class="fa-solid {{ $rInfo['icon'] }}"></i> {{ $rInfo['label'] }}
            </span>
          </td>
          <td><span style="font-size:0.85rem; color:#94a3b8">{{ $u->sucursal ?? 'Sucursal Central' }}</span></td>
          <td>
            <span class="badge badge-{{ $u->activo ? 'success':'danger' }}">
              {{ $u->activo ? 'Activo':'Inactivo' }}
            </span>
          </td>
          <td style="color:#94a3b8; font-size:0.82rem">{{ $u->created_at->format('d/m/Y') }}</td>
          <td style="text-align:right; white-space:nowrap">
            <a href="{{ route('usuarios.editar', $u->id) }}" class="btn btn-sm btn-light" title="Editar Usuario">
              <i class="fa-solid fa-pen"></i>
            </a>
            @if($u->id !== session('user_id'))
              <button onclick="toggleActivo({{ $u->id }}, this)" class="btn btn-sm btn-{{ $u->activo ? 'warning':'success' }}" title="{{ $u->activo ? 'Desactivar':'Activar' }}">
                <i class="fa-solid fa-{{ $u->activo ? 'ban' : 'circle-check' }}"></i>
                <span>{{ $u->activo ? 'Desactivar':'Activar' }}</span>
              </button>
              <a href="{{ route('usuarios.confirmar_eliminar', $u->id) }}" class="btn btn-sm btn-danger" title="Eliminar Usuario">
                <i class="fa-solid fa-trash"></i>
              </a>
            @else
              <span class="badge badge-secondary" style="font-size:0.75rem">Tú</span>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

@push('scripts')
<script>
async function toggleActivo(id, btn){
  if (!confirm('¿Deseas cambiar el estado de este usuario?')) return;
  try {
    const res = await fetch(`/usuarios/${id}/toggle`, {
      method: 'PATCH',
      headers: {
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
        'Accept': 'application/json'
      }
    });
    const d = await res.json();
    if(d.ok){
      location.reload();
    } else {
      alert(d.error || 'No se pudo cambiar el estado.');
    }
  } catch(e) {
    alert('Error al comunicar con el servidor.');
  }
}
</script>
@endpush
@endsection