@extends('layouts.main')
@section('title','Nueva Compra')
@section('content')
<div class="page-header">
  <h1>🛒 Nueva Compra / Entrada de Inventario</h1>
  <a href="{{ route('compras.index') }}" class="btn btn-secondary">← Volver</a>
</div>
<div class="grid-pos">
  <div>
    <div class="card">
      <div class="card-header"><h3>Buscar Producto</h3></div>
      <div class="card-body">
        <input type="search" id="searchProd" class="form-control" placeholder="Nombre o SKU..." autocomplete="off">
        <div id="searchResults" class="search-results"></div>
      </div>
    </div>
  </div>
  <div>
    <div class="card">
      <div class="card-header"><h3>📋 Detalle de Compra</h3></div>
      <div class="card-body p-0">
        <table class="table" id="compraTable">
          <thead><tr><th>Producto</th><th>Cant.</th><th>Precio Unit.</th><th>Subtotal</th><th></th></tr></thead>
          <tbody id="compraBody">
            <tr id="emptyRow"><td colspan="5" class="text-center text-muted" style="padding:1.5rem">Agrega productos</td></tr>
          </tbody>
        </table>
      </div>
      <div class="card-footer">
        <div class="total-row total-final"><span>TOTAL COMPRA:</span><span id="lblTotal">$0.00</span></div>
        <form id="compraForm" method="POST" action="{{ route('compras.guardar') }}">
          @csrf
          <input type="hidden" name="items" id="itemsInput">
          <div class="form-row mt-3">
            <div class="form-group">
              <label>Proveedor</label>
              <select name="proveedor_id" class="form-control">
                <option value="">Sin proveedor</option>
                @foreach($proveedores as $p)
                <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                @endforeach
              </select>
            </div>
          </div>
          <div class="form-group">
            <label>Notas</label>
            <input type="text" name="notas" class="form-control" placeholder="Opcional...">
          </div>
          <button type="submit" class="btn btn-primary btn-block btn-lg" id="btnGuardar">
            ✅ Registrar Compra
          </button>
        </form>
      </div>
    </div>
  </div>
</div>
@push('scripts')
<script>
const cart = {};
const CSRF = document.querySelector('meta[name=csrf-token]').content;

let timer;
document.getElementById('searchProd').addEventListener('input', function() {
  clearTimeout(timer);
  timer = setTimeout(() => doSearch(this.value.trim()), 300);
});

async function doSearch(term) {
  const box = document.getElementById('searchResults');
  if (term.length < 2) { box.innerHTML=''; return; }
  const res  = await fetch(`/productos/buscar?q=${encodeURIComponent(term)}`);
  const list = await res.json();
  box.innerHTML = list.map(p => `
    <div class="search-item" onclick='addItem(${JSON.stringify(p)})'>
      <strong>${p.nombre}</strong>
      <span style="float:right;color:#4f46e5">Costo: $${parseFloat(p.precio_compra||0).toFixed(2)}</span><br>
      <small class="text-muted">SKU: ${p.sku||'—'} | Stock actual: ${p.stock}</small>
    </div>`).join('') || '<div class="search-item text-muted">Sin resultados</div>';
}

function addItem(p) {
  document.getElementById('searchResults').innerHTML = '';
  document.getElementById('searchProd').value = '';
  if (cart[p.id]) { cart[p.id].cantidad++; }
  else { cart[p.id] = {...p, cantidad:1, precio: parseFloat(p.precio_compra||0)}; }
  renderCart();
}

function removeItem(id) { delete cart[id]; renderCart(); }
function updateField(id, field, val) {
  cart[id][field] = parseFloat(val) || (field==='cantidad'?1:0);
  renderCart();
}

function renderCart() {
  const ids = Object.keys(cart);
  const tbody = document.getElementById('compraBody');
  if (!ids.length) {
    tbody.innerHTML = '<tr id="emptyRow"><td colspan="5" class="text-center text-muted" style="padding:1.5rem">Agrega productos</td></tr>';
    document.getElementById('lblTotal').textContent = '$0.00'; return;
  }
  tbody.innerHTML = ids.map(id => {
    const i = cart[id];
    const sub = (i.precio * i.cantidad).toFixed(2);
    return `<tr>
      <td>${i.nombre}</td>
      <td><input type="number" min="1" value="${i.cantidad}" class="form-control form-control-sm" style="width:70px" onchange="updateField(${id},'cantidad',this.value)"></td>
      <td><input type="number" min="0" step="0.01" value="${i.precio.toFixed(2)}" class="form-control form-control-sm" style="width:90px" onchange="updateField(${id},'precio',this.value)"></td>
      <td>$${sub}</td>
      <td><button class="btn btn-sm btn-danger" onclick="removeItem(${id})">✕</button></td>
    </tr>`;
  }).join('');
  const total = ids.reduce((s,id) => s + cart[id].precio*cart[id].cantidad, 0);
  document.getElementById('lblTotal').textContent = '$' + total.toFixed(2);
}

document.getElementById('compraForm').addEventListener('submit', function(e) {
  e.preventDefault();
  if (!Object.keys(cart).length) { alert('Agrega productos.'); return; }
  const items = Object.values(cart).map(i => ({
    producto_id: i.id, nombre: i.nombre,
    cantidad: i.cantidad, precio: i.precio,
    subtotal: i.precio * i.cantidad
  }));
  document.getElementById('itemsInput').value = JSON.stringify(items);
  this.submit();
});
</script>
@endpush
@endsection
