@extends('layouts.main')
@section('title', 'Eliminar Subcategoría')
@section('content')

<div class="page-header" style="margin-bottom: 20px">
  <div>
    <h1 style="color: #ef4444; font-size: 1.6rem; font-weight: 800; margin:0">⚠️ Confirmar Eliminación de Subcategoría</h1>
    <p class="page-sub" style="color: #94a3b8; margin-top:4px">Verifica los productos asociados antes de eliminar</p>
  </div>
  <div>
    <a href="{{ route('subcategorias.index') }}" class="btn btn-light" style="font-weight:700">← Cancelar y Volver</a>
  </div>
</div>

<div class="card" style="border-radius:10px; max-width:650px; margin:0 auto 30px; border:1px solid #ef4444">
  <div class="card-header" style="background:rgba(239,68,68,0.12); padding:16px 22px; border-bottom:1px solid rgba(239,68,68,0.25)">
    <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:#ef4444; display:flex; align-items:center; gap:8px">
      🗑️ ¿Estás seguro de que deseas eliminar esta subcategoría?
    </h3>
  </div>

  <div class="card-body" style="padding:24px">
    <div style="background:rgba(0,0,0,0.04); padding:16px; border-radius:8px; margin-bottom:20px; border:1px solid #334155">
      <div style="font-size:1.3rem; font-weight:800; color:#38bdf8; margin-bottom:6px">
        {{ $subcategoria->nombre }}
      </div>
      <div style="font-size:0.9rem; color:#94a3b8; margin-bottom:12px">
        Categoría: <strong>{{ $subcategoria->categoria->nombre ?? 'Sin categoría' }}</strong>
      </div>
      <div>
        <strong>Productos asignados:</strong>
        <span class="badge {{ $subcategoria->productos_count > 0 ? 'badge-danger' : 'badge-success' }}">
          {{ $subcategoria->productos_count }} producto(s)
        </span>
      </div>
    </div>

    @if($subcategoria->productos_count > 0)
    <div class="alert alert-danger" style="margin-bottom:20px">
      🚫 <strong>Operación bloqueada:</strong> No se puede eliminar esta subcategoría porque contiene <strong>{{ $subcategoria->productos_count }} productos</strong> asociados. Reasigna o elimina los productos primero.
    </div>
    <div style="text-align:right">
      <a href="{{ route('subcategorias.index') }}" class="btn btn-light" style="font-weight:700">← Volver al Listado</a>
    </div>
    @else
    <form action="{{ route('subcategorias.eliminar', $subcategoria->id) }}" method="POST">
      @csrf
      @method('DELETE')

      <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #282d3c; padding-top:20px">
        <a href="{{ route('subcategorias.index') }}" class="btn btn-light" style="padding:10px 20px; font-weight:700">
          ✕ Cancelar
        </a>
        <button type="submit" class="btn btn-danger" style="padding:10px 24px; font-weight:800">
          🗑️ Sí, Eliminar Subcategoría Definitivamente
        </button>
      </div>
    </form>
    @endif
  </div>
</div>

@endsection
