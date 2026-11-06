@extends('layouts.main')
@section('title', 'Eliminar Categoría')
@section('content')

<div class="page-header" style="margin-bottom: 20px">
  <div>
    <h1 style="color: #ef4444; font-size: 1.6rem; font-weight: 800; margin:0"><i class="fa-solid fa-triangle-exclamation"></i> Confirmar Eliminación de Categoría</h1>
    <p class="page-sub" style="color: #94a3b8; margin-top:4px">Verifica los productos asociados antes de eliminar</p>
  </div>
  <div>
    <a href="{{ route('categorias.index') }}" class="btn btn-light" style="font-weight:700">← Cancelar y Volver</a>
  </div>
</div>

<div class="card" style="border-radius:10px; max-width:650px; margin:0 auto 30px; border:1px solid #ef4444">
  <div class="card-header" style="background:rgba(239,68,68,0.12); padding:16px 22px; border-bottom:1px solid rgba(239,68,68,0.25)">
    <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:#ef4444; display:flex; align-items:center; gap:8px">
      <i class="fa-solid fa-trash"></i> ¿Estás seguro de que deseas eliminar esta categoría?
    </h3>
  </div>

  <div class="card-body" style="padding:24px">
    <div style="background:rgba(0,0,0,0.04); padding:16px; border-radius:8px; margin-bottom:20px; border:1px solid #334155">
      <div style="font-size:1.3rem; font-weight:800; color:#38bdf8; margin-bottom:6px">
        {{ $categoria->nombre }}
      </div>
      <div style="font-size:0.9rem; color:#94a3b8; margin-bottom:12px">
        {{ $categoria->descripcion ?? 'Sin descripción' }}
      </div>
      <div>
        <strong>Productos asignados:</strong>
        <span class="badge {{ $categoria->productos_count > 0 ? 'badge-danger' : 'badge-success' }}">
          {{ $categoria->productos_count }} producto(s)
        </span>
      </div>
    </div>

    @if($categoria->productos_count > 0)
    <div class="alert alert-danger" style="margin-bottom:20px">
      <i class="fa-solid fa-ban"></i> <strong>Operación bloqueada:</strong> No se puede eliminar esta categoría porque actualmente contiene <strong>{{ $categoria->productos_count }} productos</strong> asociados. Debes reasignar o eliminar los productos primero.
    </div>
    <div style="text-align:right">
      <a href="{{ route('categorias.index') }}" class="btn btn-light" style="font-weight:700">← Volver al Listado</a>
    </div>
    @else
    <form action="{{ route('categorias.eliminar', $categoria->id) }}" method="POST">
      @csrf
      @method('DELETE')

      <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #282d3c; padding-top:20px">
        <a href="{{ route('categorias.index') }}" class="btn btn-light" style="padding:10px 20px; font-weight:700">
          <i class="fa-solid fa-xmark"></i> Cancelar
        </a>
        <button type="submit" class="btn btn-danger" style="padding:10px 24px; font-weight:800">
          <i class="fa-solid fa-trash"></i> Sí, Eliminar Categoría Definitivamente
        </button>
      </div>
    </form>
    @endif
  </div>
</div>

@endsection
