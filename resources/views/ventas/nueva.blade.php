@extends('layouts.main')
@section('title','Nueva Venta')
@section('content')
<div class="page-header">
  <h1>🛍️ Nueva Venta</h1>
  <a href="{{ route('ventas.index') }}" class="btn btn-light">← Historial</a>
</div>

@if(!$cajaAbierta)
<div class="alert alert-warning">
  ⚠️ No hay caja abierta. <a href="{{ route('caja.index') }}" class="btn btn-sm btn-primary" style="margin-left:8px">Abrir Caja</a>
</div>
@endif

<div class="grid-pos">
  <div>
    <div class="card">
      <div class="card-header"><h3>🔍 Buscar Producto</h3></div>
      <div class="card-body">
        <input type="search" id="searchProducto" class="form-control"
               placeholder="Nombre, SKU o código de barras..." autocomplete="off" autofocus>
        <div id="searchResults" class="search-results"></div>
      </div>
    </div>
  </div>

  <div>
    <div class="card">
      <div class="card-header"><h3>🛒 Carrito</h3></div>
      <div class="card-body p-0">
        <table class="table">
          <thead><tr><th>Producto</th><th>Cant.</th><th>Precio</th><th>Subt.</th><th></th></tr></thead>
          <tbody id="cartBody">
            <tr id="emptyCart"><td colspan="5" class="text-center text-muted" style="padding:2rem">Carrito vacío</td></tr>
          </tbody>
        </table>
      </div>
      <div class="card-footer">
        <div class="cart-totals">
          <div class="total-row"><span>Subtotal:</span><span id="lblSubtotal">$0.00</span></div>
          <div class="total-row">
            <label style="margin:0">Descuento ($):</label>
            <input type="number" id="descuento" value="0" min="0" step="0.01" class="form-control form-control-sm" style="width:100px">
          </div>
          <div class="total-row total-final"><span>TOTAL:</span><span id="lblTotal">$0.00</span></div>
        </div>

        <form id="ventaForm" method="POST" action="{{ route('ventas.guardar') }}">
          @csrf
          <input type="hidden" name="items"     id="itemsInput">
          <input type="hidden" name="subtotal"  id="subtotalInput">
          <input type="hidden" name="descuento" id="descuentoInput">
          <input type="hidden" name="total"     id="totalInput">

          <div class="form-row mt-3">
            <div class="form-group">
              <label>Cliente</label>
              <select name="cliente_id" id="clienteSelect" class="form-control">
                <option value="">— Cliente general —</option>
                @foreach($clientes as $c)
                  <option value="{{ $c->id }}" data-tipo="{{ $c->tipo }}">{{ $c->nombre }} {{ $c->tipo==='credito'?'(Crédito)':'' }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label>Tipo de Venta</label>
              <select name="tipo_venta" id="tipoVenta" class="form-control" onchange="togglePago()">
                <option value="contado">💵 Contado</option>
                <option value="credito">💳 Crédito</option>
              </select>
            </div>
          </div>

          <div class="form-group" id="metodoPagoGroup">
            <label>Método de Pago</label>
            <select name="metodo_pago" id="metodoPago" class="form-control" onchange="toggleEfectivo()">
              <option value="efectivo">💵 Efectivo</option>
              <option value="tarjeta">💳 Tarjeta</option>
              <option value="transferencia">🏦 Transferencia</option>
            </select>
          </div>

          {{-- Calculadora de cambio --}}
          <div class="form-group" id="efectivoGroup">
            <label>Efectivo Recibido ($)</label>
            <input type="number" id="efectivoRecibido" step="0.01" min="0" class="form-control" placeholder="0.00" oninput="calcularCambio()">
          </div>
          <div class="cambio-box" id="cambioBox" style="display:none">
            <div class="cambio-label">Cambio a Devolver</div>
            <div class="cambio-value" id="cambioValue">$0.00</div>
          </div>

          <div class="form-group">
            <label>Notas</label>
            <input type="text" name="notas" class="form-control" placeholder="Opcional...">
          </div>
          <button type="submit" id="btnVender" class="btn btn-primary btn-block btn-lg">✅ Completar Venta</button>
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
document.getElementById('searchProducto').addEventListener('input', function () {
  clearTimeout(timer);
  timer = setTimeout(() => doSearch(this.value.trim()), 300);
});

async function doSearch(term) {
  const box = document.getElementById('searchResults');
  if (term.length < 2) { box.innerHTML = ''; return; }
  const res  = await fetch(`{{ route('productos.buscar') }}?q=${encodeURIComponent(term)}`);
  const list = await res.json();
  if (!list.length) { box.innerHTML = '<div class="search-item text-muted">Sin resultados</div>'; return; }
  box.innerHTML = list.map(p => `
    <div class="search-item" onclick='addToCart(${JSON.stringify(p)})'>
      <strong>${p.nombre}</strong>
      <span style="float:right;font-weight:700;color:#4e73df">$${parseFloat(p.precio_venta).toFixed(2)}</span><br>
      <small class="text-muted">SKU: ${p.sku||'—'} | Stock: ${p.stock}</small>
    </div>`).join('');
}

function addToCart(p) {
  document.getElementById('searchResults').innerHTML = '';
  document.getElementById('searchProducto').value   = '';
  document.getElementById('searchProducto').focus();
  if (p.stock <= 0) { alert('Sin stock disponible.'); return; }
  if (cart[p.id]) {
    if (cart[p.id].cantidad >= p.stock) { alert('Stock insuficiente. Máx: ' + p.stock); return; }
    cart[p.id].cantidad++;
  } else {
    cart[p.id] = { ...p, cantidad: 1 };
  }
  renderCart();
}

function removeFromCart(id) { delete cart[id]; renderCart(); }
function updateQty(id, val) {
  cart[id].cantidad = Math.min(Math.max(1, parseInt(val)||1), cart[id].stock);
  renderCart();
}

function renderCart() {
  const ids = Object.keys(cart);
  const tbody = document.getElementById('cartBody');
  if (!ids.length) {
    tbody.innerHTML = '<tr id="emptyCart"><td colspan="5" class="text-center text-muted" style="padding:2rem">Carrito vacío</td></tr>';
    recalc(0); return;
  }
  tbody.innerHTML = ids.map(id => {
    const i = cart[id];
    return `<tr>
      <td>${i.nombre}</td>
      <td><input type="number" value="${i.cantidad}" min="1" max="${i.stock}" style="width:60px" class="form-control form-control-sm" onchange="updateQty(${id},this.value)"></td>
      <td>$${parseFloat(i.precio_venta).toFixed(2)}</td>
      <td>$${(i.precio_venta*i.cantidad).toFixed(2)}</td>
      <td><button class="btn btn-sm btn-danger" onclick="removeFromCart(${id})">✕</button></td>
    </tr>`;
  }).join('');
  recalc(ids.reduce((s,id) => s + cart[id].precio_venta * cart[id].cantidad, 0));
}

function recalc(sub) {
  const desc  = Math.max(0, parseFloat(document.getElementById('descuento').value)||0);
  document.getElementById('lblSubtotal').textContent = '$' + sub.toFixed(2);
  document.getElementById('lblTotal').textContent    = '$' + Math.max(0, sub-desc).toFixed(2);
  calcularCambio();
}
document.getElementById('descuento').addEventListener('input', () => {
  recalc(Object.values(cart).reduce((s,i) => s + i.precio_venta*i.cantidad, 0));
});

function calcularCambio(){
  const total = parseFloat(document.getElementById('lblTotal').textContent.replace('$','')) || 0;
  const recibido = parseFloat(document.getElementById('efectivoRecibido').value) || 0;
  const cambio = recibido - total;
  const box = document.getElementById('cambioBox');
  if(recibido > 0){
    box.style.display='block';
    document.getElementById('cambioValue').textContent = '$'+Math.max(0,cambio).toFixed(2);
    document.getElementById('cambioValue').style.color = cambio < 0 ? '#e74a3b':'#1cc88a';
  } else {
    box.style.display='none';
  }
}

function togglePago(){
  const tipo = document.getElementById('tipoVenta').value;
  document.getElementById('metodoPagoGroup').style.display = tipo === 'credito' ? 'none' : 'block';
  document.getElementById('efectivoGroup').style.display   = tipo === 'credito' ? 'none' : 'block';
  document.getElementById('cambioBox').style.display = 'none';
  if(tipo === 'credito'){
    const sel = document.getElementById('clienteSelect');
    if(!sel.value){ alert('Selecciona un cliente para venta a crédito.'); document.getElementById('tipoVenta').value='contado'; togglePago(); }
  }
}
function toggleEfectivo(){
  const metodo = document.getElementById('metodoPago').value;
  document.getElementById('efectivoGroup').style.display = metodo === 'efectivo' ? 'block' : 'none';
  if(metodo !== 'efectivo') document.getElementById('cambioBox').style.display='none';
}

document.getElementById('ventaForm').addEventListener('submit', async function(e) {
  e.preventDefault();
  if (!Object.keys(cart).length) { alert('Agrega productos al carrito.'); return; }

  const tipoVenta = document.getElementById('tipoVenta').value;
  if(tipoVenta === 'credito' && !document.getElementById('clienteSelect').value){
    alert('Debes seleccionar un cliente para venta a crédito.'); return;
  }

  const btn = document.getElementById('btnVender');
  btn.disabled = true; btn.textContent = 'Procesando...';

  const items = Object.values(cart).map(i => ({
    producto_id: i.id, nombre: i.nombre, cantidad: i.cantidad,
    precio: i.precio_venta, subtotal: i.precio_venta * i.cantidad
  }));
  const sub  = items.reduce((s,i) => s+i.subtotal, 0);
  const desc = Math.max(0, parseFloat(document.getElementById('descuento').value)||0);

  document.getElementById('itemsInput').value    = JSON.stringify(items);
  document.getElementById('subtotalInput').value = sub.toFixed(2);
  document.getElementById('descuentoInput').value= desc.toFixed(2);
  document.getElementById('totalInput').value    = Math.max(0, sub-desc).toFixed(2);

  try {
    const r = await fetch(this.action, { method:'POST', body: new FormData(this), headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'} });
    const d = await r.json();
    if (d.ok) window.location.href = `/ventas/${d.id}`;
    else { alert(d.error||'Error al procesar.'); btn.disabled=false; btn.textContent='✅ Completar Venta'; }
  } catch { alert('Error de conexión.'); btn.disabled=false; btn.textContent='✅ Completar Venta'; }
});
</script>
@endpush
@endsection
