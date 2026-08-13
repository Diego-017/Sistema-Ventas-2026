@extends('layouts.main')
@section('title','Mi Perfil')
@section('content')

<div class="page-header">
  <h1>👤 Mi Perfil</h1>
</div>

<div class="grid-2" style="max-width:900px">
  <div class="card">
    <div class="card-header"><h3>Información Personal</h3></div>
    <div class="card-body">
      <form method="POST" action="{{ route('perfil.update') }}">
        @csrf
        <div class="form-group">
          <label>Nombre completo <span class="req">*</span></label>
          <input type="text" name="nombre" class="form-control" required
                 value="{{ old('nombre', $user->nombre) }}">
          @error('nombre')<span class="text-danger">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
          <label>Email <span class="req">*</span></label>
          <input type="email" name="email" class="form-control" required
                 value="{{ old('email', $user->email) }}">
          @error('email')<span class="text-danger">{{ $message }}</span>@enderror
        </div>
        <hr style="margin:20px 0;border-color:#e2e8f0">
        <h4 style="margin-bottom:16px;font-size:.95rem;color:#374151">Cambiar Contraseña</h4>
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
        <div class="form-actions">
          <button type="submit" class="btn btn-primary">💾 Actualizar Perfil</button>
        </div>
      </form>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h3>Resumen de Actividad</h3></div>
    <div class="card-body">
      <div class="profile-avatar">{{ strtoupper(substr($user->nombre,0,1)) }}</div>
      <div class="info-grid" style="margin-top:20px">
        <div class="info-item"><span>Rol</span><strong class="text-primary">{{ ucfirst($user->rol) }}</strong></div>
        <div class="info-item"><span>Miembro desde</span><strong>{{ $user->created_at->format('d/m/Y') }}</strong></div>
        <div class="info-item">
          <span>Ventas registradas</span>
          <strong>{{ \App\Models\Venta::where('usuario_id',$user->id)->count() }}</strong>
        </div>
        <div class="info-item">
          <span>Ventas este mes</span>
          <strong>{{ \App\Models\Venta::where('usuario_id',$user->id)->whereMonth('created_at',now()->month)->count() }}</strong>
        </div>
      </div>
    </div>
  </div>
</div>
@endsection
