@extends('layouts.main')
@section('title', $categoria ? 'Editar Categoría: ' . $categoria->nombre : 'Nueva Categoría')
@section('content')

<div class="page-header" style="margin-bottom: 20px">
  <div>
    <h1 style="color: #38bdf8; font-size: 1.6rem; font-weight: 800; margin:0">
      {{ $categoria ? '✏️ Editar Categoría' : '➕ Registrar Nueva Categoría' }}
    </h1>
    <p class="page-sub" style="color: #94a3b8; margin-top:4px">
      {{ $categoria ? 'Modifica el nombre y descripción de la categoría' : 'Crea una nueva clasificación para organizar los productos' }}
    </p>
  </div>
  <div>
    <a href="{{ route('categorias.index') }}" class="btn btn-light" style="font-weight:700">← Volver al Listado</a>
  </div>
</div>

<div class="card" style="border-radius:10px; max-width:650px; margin:0 auto 30px">
  <div class="card-header" style="padding:14px 22px; border-bottom:1px solid #282d3c">
    <h3 style="margin:0; font-size:1.1rem; font-weight:800">
      Ficha de Categoría {{ $categoria ? '(#'.$categoria->id.')' : '' }}
    </h3>
  </div>
  <div class="card-body" style="padding:24px">
    <form action="{{ $categoria ? route('categorias.actualizar', $categoria->id) : route('categorias.guardar') }}" method="POST">
      @csrf
      @if($categoria)
        @method('PUT')
      @endif

      <div class="form-group" style="margin-bottom:18px">
        <label style="font-weight:700">Nombre de la Categoría <span class="req" style="color:#ef4444">*</span></label>
        <input type="text" name="nombre" class="form-control" required
               value="{{ old('nombre', $categoria->nombre ?? '') }}" placeholder="Ej: Bebidas, Snacks, Ferretería...">
        @error('nombre')<span class="text-danger" style="font-size:0.8rem">{{ $message }}</span>@enderror
      </div>

      <div class="form-group" style="margin-bottom:24px">
        <label style="font-weight:700">Descripción (Opcional)</label>
        <textarea name="descripcion" class="form-control" rows="3" placeholder="Detalles o alcance de los productos incluidos...">{{ old('descripcion', $categoria->descripcion ?? '') }}</textarea>
      </div>

      <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #282d3c; padding-top:20px">
        <a href="{{ route('categorias.index') }}" class="btn btn-light" style="padding:10px 20px; font-weight:700">
          ✕ Cancelar
        </a>
        <button type="submit" class="btn btn-primary" style="padding:10px 24px; font-weight:800; font-size:0.95rem">
          💾 {{ $categoria ? 'Guardar Cambios' : 'Registrar Categoría' }}
        </button>
      </div>
    </form>
  </div>
</div>

@endsection
