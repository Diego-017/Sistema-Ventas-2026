@extends('layouts.main')
@section('title','Mi Perfil')
@section('content')

@php
  $esAdmin = (session('user.rol') ?? (is_array(session('user')) ? (session('user')['rol'] ?? '') : (session('user')->rol ?? ''))) === 'admin';
@endphp

<div class="page-header">
  <div>
    <h1><i class="fa-solid fa-user"></i> Mi Perfil</h1>
    <p class="page-sub">Consulta y administración de información de tu cuenta</p>
  </div>
</div>

<div class="grid-2" style="max-width:960px">
  <div class="card">
    <div class="card-header">
      <h3 style="display:flex; align-items:center; gap:8px">
        <i class="fa-solid fa-id-card" style="color:#38bdf8"></i> Información Personal
      </h3>
    </div>
    <div class="card-body">
      @if(!$esAdmin)
        <div class="alert alert-warning" style="margin-bottom:20px; border-left:4px solid #f59e0b; padding:12px 14px; font-size:0.88rem; line-height:1.45;">
          <i class="fa-solid fa-lock" style="margin-right:6px"></i>
          <strong>Acceso solo para administradores:</strong> La edición de datos personales y contraseña se encuentra bloqueada para empleados y vendedores. Solo el administrador puede realizar modificaciones.
        </div>
      @endif

      <form method="POST" action="{{ route('perfil.update') }}">
        @csrf
        <div class="form-group">
          <label>Nombre completo <span class="req">*</span></label>
          <input type="text" name="nombre" class="form-control" required
                 value="{{ old('nombre', $user->nombre) }}" {{ !$esAdmin ? 'disabled readonly' : '' }}>
          @error('nombre')<span class="text-danger">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
          <label>Email <span class="req">*</span></label>
          <input type="email" name="email" class="form-control" required
                 value="{{ old('email', $user->email) }}" {{ !$esAdmin ? 'disabled readonly' : '' }}>
          @error('email')<span class="text-danger">{{ $message }}</span>@enderror
        </div>
        <div class="form-row">
          <div class="form-group">
            <label>Teléfono</label>
            <input type="text" name="telefono" class="form-control"
                   value="{{ old('telefono', $user->telefono ?? '—') }}" {{ !$esAdmin ? 'disabled readonly' : '' }}>
          </div>
          <div class="form-group">
            <label>Cargo / Puesto</label>
            <input type="text" name="cargo" class="form-control"
                   value="{{ old('cargo', $user->cargo ?? ucfirst($user->rol)) }}" {{ !$esAdmin ? 'disabled readonly' : '' }}>
          </div>
        </div>
        <div class="form-group">
          <label>Sucursal Asignada</label>
          <input type="text" name="sucursal" class="form-control"
                 value="{{ old('sucursal', $user->sucursal ?? 'Sucursal Central') }}" {{ !$esAdmin ? 'disabled readonly' : '' }}>
        </div>

        @if($esAdmin)
        <hr style="margin:20px 0;border-color:var(--border-color, #2c3244)">
        <h4 style="margin-bottom:14px;font-size:.95rem;color:var(--text-color, #38bdf8)">Cambiar Contraseña</h4>
        <p class="text-muted small" style="margin-bottom:12px">Deja en blanco si no deseas cambiarla.</p>
        <div class="form-group">
          <label>Nueva Contraseña</label>
          <input type="password" name="password" class="form-control" placeholder="Mínimo 6 caracteres">
          @error('password')<span class="text-danger">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
          <label>Confirmar Contraseña</label>
          <input type="password" name="password_confirmation" class="form-control">
        </div>
        <div class="form-actions" style="margin-top:20px">
          <button type="submit" class="btn btn-primary"><i class="fa-solid fa-floppy-disk"></i> Actualizar Perfil</button>
        </div>
        @endif
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h3 style="display:flex; align-items:center; gap:8px">
        <i class="fa-solid fa-chart-simple" style="color:#10b981"></i> Resumen de Actividad
      </h3>
    </div>
    <div class="card-body">
      @if(!$esAdmin)
        <div class="alert alert-info" style="margin-bottom:18px; font-size:0.85rem; padding:10px 12px">
          <i class="fa-solid fa-circle-info" style="margin-right:6px"></i> Resumen informativo de operaciones de tu usuario en el sistema.
        </div>
      @endif

      <div class="profile-avatar">{{ strtoupper(substr($user->nombre,0,1)) }}</div>
      <div class="info-grid" style="margin-top:20px">
        <div class="info-item"><span>Rol en el Sistema</span><strong class="text-primary">{{ strtoupper($user->rol) }}</strong></div>
        <div class="info-item"><span>Estado de Cuenta</span><strong class="badge badge-success">{{ $user->activo ? 'ACTIVO' : 'INACTIVO' }}</strong></div>
        <div class="info-item"><span>Sucursal</span><strong>{{ $user->sucursal ?? 'Sucursal Central' }}</strong></div>
        <div class="info-item"><span>Miembro desde</span><strong>{{ $user->created_at->format('d/m/Y') }}</strong></div>
        <div class="info-item">
          <span>Ventas registradas</span>
          <strong class="text-success">{{ \App\Models\Venta::where('usuario_id',$user->id)->count() }} transacciones</strong>
        </div>
        <div class="info-item">
          <span>Ventas este mes</span>
          <strong>${{ number_format(\App\Models\Venta::where('usuario_id',$user->id)->whereMonth('created_at',now()->month)->sum('total'), 2) }}</strong>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
