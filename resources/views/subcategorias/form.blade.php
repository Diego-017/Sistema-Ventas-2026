@extends('layouts.main')
@section('title', $subcategoria ? 'Editar Subcategoría: ' . $subcategoria->nombre : 'Nueva Subcategoría')
@section('content')

<div class="page-header" style="margin-bottom: 20px">
  <div>
    <h1 style="color: #38bdf8; font-size: 1.6rem; font-weight: 800; margin:0">
      {{ $subcategoria ? '✏️ Editar Subcategoría' : '➕ Registrar Nueva Subcategoría' }}
    </h1>
    <p class="page-sub" style="color: #94a3b8; margin-top:4px">
      {{ $subcategoria ? 'Modifica la subclasificación y su categoría padre' : 'Crea una subcategoría para clasificar con mayor detalle tus productos' }}
    </p>
  </div>
  <div>
    <a href="{{ route('subcategorias.index') }}" class="btn btn-light" style="font-weight:700">← Volver al Listado</a>
  </div>
</div>

<div class="card" style="border-radius:10px; max-width:650px; margin:0 auto 30px">
  <div class="card-header" style="padding:14px 22px; border-bottom:1px solid #282d3c">
    <h3 style="margin:0; font-size:1.1rem; font-weight:800">
      Ficha de Subcategoría {{ $subcategoria ? '(#'.$subcategoria->id.')' : '' }}
    </h3>
  </div>
  <div class="card-body" style="padding:24px">
    <form action="{{ $subcategoria ? route('subcategorias.actualizar', $subcategoria->id) : route('subcategorias.guardar') }}" method="POST">
      @csrf
      @if($subcategoria)
        @method('PUT')
      @endif

      <div class="form-group" style="margin-bottom:18px">
        <label style="font-weight:700">Categoría Padre <span class="req" style="color:#ef4444">*</span></label>
        <select name="categoria_id" class="form-control" required>
          <option value="">-- Selecciona una categoría --</option>
          @foreach($categorias as $cat)
          <option value="{{ $cat->id }}" {{ old('categoria_id', $subcategoria->categoria_id ?? '') == $cat->id ? 'selected' : '' }}>
            {{ $cat->nombre }}
          </option>
          @endforeach
        </select>
      </div>

      <div class="form-group" style="margin-bottom:18px">
        <label style="font-weight:700">Nombre de la Subcategoría <span class="req" style="color:#ef4444">*</span></label>
        <input type="text" name="nombre" class="form-control" required
               value="{{ old('nombre', $subcategoria->nombre ?? '') }}" placeholder="Ej: Gaseosas, Jugos, Lácteos...">
      </div>

      <div class="form-group" style="margin-bottom:24px">
        <label style="font-weight:700">Descripción (Opcional)</label>
        <textarea name="descripcion" class="form-control" rows="3" placeholder="Detalles de la subcategoría...">{{ old('descripcion', $subcategoria->descripcion ?? '') }}</textarea>
      </div>

      <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #282d3c; padding-top:20px">
        <a href="{{ route('subcategorias.index') }}" class="btn btn-light" style="padding:10px 20px; font-weight:700">
          ✕ Cancelar
        </a>
        <button type="submit" class="btn btn-primary" style="padding:10px 24px; font-weight:800; font-size:0.95rem">
          💾 {{ $subcategoria ? 'Guardar Cambios' : 'Registrar Subcategoría' }}
        </button>
      </div>
    </form>
  </div>
</div>

@endsection
