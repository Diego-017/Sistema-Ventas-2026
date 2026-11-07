@extends('layouts.main')
@section('title', $categoria ? 'Editar Categoría: ' . $categoria->nombre : 'Nueva Categoría')
@section('content')

<div class="page-header" style="margin-bottom: 20px">
  <div>
    <h1 style="color: #38bdf8; font-size: 1.6rem; font-weight: 800; margin:0">
      {!! $categoria ? '<i class="fa-solid fa-pen"></i> Editar Categoría' : '<i class="fa-solid fa-plus"></i> Registrar Nueva Categoría' !!}
    </h1>
    <p class="page-sub" style="color: #94a3b8; margin-top:4px">
      {{ $categoria ? 'Modifica la ficha técnica, márgenes y configuración de la categoría' : 'Crea una nueva clasificación completa para organizar los productos' }}
    </p>
  </div>
  <div>
    <a href="{{ route('categorias.index') }}" class="btn btn-secondary" style="font-weight:700">
      <i class="fa-solid fa-arrow-left"></i> Volver al Listado
    </a>
  </div>
</div>

<div class="card" style="border-radius:10px; max-width:820px; margin:0 auto 30px">
  <div class="card-header" style="padding:14px 22px; border-bottom:1px solid #282d3c">
    <h3 style="margin:0; font-size:1.1rem; font-weight:800; display:flex; align-items:center; gap:8px">
      <i class="fa-solid fa-tags" style="color:#38bdf8"></i> Ficha Técnica de Categoría {{ $categoria ? '(#'.$categoria->id.')' : '' }}
    </h3>
  </div>
  <div class="card-body" style="padding:26px">
    <form action="{{ $categoria ? route('categorias.actualizar', $categoria->id) : route('categorias.guardar') }}" method="POST">
      @csrf
      @if($categoria)
        @method('PUT')
      @endif

      <div class="form-row">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Código / Prefijo</label>
          <input type="text" name="codigo" class="form-control"
                 value="{{ old('codigo', $categoria->codigo ?? '') }}" placeholder="Ej: CAT-001">
          <small class="text-muted">Identificador único interno o prefijo de SKU</small>
        </div>
        <div class="form-group" style="flex:2">
          <label style="font-weight:700">Nombre de la Categoría <span class="req" style="color:#ef4444">*</span></label>
          <input type="text" name="nombre" class="form-control" required
                 value="{{ old('nombre', $categoria->nombre ?? '') }}" placeholder="Ej: Laptops y Computadoras, Bebidas...">
          @error('nombre')<span class="text-danger" style="font-size:0.8rem">{{ $message }}</span>@enderror
        </div>
      </div>

      <div class="form-row">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Margen Sugerido de Ganancia (%)</label>
          <input type="number" step="0.01" min="0" max="100" name="margen_sugerido" class="form-control"
                 value="{{ old('margen_sugerido', $categoria->margen_sugerido ?? '25.00') }}" placeholder="25.00">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Color Representativo</label>
          <div style="display:flex; align-items:center; gap:8px">
            <input type="color" name="color" value="{{ old('color', $categoria->color ?? '#3b82f6') }}" style="height:38px; width:48px; padding:2px; border-radius:6px; background:#1e293b; border:1px solid #334155; cursor:pointer">
            <input type="text" readonly value="{{ old('color', $categoria->color ?? '#3b82f6') }}" class="form-control" style="font-family:monospace">
          </div>
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Orden de Visualización</label>
          <input type="number" name="orden" class="form-control" min="0"
                 value="{{ old('orden', $categoria->orden ?? '0') }}" placeholder="0">
        </div>
      </div>

      <div class="form-group" style="margin-bottom:18px">
        <label style="font-weight:700">Descripción Amplia de la Categoría</label>
        <textarea name="descripcion" class="form-control" rows="3" placeholder="Detalles de los productos incluidos, políticas de garantía...">{{ old('descripcion', $categoria->descripcion ?? '') }}</textarea>
      </div>

      <div class="form-group" style="margin-bottom:24px">
        <label style="display:flex; align-items:center; gap:8px; cursor:pointer">
          <input type="checkbox" name="activo" value="1" {{ old('activo', $categoria->activo ?? 1) ? 'checked' : '' }}>
          <span style="font-weight:700">Categoría Activa (Visible en catálogo y punto de venta)</span>
        </label>
      </div>

      <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #282d3c; padding-top:20px">
        <a href="{{ route('categorias.index') }}" class="btn btn-secondary" style="padding:10px 20px; font-weight:700">
          <i class="fa-solid fa-xmark"></i> Cancelar
        </a>
        <button type="submit" class="btn btn-primary" style="padding:10px 26px; font-weight:800; font-size:0.95rem">
          <i class="fa-solid fa-floppy-disk"></i> {{ $categoria ? 'Guardar Cambios' : 'Registrar Categoría' }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection