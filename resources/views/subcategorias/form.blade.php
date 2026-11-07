@extends('layouts.main')
@section('title', $subcategoria ? 'Editar Subcategoría: ' . $subcategoria->nombre : 'Nueva Subcategoría')
@section('content')

<div class="page-header" style="margin-bottom: 20px">
  <div>
    <h1 style="color: #38bdf8; font-size: 1.6rem; font-weight: 800; margin:0">
      {!! $subcategoria ? '<i class="fa-solid fa-pen"></i> Editar Subcategoría' : '<i class="fa-solid fa-plus"></i> Registrar Nueva Subcategoría' !!}
    </h1>
    <p class="page-sub" style="color: #94a3b8; margin-top:4px">
      {{ $subcategoria ? 'Modifica los parámetros de la subcategoría' : 'Crea una subclasificación específica vinculada a una categoría padre' }}
    </p>
  </div>
  <div>
    <a href="{{ route('subcategorias.index') }}" class="btn btn-secondary" style="font-weight:700">
      <i class="fa-solid fa-arrow-left"></i> Volver al Listado
    </a>
  </div>
</div>

<div class="card" style="border-radius:10px; max-width:800px; margin:0 auto 30px">
  <div class="card-header" style="padding:14px 22px; border-bottom:1px solid #282d3c">
    <h3 style="margin:0; font-size:1.1rem; font-weight:800; display:flex; align-items:center; gap:8px">
      <i class="fa-solid fa-folder-tree" style="color:#38bdf8"></i> Ficha Técnica de Subcategoría {{ $subcategoria ? '(#'.$subcategoria->id.')' : '' }}
    </h3>
  </div>
  <div class="card-body" style="padding:26px">
    <form action="{{ $subcategoria ? route('subcategorias.actualizar', $subcategoria->id) : route('subcategorias.guardar') }}" method="POST">
      @csrf
      @if($subcategoria)
        @method('PUT')
      @endif

      <div class="form-row">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Categoría Padre <span class="req" style="color:#ef4444">*</span></label>
          <select name="categoria_id" class="form-control" required>
            <option value="">— Selecciona Categoría —</option>
            @foreach($categorias as $cat)
              <option value="{{ $cat->id }}" {{ old('categoria_id', $subcategoria->categoria_id ?? '') == $cat->id ? 'selected' : '' }}>
                {{ $cat->nombre }}
              </option>
            @endforeach
          </select>
          @error('categoria_id')<span class="text-danger" style="font-size:0.8rem">{{ $message }}</span>@enderror
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Código / Prefijo Subcategoría</label>
          <input type="text" name="codigo" class="form-control"
                 value="{{ old('codigo', $subcategoria->codigo ?? '') }}" placeholder="Ej: SUB-001">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group" style="flex:2">
          <label style="font-weight:700">Nombre de la Subcategoría <span class="req" style="color:#ef4444">*</span></label>
          <input type="text" name="nombre" class="form-control" required
                 value="{{ old('nombre', $subcategoria->nombre ?? '') }}" placeholder="Ej: Mouse Inalámbricos, Bebidas Energéticas...">
          @error('nombre')<span class="text-danger" style="font-size:0.8rem">{{ $message }}</span>@enderror
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Margen Sugerido (%)</label>
          <input type="number" step="0.01" min="0" max="100" name="margen_sugerido" class="form-control"
                 value="{{ old('margen_sugerido', $subcategoria->margen_sugerido ?? '20.00') }}" placeholder="20.00">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Orden de Visualización</label>
          <input type="number" name="orden" class="form-control" min="0"
                 value="{{ old('orden', $subcategoria->orden ?? '0') }}" placeholder="0">
        </div>
        <div class="form-group" style="flex:2; display:flex; align-items:center; padding-top:20px">
          <label style="display:flex; align-items:center; gap:8px; cursor:pointer">
            <input type="checkbox" name="activo" value="1" {{ old('activo', $subcategoria->activo ?? 1) ? 'checked' : '' }}>
            <span style="font-weight:700">Subcategoría Activa en Inventario y POS</span>
          </label>
        </div>
      </div>

      <div class="form-group" style="margin-bottom:24px">
        <label style="font-weight:700">Descripción (Opcional)</label>
        <textarea name="descripcion" class="form-control" rows="3" placeholder="Detalles de la subclasificación...">{{ old('descripcion', $subcategoria->descripcion ?? '') }}</textarea>
      </div>

      <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #282d3c; padding-top:20px">
        <a href="{{ route('subcategorias.index') }}" class="btn btn-secondary" style="padding:10px 20px; font-weight:700">
          <i class="fa-solid fa-xmark"></i> Cancelar
        </a>
        <button type="submit" class="btn btn-primary" style="padding:10px 26px; font-weight:800; font-size:0.95rem">
          <i class="fa-solid fa-floppy-disk"></i> {{ $subcategoria ? 'Guardar Cambios' : 'Registrar Subcategoría' }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection