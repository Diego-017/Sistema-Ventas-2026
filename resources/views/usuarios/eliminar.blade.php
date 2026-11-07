@extends('layouts.main')
@section('title', 'Eliminar Usuario')
@section('content')

<div class="page-header" style="margin-bottom: 22px">
  <div>
    <h1 style="color:#ef4444; font-size:1.75rem; font-weight:800; margin:0">
      <i class="fa-solid fa-triangle-exclamation"></i> Confirmar Eliminación de Usuario
    </h1>
    <p class="page-sub" style="color:#94a3b8; margin-top:4px">
      Esta acción removerá el acceso del usuario seleccionado al sistema.
    </p>
  </div>
  <div>
    <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">
      <i class="fa-solid fa-arrow-left"></i> Cancelar y Volver
    </a>
  </div>
</div>

<div class="card" style="max-width:680px; border-radius:10px; border-left:5px solid #ef4444">
  <div class="card-body" style="padding:26px">
    <div style="display:flex; align-items:center; gap:16px; margin-bottom:20px">
      <div style="width:54px; height:54px; border-radius:50%; background:#ef4444; color:#fff; display:flex; align-items:center; justify-content:center; font-size:1.5rem; font-weight:800">
        {{ strtoupper(substr($usuario->nombre, 0, 1)) }}
      </div>
      <div>
        <h3 style="margin:0; font-size:1.25rem; font-weight:800; color:var(--text-color, #fff)">{{ $usuario->nombre }}</h3>
        <p style="margin:2px 0 0; color:#94a3b8; font-size:0.9rem">{{ $usuario->email }} — <span class="badge badge-info">{{ ucfirst($usuario->rol) }}</span></p>
      </div>
    </div>

    <div class="alert alert-danger" style="margin-bottom:22px; line-height:1.5">
      <strong>¿Estás seguro de que deseas eliminar este usuario?</strong><br>
      Si el usuario tiene registros de ventas, compras o aperturas de caja asociadas, se recomienda únicamente <strong>desactivar su cuenta</strong> para conservar la trazabilidad y auditoría.
    </div>

    <table class="table" style="margin-bottom:24px">
      <tbody>
        <tr>
          <td style="width:160px; font-weight:700; color:#94a3b8">Sucursal Asignada:</td>
          <td>{{ $usuario->sucursal ?? 'Sucursal Central' }}</td>
        </tr>
        <tr>
          <td style="font-weight:700; color:#94a3b8">Cargo / Puesto:</td>
          <td>{{ $usuario->cargo ?? '—' }}</td>
        </tr>
        <tr>
          <td style="font-weight:700; color:#94a3b8">Teléfono:</td>
          <td>{{ $usuario->telefono ?? '—' }}</td>
        </tr>
        <tr>
          <td style="font-weight:700; color:#94a3b8">Estado Actual:</td>
          <td>
            <span class="badge badge-{{ $usuario->activo ? 'success' : 'danger' }}">
              {{ $usuario->activo ? 'ACTIVO' : 'INACTIVO' }}
            </span>
          </td>
        </tr>
      </tbody>
    </table>

    <div style="display:flex; justify-content:flex-end; gap:12px; border-top:1px solid rgba(255,255,255,0.1); padding-top:18px">
      <a href="{{ route('usuarios.index') }}" class="btn btn-secondary">Cancelar</a>
      <form method="POST" action="{{ route('usuarios.eliminar', $usuario->id) }}" style="margin:0">
        @csrf
        @method('DELETE')
        <button type="submit" class="btn btn-danger" style="font-weight:800; padding:10px 20px">
          <i class="fa-solid fa-trash" style="margin-right:6px"></i> Confirmar Eliminación
        </button>
      </form>
    </div>
  </div>
</div>
@endsection