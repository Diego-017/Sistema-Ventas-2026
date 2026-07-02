@extends('layouts.main')
@section('title', $producto ? 'Editar Producto' : 'Nuevo Producto')
@section('content')
<div class="page-header">
  <h1>{{ $producto ? '✏️ Editar Producto' : '➕ Nuevo Producto' }}</h1>
  <a href="{{ route('productos.index') }}" class="btn btn-light">← Volver</a>
</div>
<div class="card">
  <div class="card-body">
    <form action="{{ $producto ? route('productos.actualizar',$producto->id) : route('productos.guardar') }}"
          method="POST" enctype="multipart/form-data">
      @csrf
      @if($producto) @method('PUT') @endif

      <div class="form-row">
        <div class="form-group">
          <label>SKU / Código</label>
          <input type="text" name="sku" class="form-control" value="{{ old('sku',$producto->sku??'') }}" placeholder="TECH-001">
          @error('sku')<span class="text-danger">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
          <label>Código de Barras</label>
          <input type="text" name="codigo_barras" class="form-control" value="{{ old('codigo_barras',$producto->codigo_barras??'') }}">
        </div>
        <div class="form-group">
          <label>Código de Barras 2</label>
          <input type="text" name="codigo_barras2" class="form-control" value="{{ old('codigo_barras2',$producto->codigo_barras2??'') }}" placeholder="Opcional">
        </div>
      </div>

      <div class="form-group">
        <label>Nombre <span class="req">*</span></label>
        <input type="text" name="nombre" class="form-control" required value="{{ old('nombre',$producto->nombre??'') }}">
        @error('nombre')<span class="text-danger">{{ $message }}</span>@enderror
      </div>

      <div class="form-group">
        <label>Descripción</label>
        <textarea name="descripcion" class="form-control" rows="2">{{ old('descripcion',$producto->descripcion??'') }}</textarea>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Categoría</label>
          <select name="categoria_id" id="categoriaSelect" class="form-control" onchange="cargarSubcategorias(this.value)">
            <option value="">— Sin categoría —</option>
            @foreach($categorias as $c)
            <option value="{{ $c->id }}" {{ old('categoria_id',$producto->categoria_id??'')==$c->id?'selected':'' }}>{{ $c->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Subcategoría</label>
          <select name="subcategoria_id" id="subcategoriaSelect" class="form-control">
            <option value="">— Sin subcategoría —</option>
            @foreach($subcategorias ?? [] as $s)
            <option value="{{ $s->id }}" {{ old('subcategoria_id',$producto->subcategoria_id??'')==$s->id?'selected':'' }}>{{ $s->nombre }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group">
          <label>Proveedor</label>
          <select name="proveedor_id" class="form-control">
            <option value="">— Sin proveedor —</option>
            @foreach($proveedores as $p)
            <option value="{{ $p->id }}" {{ old('proveedor_id',$producto->proveedor_id??'')==$p->id?'selected':'' }}>{{ $p->nombre }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="form-row">
        <div class="form-group">
          <label>Precio Compra ($)</label>
          <input type="number" name="precio_compra" step="0.01" min="0" class="form-control" value="{{ old('precio_compra',$producto->precio_compra??'0.00') }}">
        </div>
        <div class="form-group">
          <label>Precio Venta ($) <span class="req">*</span></label>
          <input type="number" name="precio_venta" step="0.01" min="0.01" required class="form-control" value="{{ old('precio_venta',$producto->precio_venta??'') }}">
          @error('precio_venta')<span class="text-danger">{{ $message }}</span>@enderror
        </div>
        <div class="form-group">
          <label>Precio Mayoreo ($)</label>
          <input type="number" name="precio_mayoreo" step="0.01" min="0" class="form-control" value="{{ old('precio_mayoreo',$producto->precio_mayoreo??'0.00') }}">
        </div>
      </div>

      @if($producto)
      <div class="form-group">
        <label>Motivo del cambio de precio (si aplica)</label>
        <input type="text" name="motivo_cambio_precio" class="form-control" placeholder="Ej: Ajuste por inflación, nuevo proveedor...">
      </div>
      @endif

      <div class="form-row">
        <div class="form-group">
          <label>Stock</label>
          <input type="number" name="stock" min="0" class="form-control" value="{{ old('stock',$producto->stock??0) }}">
        </div>
        <div class="form-group">
          <label>Stock Mínimo</label>
          <input type="number" name="stock_minimo" min="0" class="form-control" value="{{ old('stock_minimo',$producto->stock_minimo??5) }}">
        </div>
        <div class="form-group">
          <label>Unidad de Medida</label>
          <select name="unidad" class="form-control">
            @foreach(['unidad','caja','lata','bolsa','kg','lb','litro','metro','par'] as $u)
            <option value="{{ $u }}" {{ old('unidad',$producto->unidad??'unidad')==$u?'selected':'' }}>{{ ucfirst($u) }}</option>
            @endforeach
          </select>
        </div>
      </div>

      <div class="form-group">
        <label>Imagen</label>
        @if(!empty($producto->imagen))
        <div class="mb-2"><img src="{{ asset('storage/'.$producto->imagen) }}" style="max-height:80px;border-radius:8px"></div>
        @endif
        <input type="file" name="imagen" accept="image/jpeg,image/png,image/webp" class="form-control">
        @error('imagen')<span class="text-danger">{{ $message }}</span>@enderror
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary">💾 Guardar</button>
        <a href="{{ route('productos.index') }}" class="btn btn-secondary">Cancelar</a>
      </div>
    </form>
  </div>
</div>
@push('scripts')
<script>
async function cargarSubcategorias(catId){
  const sel = document.getElementById('subcategoriaSelect');
  sel.innerHTML = '<option value="">— Sin subcategoría —</option>';
  if(!catId) return;
  const res = await fetch(`/subcategorias/por-categoria/${catId}`);
  const list = await res.json();
  list.forEach(s => {
    sel.innerHTML += `<option value="${s.id}">${s.nombre}</option>`;
  });
}
</script>
@endpush
@endsection
