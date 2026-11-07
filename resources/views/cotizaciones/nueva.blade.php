@extends('layouts.main')
@section('title', 'Nueva Cotización — POS')

@section('content')
<style>
/* ============================================================
   PANEL DE COTIZACIÓN PROFESIONAL (ESTILO POS AGREGAR VENTA)
   ============================================================ */
.pos-cot-container {
  padding: 10px 0 30px;
}
.pos-cot-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  flex-wrap: wrap;
  gap: 12px;
  margin-bottom: 18px;
}
.pos-cot-title {
  display: flex;
  align-items: center;
  gap: 12px;
}
.pos-cot-title h1 {
  font-size: 1.6rem;
  font-weight: 800;
  color: #38bdf8;
  margin: 0;
}
.pos-cot-actions {
  display: flex;
  gap: 10px;
}

/* Layout 2 Columnas */
.pos-cot-layout {
  display: grid;
  grid-template-columns: 1fr 380px;
  gap: 20px;
  align-items: start;
}
@media (max-width: 1024px) {
  .pos-cot-layout { grid-template-columns: 1fr; }
}

/* Columna Izquierda: Buscador + Carrito + Cliente */
.pos-card {
  background: #1a1e29;
  border: 1px solid #2c3244;
  border-radius: 10px;
  margin-bottom: 18px;
  overflow: hidden;
  box-shadow: 0 4px 20px rgba(0,0,0,0.25);
}
.pos-card-header {
  background: #202534;
  padding: 12px 18px;
  border-bottom: 1px solid #2c3244;
  display: flex;
  justify-content: space-between;
  align-items: center;
}
.pos-card-header h3 {
  margin: 0;
  font-size: 0.98rem;
  font-weight: 700;
  color: #38bdf8;
  display: flex;
  align-items: center;
  gap: 8px;
}

/* Buscador Rápido */
.search-box-wrapper {
  position: relative;
  padding: 14px 18px;
}
.search-icon {
  position: absolute;
  left: 30px;
  top: 26px;
  color: #64748b;
  font-size: 1.1rem;
}
.search-input-pos {
  width: 100%;
  height: 46px;
  padding-left: 44px;
  padding-right: 14px;
  background: #12141c;
  border: 1px solid #334155;
  border-radius: 8px;
  color: #ffffff;
  font-size: 1rem;
  font-weight: 600;
  outline: none;
  transition: .2s;
}
.search-input-pos:focus {
  border-color: #38bdf8;
  box-shadow: 0 0 0 3px rgba(56,189,248,0.15);
}
.search-dropdown-results {
  position: absolute;
  top: 68px;
  left: 18px;
  right: 18px;
  background: #141822;
  border: 1px solid #334155;
  border-radius: 8px;
  max-height: 280px;
  overflow-y: auto;
  z-index: 50;
  box-shadow: 0 10px 25px rgba(0,0,0,0.4);
}
.search-res-item {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 10px 14px;
  border-bottom: 1px solid #1e2536;
  cursor: pointer;
  transition: .15s;
}
.search-res-item:hover {
  background: #20273a;
}
.search-res-name {
  font-weight: 700;
  color: #f1f5f9;
}
.search-res-sub {
  font-size: 0.78rem;
  color: #94a3b8;
}
.search-res-price {
  font-weight: 800;
  color: #10b981;
  font-size: 1rem;
}

/* Tabla de Productos */
.pos-table {
  width: 100%;
  border-collapse: collapse;
}
.pos-table th {
  background: #151822;
  color: #94a3b8;
  font-size: 0.8rem;
  text-transform: uppercase;
  font-weight: 700;
  padding: 10px 14px;
  border-bottom: 1px solid #282d3c;
  text-align: left;
}
.pos-table td {
  padding: 10px 14px;
  border-bottom: 1px solid #282d3c;
  color: #e2e8f0;
  vertical-align: middle;
}
.qty-btn {
  width: 28px;
  height: 28px;
  border-radius: 4px;
  background: #242b3d;
  border: 1px solid #334155;
  color: #ffffff;
  font-weight: bold;
  cursor: pointer;
  display: inline-flex;
  align-items: center;
  justify-content: center;
  transition: .15s;
}
.qty-btn:hover {
  background: #38bdf8;
  color: #0f172a;
}
.qty-input {
  width: 48px;
  height: 28px;
  background: #12141c;
  border: 1px solid #334155;
  border-radius: 4px;
  color: #ffffff;
  text-align: center;
  font-weight: 700;
  margin: 0 4px;
}

/* Columna Derecha: Totales y Confirmación */
.summary-stat-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 10px;
  margin-bottom: 16px;
}
.stat-box-pos {
  background: #151822;
  border: 1px solid #282d3c;
  border-radius: 8px;
  padding: 12px;
  text-align: center;
}
.stat-box-label {
  font-size: 0.75rem;
  color: #94a3b8;
  text-transform: uppercase;
  font-weight: 700;
}
.stat-box-val {
  font-size: 1.35rem;
  font-weight: 900;
  color: #38bdf8;
  margin-top: 4px;
}
.summary-line {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 8px 0;
  border-bottom: 1px dashed #2c3244;
  font-size: 0.92rem;
}
.total-highlight-row {
  display: flex;
  justify-content: space-between;
  align-items: center;
  padding: 14px 0 6px;
  border-top: 2px solid #38bdf8;
  margin-top: 8px;
}
.total-highlight-label {
  font-size: 1.15rem;
  font-weight: 800;
  color: #ffffff;
}
.total-highlight-val {
  font-size: 1.6rem;
  font-weight: 900;
  color: #10b981;
}
.btn-emitir-cot {
  width: 100%;
  height: 48px;
  background: #10b981;
  color: #ffffff;
  border: none;
  border-radius: 8px;
  font-size: 1rem;
  font-weight: 800;
  cursor: pointer;
  display: flex;
  align-items: center;
  justify-content: center;
  gap: 8px;
  margin-top: 18px;
  transition: .2s;
}
.btn-emitir-cot:hover {
  background: #059669;
  transform: translateY(-1px);
}
</style>

<div class="pos-cot-container">
  {{-- Header --}}
  <div class="pos-cot-header">
    <div class="pos-cot-title">
      <span style="font-size:1.8rem; color:#38bdf8"><i class="fa-solid fa-file-invoice-dollar"></i></span>
      <div>
        <h1>Nueva Cotización</h1>
        <p style="color:#94a3b8; margin:2px 0 0; font-size:0.85rem">Panel interactivo de cotización y presupuesto de ventas</p>
      </div>
    </div>
    <div class="pos-cot-actions">
      <button type="button" class="btn btn-secondary" onclick="limpiarCarritoCot()"><i class="fa-solid fa-trash-can"></i> Limpiar</button>
      <a href="{{ route('cotizaciones.index') }}" class="btn btn-light"><i class="fa-solid fa-arrow-left"></i> Volver al Listado</a>
    </div>
  </div>

  <form id="formCotizacion" action="{{ route('cotizaciones.store') }}" method="POST">
    @csrf
    <input type="hidden" name="items" id="itemsInput">
    <input type="hidden" name="subtotal" id="subtotalInput">
    <input type="hidden" name="descuento" id="descuentoInput" value="0">
    <input type="hidden" name="total" id="totalInput">

    <div class="pos-cot-layout">
      {{-- COLUMNA IZQUIERDA --}}
      <div>
        {{-- Card Buscador --}}
        <div class="pos-card">
          <div class="search-box-wrapper">
            <span class="search-icon"><i class="fa-solid fa-magnifying-glass"></i></span>
            <input type="search" id="inputBuscarProd" class="search-input-pos"
                   placeholder="Buscar producto por nombre, código de barras o SKU (Escriba al menos 2 letras)..."
                   autocomplete="off" autofocus>
            <div id="dropdownResultados" class="search-dropdown-results" style="display:none"></div>
          </div>
        </div>

        {{-- Card Carrito / Detalle de Cotización --}}
        <div class="pos-card">
          <div class="pos-card-header">
            <h3><i class="fa-solid fa-cart-shopping"></i> Productos en Cotización</h3>
            <span id="badgeCantItems" class="badge" style="background:#242b3d; color:#38bdf8; font-weight:700">0 productos</span>
          </div>
          <div style="overflow-x:auto">
            <table class="pos-table" id="tablaCotizacion">
              <thead>
                <tr>
                  <th style="width:40px">#</th>
                  <th>Descripción del Producto</th>
                  <th style="width:130px; text-align:center">Cantidad</th>
                  <th style="width:110px; text-align:right">Precio</th>
                  <th style="width:120px; text-align:right">Subtotal</th>
                  <th style="width:40px; text-align:center"></th>
                </tr>
              </thead>
              <tbody id="bodyCotizacion">
                <tr id="emptyRow">
                  <td colspan="6" style="text-align:center; padding:3rem 1rem; color:#64748b">
                    <i class="fa-solid fa-cart-plus" style="font-size:2.5rem; opacity:0.3; display:block; margin-bottom:10px"></i>
                    No hay productos agregados a la cotización.<br>
                    <small>Utilice el buscador superior para agregar ítems rápidamente.</small>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        {{-- Card Datos del Cliente y Condiciones --}}
        <div class="pos-card">
          <div class="pos-card-header">
            <h3><i class="fa-solid fa-user-tag"></i> Datos del Cliente y Condiciones</h3>
          </div>
          <div style="padding:16px 18px; display:grid; grid-template-columns:1fr 1fr; gap:16px">
            <div>
              <label style="display:block; font-size:0.85rem; font-weight:700; color:#e2e8f0; margin-bottom:6px">
                Cliente Receptor:
              </label>
              <select name="cliente_id" id="clienteSelect" class="form-control" style="width:100%; height:40px; background:#12141c; color:#ffffff; border-color:#2c3244">
                <option value="">— Cliente General (Público) —</option>
                @foreach($clientes as $cl)
                  <option value="{{ $cl->id }}">{{ $cl->nombre }} ({{ $cl->documento ?? 'Sin doc' }})</option>
                @endforeach
              </select>
            </div>
            <div>
              <label style="display:block; font-size:0.85rem; font-weight:700; color:#e2e8f0; margin-bottom:6px">
                Válida Hasta:
              </label>
              <input type="date" name="valida_hasta" class="form-control"
                     value="{{ now()->addDays(15)->format('Y-m-d') }}"
                     style="width:100%; height:40px; background:#12141c; color:#ffffff; border-color:#2c3244">
            </div>
            <div style="grid-column:1 / -1">
              <label style="display:block; font-size:0.85rem; font-weight:700; color:#e2e8f0; margin-bottom:6px">
                Notas y Condiciones del Presupuesto:
              </label>
              <textarea name="notas" rows="2" class="form-control" placeholder="Ej. Precios válidos por 15 días. Entrega en sucursal. Incluye IVA..."
                        style="width:100%; background:#12141c; color:#ffffff; border-color:#2c3244; resize:none"></textarea>
            </div>
          </div>
        </div>
      </div>

      {{-- COLUMNA DERECHA: RESUMEN FINANCIERO --}}
      <div>
        <div class="pos-card" style="position:sticky; top:20px">
          <div class="pos-card-header">
            <h3><i class="fa-solid fa-calculator"></i> Resumen de Cotización</h3>
          </div>
          <div style="padding:18px">
            {{-- Estadísticas Rápidas --}}
            <div class="summary-stat-grid">
              <div class="stat-box-pos">
                <div class="stat-box-label">Productos</div>
                <div class="stat-box-val" id="statTotalItems">0</div>
              </div>
              <div class="stat-box-pos">
                <div class="stat-box-label">Unidades</div>
                <div class="stat-box-val" id="statTotalUnidades" style="color:#10b981">0</div>
              </div>
            </div>

            {{-- Desglose Financiero con IVA 13% Integrado --}}
            <div class="summary-line">
              <span style="color:#94a3b8">Base Gravada (sin IVA):</span>
              <strong id="lblBaseGravada">$0.00</strong>
            </div>
            <div class="summary-line">
              <span style="color:#94a3b8">IVA (13% integrado):</span>
              <strong id="lblIvaCalculado" style="color:#38bdf8">$0.00</strong>
            </div>
            <div class="summary-line">
              <span style="color:#94a3b8">Subtotal Acumulado:</span>
              <strong id="lblSubtotalAcumulado">$0.00</strong>
            </div>
            <div class="summary-line" style="align-items:center">
              <span style="color:#94a3b8">Descuento Global ($):</span>
              <input type="number" step="0.01" min="0" value="0.00" id="inputDescuentoGlobal"
                     class="form-control" style="width:110px; height:32px; text-align:right; font-weight:700; background:#12141c; color:#f87171; border-color:#2c3244">
            </div>

            {{-- Total Final --}}
            <div class="total-highlight-row">
              <span class="total-highlight-label">TOTAL COTIZADO:</span>
              <span class="total-highlight-val" id="lblTotalFinal">$0.00</span>
            </div>

            {{-- Botón Guardar --}}
            <button type="submit" id="btnEmitirCot" class="btn-emitir-cot">
              <i class="fa-solid fa-file-circle-check"></i> Emitir y Guardar Cotización
            </button>
          </div>
        </div>
      </div>
    </div>
  </form>
</div>

@push('scripts')
<script>
const carritoCot = {};
let searchTimer = null;

// Buscador Ajax
const inputBuscar = document.getElementById('inputBuscarProd');
const dropdownRes = document.getElementById('dropdownResultados');

inputBuscar.addEventListener('input', function() {
  clearTimeout(searchTimer);
  const term = this.value.trim();
  if (term.length < 2) {
    dropdownRes.innerHTML = '';
    dropdownRes.style.display = 'none';
    return;
  }
  searchTimer = setTimeout(() => buscarProductosCot(term), 250);
});

async function buscarProductosCot(term) {
  try {
    const res = await fetch(`{{ route('productos.buscar') }}?q=${encodeURIComponent(term)}`);
    const items = await res.json();
    if (!items || !items.length) {
      dropdownRes.innerHTML = '<div style="padding:12px; color:#94a3b8; text-align:center">No se encontraron productos coincidentes.</div>';
      dropdownRes.style.display = 'block';
      return;
    }
    dropdownRes.innerHTML = items.map(p => `
      <div class="search-res-item" onclick='agregarAlCarrito(${JSON.stringify(p)})'>
        <div>
          <div class="search-res-name">${p.nombre}</div>
          <div class="search-res-sub">SKU: ${p.sku || 'S/SKU'} | Código: ${p.codigo_barras || 'S/C'} | Stock Disp: ${p.stock}</div>
        </div>
        <div class="search-res-price">$${parseFloat(p.precio_venta).toFixed(2)}</div>
      </div>
    `).join('');
    dropdownRes.style.display = 'block';
  } catch (e) {
    console.error(e);
  }
}

// Cerrar dropdown al hacer click fuera
document.addEventListener('click', function(e) {
  if (!inputBuscar.contains(e.target) && !dropdownRes.contains(e.target)) {
    dropdownRes.style.display = 'none';
  }
});

function agregarAlCarrito(prod) {
  dropdownRes.innerHTML = '';
  dropdownRes.style.display = 'none';
  inputBuscar.value = '';
  inputBuscar.focus();

  if (carritoCot[prod.id]) {
    carritoCot[prod.id].cantidad++;
  } else {
    carritoCot[prod.id] = {
      id: prod.id,
      nombre: prod.nombre,
      precio: parseFloat(prod.precio_venta),
      cantidad: 1,
      sku: prod.sku || '—'
    };
  }
  renderizarTablaCot();
}

function cambiarCantidadCot(id, delta) {
  if (!carritoCot[id]) return;
  carritoCot[id].cantidad += delta;
  if (carritoCot[id].cantidad <= 0) {
    delete carritoCot[id];
  }
  renderizarTablaCot();
}

function setCantidadDirecta(id, val) {
  if (!carritoCot[id]) return;
  const parsed = parseInt(val) || 1;
  carritoCot[id].cantidad = Math.max(1, parsed);
  renderizarTablaCot();
}

function eliminarItemCot(id) {
  delete carritoCot[id];
  renderizarTablaCot();
}

function limpiarCarritoCot() {
  if (confirm('¿Desea vaciar todos los productos de la cotización?')) {
    for (let k in carritoCot) delete carritoCot[k];
    renderizarTablaCot();
  }
}

function renderizarTablaCot() {
  const ids = Object.keys(carritoCot);
  const tbody = document.getElementById('bodyCotizacion');

  if (!ids.length) {
    tbody.innerHTML = `
      <tr id="emptyRow">
        <td colspan="6" style="text-align:center; padding:3rem 1rem; color:#64748b">
          <i class="fa-solid fa-cart-plus" style="font-size:2.5rem; opacity:0.3; display:block; margin-bottom:10px"></i>
          No hay productos agregados a la cotización.<br>
          <small>Utilice el buscador superior para agregar ítems rápidamente.</small>
        </td>
      </tr>
    `;
    actualizarTotalesCot(0);
    return;
  }

  let html = '';
  let idx = 1;
  let totalSub = 0;
  let totalUds = 0;

  ids.forEach(id => {
    const item = carritoCot[id];
    const sub = item.cantidad * item.precio;
    totalSub += sub;
    totalUds += item.cantidad;

    html += `
      <tr>
        <td style="color:#64748b; font-weight:700">${idx++}</td>
        <td>
          <strong style="color:#f1f5f9">${item.nombre}</strong>
          <span style="display:block; font-size:0.75rem; color:#64748b">SKU: ${item.sku}</span>
        </td>
        <td style="text-align:center; white-space:nowrap">
          <button type="button" class="qty-btn" onclick="cambiarCantidadCot(${id}, -1)">-</button>
          <input type="number" class="qty-input" value="${item.cantidad}" min="1" onchange="setCantidadDirecta(${id}, this.value)">
          <button type="button" class="qty-btn" onclick="cambiarCantidadCot(${id}, 1)">+</button>
        </td>
        <td style="text-align:right; font-weight:600; color:#cbd5e1">$${item.precio.toFixed(2)}</td>
        <td style="text-align:right; font-weight:800; color:#10b981">$${sub.toFixed(2)}</td>
        <td style="text-align:center">
          <button type="button" onclick="eliminarItemCot(${id})" style="background:none; border:none; color:#ef4444; cursor:pointer" title="Eliminar ítem">
            <i class="fa-solid fa-trash-can"></i>
          </button>
        </td>
      </tr>
    `;
  });

  tbody.innerHTML = html;
  document.getElementById('badgeCantItems').textContent = `${ids.length} productos`;
  document.getElementById('statTotalItems').textContent = ids.length;
  document.getElementById('statTotalUnidades').textContent = totalUds;

  actualizarTotalesCot(totalSub);
}

function actualizarTotalesCot(subtotal) {
  const desc = Math.max(0, parseFloat(document.getElementById('inputDescuentoGlobal').value) || 0);
  const total = Math.max(0, subtotal - desc);

  // IVA 13% integrado en el precio (El Salvador):
  const gravado = total > 0 ? (total / 1.13) : 0;
  const iva = total - gravado;

  document.getElementById('lblBaseGravada').textContent = '$' + gravado.toFixed(2);
  document.getElementById('lblIvaCalculado').textContent = '$' + iva.toFixed(2);
  document.getElementById('lblSubtotalAcumulado').textContent = '$' + subtotal.toFixed(2);
  document.getElementById('lblTotalFinal').textContent = '$' + total.toFixed(2);

  // Sincronizar inputs ocultos
  document.getElementById('subtotalInput').value = subtotal.toFixed(2);
  document.getElementById('descuentoInput').value = desc.toFixed(2);
  document.getElementById('totalInput').value = total.toFixed(2);
}

document.getElementById('inputDescuentoGlobal').addEventListener('input', function() {
  const ids = Object.keys(carritoCot);
  const subtotal = ids.reduce((s, id) => s + (carritoCot[id].cantidad * carritoCot[id].precio), 0);
  actualizarTotalesCot(subtotal);
});

// Envío del Formulario
document.getElementById('formCotizacion').addEventListener('submit', async function(e) {
  e.preventDefault();
  const ids = Object.keys(carritoCot);
  if (!ids.length) {
    alert('Por favor agregue al menos un producto a la cotización.');
    return;
  }

  const btn = document.getElementById('btnEmitirCot');
  btn.disabled = true;
  btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Guardando Cotización...';

  const items = ids.map(id => ({
    producto_id: carritoCot[id].id,
    nombre: carritoCot[id].nombre,
    cantidad: carritoCot[id].cantidad,
    precio: carritoCot[id].precio,
    subtotal: carritoCot[id].cantidad * carritoCot[id].precio
  }));

  document.getElementById('itemsInput').value = JSON.stringify(items);

  try {
    const formData = new FormData(this);
    const res = await fetch(this.action, {
      method: 'POST',
      body: formData,
      headers: {
        'Accept': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
      }
    });

    const data = await res.json();
    if (data.ok) {
      window.location.href = `/cotizaciones/${data.id}`;
    } else {
      alert(data.error || 'Ocurrió un error al registrar la cotización.');
      btn.disabled = false;
      btn.innerHTML = '<i class="fa-solid fa-file-circle-check"></i> Emitir y Guardar Cotización';
    }
  } catch (err) {
    console.error(err);
    // Fallback: regular form submit
    this.submit();
  }
});
</script>
@endpush
@endsection
