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

      <div class="form-row">
        <div class="form-group">
          <label class="form-check-label" style="display:flex;align-items:center;gap:8px">
            <input type="checkbox" name="exento" value="1" style="width:auto"
              {{ old('exento',$producto->exento??false) ? 'checked' : '' }}>
            Producto exento de IVA
          </label>
          <small class="text-muted">Ej: pan francés, medicinas, canasta básica (Art. 45/46 Ley de IVA)</small>
        </div>
        <div class="form-group">
          <label>Tipo de comisión</label>
          <select name="tipo_comision" id="tipoComision" class="form-control" onchange="document.getElementById('valorComisionGroup').style.display = this.value==='ninguno' ? 'none' : 'block'">
            @foreach(['ninguno'=>'Ninguna','porcentaje'=>'% sobre venta','monto_fijo'=>'Monto fijo por venta'] as $val=>$label)
            <option value="{{ $val }}" {{ old('tipo_comision',$producto->tipo_comision??'ninguno')==$val?'selected':'' }}>{{ $label }}</option>
            @endforeach
          </select>
        </div>
        <div class="form-group" id="valorComisionGroup" style="display:{{ old('tipo_comision',$producto->tipo_comision??'ninguno')==='ninguno'?'none':'block' }}">
          <label>Valor de la comisión</label>
          <input type="number" name="valor_comision" step="0.01" min="0" class="form-control" value="{{ old('valor_comision',$producto->valor_comision??0) }}">
        </div>
      </div>

      <div class="form-group">
        <label class="form-check-label" style="display:flex;align-items:center;gap:8px">
          <input type="checkbox" name="es_combo" id="esCombo" value="1"
            onchange="document.getElementById('comboBuilder').style.display=this.checked?'block':'none'"
            {{ old('es_combo',$producto->es_combo??false) ? 'checked' : '' }}>
          Este producto es un combo/paquete (agrupa otros productos)
        </label>
      </div>

      <div id="comboBuilder" class="card" style="display:{{ old('es_combo',$producto->es_combo??false)?'block':'none' }};background:var(--bg-subtle,#f8f9fc);margin-bottom:1rem">
        <div class="card-body">
          <label>Componentes del combo</label>
          <div id="comboItemsBody"></div>
          <div class="form-row" style="align-items:flex-end">
            <div class="form-group">
              <select id="comboProductoSelect" class="form-control">
                <option value="">— Elegir producto —</option>
                @foreach($otrosProductos ?? [] as $op)
                <option value="{{ $op->id }}" data-nombre="{{ $op->nombre }}">{{ $op->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group" style="max-width:100px">
              <input type="number" id="comboCantidad" value="1" min="1" class="form-control" placeholder="Cant.">
            </div>
            <div class="form-group" style="max-width:140px">
              <button type="button" class="btn btn-secondary" onclick="agregarComboItem()">+ Agregar</button>
            </div>
          </div>
        </div>
      </div>
      <input type="hidden" name="combo_items" id="comboItemsInput">

      <div class="form-group">
        <label>Presentaciones adicionales <small class="text-muted">(ej: Unidad, Caja x24, Six Pack)</small></label>
        <div id="presentacionesBody"></div>
        <div class="form-row" style="align-items:flex-end">
          <div class="form-group"><input type="text" id="presNombre" class="form-control" placeholder="Ej: Caja x24"></div>
          <div class="form-group" style="max-width:120px"><input type="number" id="presFactor" value="1" min="0.01" step="0.01" class="form-control" placeholder="Factor"></div>
          <div class="form-group" style="max-width:120px"><input type="number" id="presPrecio" value="0" min="0" step="0.01" class="form-control" placeholder="Precio $"></div>
          <div class="form-group" style="max-width:140px"><button type="button" class="btn btn-secondary" onclick="agregarPresentacion()">+ Agregar</button></div>
        </div>
      </div>
      <input type="hidden" name="presentaciones" id="presentacionesInput">

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

// ── Combos ──────────────────────────────────────────────
@php
  $comboItemsIniciales = ($producto ?? null)?->comboItems?->map(fn($i) => [
      'componente_id' => $i->componente_id,
      'nombre'        => $i->componente->nombre ?? '—',
      'cantidad'      => $i->cantidad,
  ])->values() ?? [];
@endphp
let comboItems = @json($comboItemsIniciales);

function renderComboItems(){
  const body = document.getElementById('comboItemsBody');
  if(!comboItems.length){ body.innerHTML = '<p class="text-muted" style="margin:8px 0">Sin componentes agregados.</p>'; }
  else {
    body.innerHTML = '<table class="table table-sm"><tbody>' + comboItems.map((it,idx) => `
      <tr><td>${it.nombre}</td><td>x${it.cantidad}</td>
      <td><button type="button" class="btn btn-sm btn-danger" onclick="quitarComboItem(${idx})">✕</button></td></tr>
    `).join('') + '</tbody></table>';
  }
  document.getElementById('comboItemsInput').value = JSON.stringify(comboItems);
}
function agregarComboItem(){
  const sel = document.getElementById('comboProductoSelect');
  const cant = parseInt(document.getElementById('comboCantidad').value) || 1;
  if(!sel.value) return;
  comboItems.push({ componente_id: parseInt(sel.value), nombre: sel.selectedOptions[0].dataset.nombre, cantidad: cant });
  sel.value = ''; document.getElementById('comboCantidad').value = 1;
  renderComboItems();
}
function quitarComboItem(idx){ comboItems.splice(idx,1); renderComboItems(); }
renderComboItems();

// ── Presentaciones ──────────────────────────────────────
@php
  $presentacionesIniciales = ($producto ?? null)?->presentaciones?->map(fn($p) => [
      'nombre' => $p->nombre, 'factor' => $p->factor, 'precio' => $p->precio,
  ])->values() ?? [];
@endphp
let presentaciones = @json($presentacionesIniciales);

function renderPresentaciones(){
  const body = document.getElementById('presentacionesBody');
  if(!presentaciones.length){ body.innerHTML = '<p class="text-muted" style="margin:8px 0">Sin presentaciones adicionales (solo la unidad base).</p>'; }
  else {
    body.innerHTML = '<table class="table table-sm"><tbody>' + presentaciones.map((p,idx) => `
      <tr><td>${p.nombre}</td><td>Factor ${p.factor}</td><td>$${parseFloat(p.precio).toFixed(2)}</td>
      <td><button type="button" class="btn btn-sm btn-danger" onclick="quitarPresentacion(${idx})">✕</button></td></tr>
    `).join('') + '</tbody></table>';
  }
  document.getElementById('presentacionesInput').value = JSON.stringify(presentaciones);
}
function agregarPresentacion(){
  const nombre = document.getElementById('presNombre').value.trim();
  const factor = parseFloat(document.getElementById('presFactor').value) || 1;
  const precio = parseFloat(document.getElementById('presPrecio').value) || 0;
  if(!nombre) return;
  presentaciones.push({ nombre, factor, precio });
  document.getElementById('presNombre').value = '';
  document.getElementById('presFactor').value = 1;
  document.getElementById('presPrecio').value = 0;
  renderPresentaciones();
}
function quitarPresentacion(idx){ presentaciones.splice(idx,1); renderPresentaciones(); }
renderPresentaciones();
</script>
@endpush
@endsection
