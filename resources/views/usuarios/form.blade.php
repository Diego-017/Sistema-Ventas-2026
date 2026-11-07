@extends('layouts.main')
@section('title', $usuario ? 'Editar Usuario' : 'Nuevo Usuario')
@section('content')

<div class="page-header" style="margin-bottom: 22px">
  <div>
    <h1 style="color:#38bdf8; font-size:1.75rem; font-weight:800; margin:0">
      {!! $usuario ? '<i class="fa-solid fa-pen"></i> Editar Usuario' : '<i class="fa-solid fa-plus"></i> Registrar Nuevo Usuario' !!}
    </h1>
    <p class="page-sub" style="color:#94a3b8; margin-top:4px">
      {{ $usuario ? 'Actualización de credenciales, roles y permisos' : 'Ficha completa de registro de usuario del sistema' }}
    </p>
  </div>
  <div>
    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
      <i class="fa-solid fa-arrow-left"></i> Volver a Usuarios
    </a>
  </div>
</div>

<div class="card" style="max-width:920px; border-radius:10px;">
  <div class="card-body" style="padding:26px">
    <form method="POST" action="{{ $usuario ? route('usuarios.actualizar', $usuario->id) : route('usuarios.guardar') }}">
      @csrf
      @if($usuario)
        @method('PUT')
      @endif

      <h4 style="color:#38bdf8; font-size:1rem; font-weight:800; margin-bottom:16px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">
        <i class="fa-solid fa-id-badge" style="margin-right:6px"></i> 1. Información de Acceso y Credenciales
      </h4>

      <div class="form-row">
        <div class="form-group" style="flex:1">
          <label>Nombre Completo <span class="req" style="color:#ef4444">*</span></label>
          <input type="text" name="nombre" class="form-control" required
                 value="{{ old('nombre', $usuario->nombre ?? '') }}" placeholder="Ej: Juan Carlos Pérez">
        </div>
        <div class="form-group" style="flex:1">
          <label>Correo Electrónico (Usuario Login) <span class="req" style="color:#ef4444">*</span></label>
          <input type="email" name="email" class="form-control" required
                 value="{{ old('email', $usuario->email ?? '') }}" placeholder="usuario@ventas.com">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group" style="flex:1">
          <label>Rol en el Sistema <span class="req" style="color:#ef4444">*</span></label>
          <select name="rol" class="form-control" required>
            @foreach($roles as $key => $label)
              <option value="{{ $key }}" {{ old('rol', $usuario->rol ?? '') === $key ? 'selected' : '' }}>
                {{ $label }}
              </option>
            @endforeach
          </select>
          <small class="text-muted" style="display:block; margin-top:4px">Define los permisos y vistas accesibles</small>
        </div>
        <div class="form-group" style="flex:1">
          <label>Sucursal Asignada</label>
          <select name="sucursal" class="form-control">
            @foreach($sucursales as $s)
              <option value="{{ $s }}" {{ old('sucursal', $usuario->sucursal ?? 'Sucursal Central') === $s ? 'selected' : '' }}>
                {{ $s }}
              </option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group" style="flex:1">
          <label>{{ $usuario ? 'Nueva Contraseña (dejar en blanco para no cambiar)' : 'Contraseña' }} <span class="req" style="color:#ef4444">{{ $usuario ? '' : '*' }}</span></label>
          <input type="password" name="password" class="form-control" {{ $usuario ? '' : 'required' }} placeholder="Mínimo 6 caracteres">
        </div>
        <div class="form-group" style="flex:1">
          <label>Confirmar Contraseña <span class="req" style="color:#ef4444">{{ $usuario ? '' : '*' }}</span></label>
          <input type="password" name="password_confirmation" class="form-control" {{ $usuario ? '' : 'required' }} placeholder="Repetir contraseña">
        </div>
      </div>

      <h4 style="color:#38bdf8; font-size:1rem; font-weight:800; margin:24px 0 16px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:8px;">
        <i class="fa-solid fa-address-card" style="margin-right:6px"></i> 2. Datos de Contacto y Ficha Laboral
      </h4>

      <div class="form-row">
        <div class="form-group" style="flex:1">
          <label>Teléfono / Celular</label>
          <input type="text" name="telefono" class="form-control"
                 value="{{ old('telefono', $usuario->telefono ?? '') }}" placeholder="Ej: 7890-1234">
        </div>
        <div class="form-group" style="flex:1">
          <label>Cargo / Puesto Formal</label>
          <input type="text" name="cargo" class="form-control"
                 value="{{ old('cargo', $usuario->cargo ?? '') }}" placeholder="Ej: Cajero Turno Mañana">
        </div>
      </div>

      <div class="form-group">
        <label>Dirección Domiciliar</label>
        <input type="text" name="direccion" class="form-control"
               value="{{ old('direccion', $usuario->direccion ?? '') }}" placeholder="Dirección de residencia">
      </div>

      <div class="form-group">
        <label>Notas / Observaciones del Colaborador</label>
        <textarea name="notas" class="form-control" rows="2" placeholder="Observaciones internas, horario asignado...">{{ old('notas', $usuario->notas ?? '') }}</textarea>
      </div>

      <div class="form-group" style="margin-top:16px">
        <label style="display:flex; align-items:center; gap:8px; cursor:pointer">
          <input type="checkbox" name="activo" value="1" {{ old('activo', $usuario->activo ?? 1) ? 'checked' : '' }}>
          <span style="font-weight:700">Usuario Activo con Acceso al Sistema</span>
        </label>
      </div>

      <div class="form-actions" style="margin-top:28px; display:flex; justify-content:flex-end; gap:12px; border-top:1px solid rgba(255,255,255,0.1); padding-top:18px">
        <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
        <button type="submit" class="btn btn-primary" style="font-weight:800; padding:10px 24px">
          <i class="fa-solid fa-floppy-disk" style="margin-right:6px"></i> {{ $usuario ? 'Guardar Cambios' : 'Registrar Usuario' }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection