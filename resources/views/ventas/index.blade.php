@extends('layouts.main')
@section('title','Historial de Ventas')
@section('content')

<div class="page-header" style="margin-bottom:20px">
  <div>
    <h1 style="color:#38bdf8; font-size:1.75rem; font-weight:800; margin:0">
      <i class="fa-solid fa-clipboard-list"></i> Administrar Ventas
    </h1>
    <p class="page-sub" style="color:#94a3b8; margin-top:4px">Historial de transacciones de ventas y comprobantes emitidos</p>
  </div>
  <a href="{{ route('ventas.nueva') }}" class="btn btn-primary" style="display:flex; align-items:center; gap:6px; font-weight:800">
    <i class="fa-solid fa-plus"></i> Nueva Venta
  </a>
</div>

<div class="card" style="border-radius:10px;">
  <div class="card-body p-0">
    {{-- Barra de Filtros y Selector de Registros --}}
    <div class="table-toolbar" style="padding:14px 18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px">
      <div style="display:flex; align-items:center; gap:10px; flex:1; max-width:420px">
        <form method="GET" action="{{ route('ventas.index') }}" style="display:flex; gap:8px; width:100%">
          <input type="search" name="q" value="{{ request('q') }}" placeholder="Buscar por código, cliente..." class="form-control" style="flex:1">
          <input type="hidden" name="per_page" value="{{ $perPage ?? 25 }}">
          <button type="submit" class="btn btn-secondary"><i class="fa-solid fa-magnifying-glass"></i></button>
        </form>
      </div>

      <div style="display:flex; align-items:center; gap:8px">
        <span style="font-size:0.85rem; color:#94a3b8">Mostrar:</span>
        <select class="form-control" style="width:auto; padding:6px 12px; font-weight:700" onchange="window.location='{{ route('ventas.index') }}?q={{ urlencode(request('q','')) }}&per_page=' + this.value">
          <option value="10" {{ ($perPage ?? 25) == 10 ? 'selected' : '' }}>10 registros</option>
          <option value="25" {{ ($perPage ?? 25) == 25 ? 'selected' : '' }}>25 registros</option>
          <option value="50" {{ ($perPage ?? 25) == 50 ? 'selected' : '' }}>50 registros</option>
          <option value="100" {{ ($perPage ?? 25) == 100 ? 'selected' : '' }}>100 registros</option>
        </select>
      </div>
    </div>

    {{-- Tabla de Ventas --}}
    <div style="overflow-x:auto">
      <table class="table" id="ventasTable" style="margin:0">
        <thead>
          <tr>
            <th>CÓDIGO</th>
            <th>FECHA / HORA</th>
            <th>CLIENTE</th>
            <th>VENDEDOR</th>
            <th>TOTAL</th>
            <th>ITEMS / CANT.</th>
            <th>ESTADO</th>
            <th style="text-align:right">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          @forelse($ventas as $v)
          <tr>
            <td>
              <button type="button" onclick='mostrarDetalleVenta(@json($v->load("items", "cliente", "usuario")))'
                      style="background:none; border:none; padding:0; font-family:monospace; font-weight:700; color:#38bdf8; cursor:pointer; text-decoration:underline">
                {{ $v->codigo }}
              </button>
            </td>
            <td style="color:#94a3b8">{{ $v->created_at->format('d/m/Y H:i') }}</td>
            <td><strong>{{ $v->cliente->nombre ?? 'Clientes Varios' }}</strong></td>
            <td><span style="color:#94a3b8">{{ $v->usuario->nombre ?? 'Sistema' }}</span></td>
            <td><strong style="color:#10b981; font-size:0.95rem">${{ number_format($v->total, 2) }}</strong></td>
            <td>
              <span class="badge badge-info" style="font-size:0.75rem">
                {{ $v->items->count() }} prod. ({{ $v->items->sum('cantidad') }} uds)
              </span>
            </td>
            <td>
              <span class="badge badge-{{ $v->estado === 'completada' ? 'success' : ($v->estado === 'anulada' ? 'danger' : 'warning') }}">
                {{ ucfirst($v->estado) }}
              </span>
            </td>
            <td style="text-align:right; white-space:nowrap">
              <button type="button" onclick='mostrarDetalleVenta(@json($v->load("items", "cliente", "usuario")))' class="btn btn-sm btn-secondary" title="Ver detalle en pantalla">
                <i class="fa-solid fa-eye"></i> Ver
              </button>
              <a href="{{ route('ventas.pdf', $v->id) }}" target="_blank" class="btn btn-sm btn-primary" title="Generar PDF (DTE)">
                <i class="fa-solid fa-file-lines"></i> PDF
              </a>
              <a href="{{ route('impresoras.ticket', $v->id) }}" target="_blank" class="btn btn-sm btn-light" title="Imprimir Ticket">
                <i class="fa-solid fa-print"></i>
              </a>
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" class="text-center text-muted" style="padding:2.5rem">
              <i class="fa-solid fa-receipt" style="font-size:2rem; opacity:0.3; margin-bottom:8px; display:block"></i>
              No hay ventas registradas con los filtros seleccionados.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    {{-- Resumen de Paginación Exacto a la Imagen 3 --}}
    <div class="table-footer" style="padding:14px 18px; border-top:1px solid #282d3c; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:10px">
      <div style="font-size:0.85rem; color:#94a3b8">
        Mostrando registros del <strong>{{ $ventas->firstItem() ?? 0 }}</strong> al <strong>{{ $ventas->lastItem() ?? 0 }}</strong> de un total de <strong>{{ number_format($ventas->total()) }}</strong> registros
      </div>
      <div>
        {{ $ventas->links() }}
      </div>
    </div>
  </div>
</div>

{{-- MODAL EN LA MISMA PANTALLA: VER DETALLE DE VENTA --}}
<div id="modalVerVenta" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.75); z-index:9999; align-items:center; justify-content:center; padding:16px;">
  <div style="background:#1a1e29; border:1px solid #2c3244; border-radius:12px; width:100%; max-width:760px; max-height:90vh; display:flex; flex-direction:column; box-shadow:0 10px 40px rgba(0,0,0,0.5); overflow:hidden">
    {{-- Header Modal --}}
    <div style="padding:16px 20px; border-bottom:1px solid #2c3244; display:flex; justify-content:space-between; align-items:center; background:#151822">
      <div style="display:flex; align-items:center; gap:10px">
        <span style="font-size:1.3rem; color:#38bdf8"><i class="fa-solid fa-receipt"></i></span>
        <div>
          <h3 id="modalVentaCodigo" style="margin:0; font-size:1.15rem; font-weight:800; color:#38bdf8">VTA-XXXX</h3>
          <span id="modalVentaFecha" style="font-size:0.8rem; color:#94a3b8">Fecha</span>
        </div>
      </div>
      <button onclick="cerrarModalVerVenta()" style="background:none; border:none; color:#94a3b8; font-size:1.3rem; cursor:pointer">
        <i class="fa-solid fa-xmark"></i>
      </button>
    </div>

    {{-- Body Modal --}}
    <div style="padding:20px; overflow-y:auto; flex:1">
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:18px; font-size:0.88rem; background:#151822; padding:14px; border-radius:8px">
        <div><strong>Cliente:</strong> <span id="modalVentaCliente" style="color:#38bdf8">General</span></div>
        <div><strong>Vendedor / Cajero:</strong> <span id="modalVentaUsuario">Admin</span></div>
        <div><strong>Condición de Pago:</strong> <span id="modalVentaTipo">Contado (Efectivo)</span></div>
        <div><strong>Estado:</strong> <span id="modalVentaEstado" class="badge badge-success">Completada</span></div>
      </div>

      <h4 style="font-size:0.95rem; font-weight:800; color:#38bdf8; margin:0 0 10px">
        <i class="fa-solid fa-cart-shopping" style="margin-right:6px"></i> Productos Facturados
      </h4>
      <table class="table" style="margin-bottom:18px">
        <thead>
          <tr>
            <th>Producto</th>
            <th style="text-align:center">Cant.</th>
            <th style="text-align:right">Precio Unit.</th>
            <th style="text-align:right">Subtotal</th>
          </tr>
        </thead>
        <tbody id="modalVentaItems">
          {{-- Items dinámicos --}}
        </tbody>
      </table>

      {{-- Totales --}}
      <div style="display:flex; justify-content:flex-end">
        <div style="width:280px; font-size:0.9rem; line-height:1.8">
          <div style="display:flex; justify-content:space-between">
            <span style="color:#94a3b8">Base Gravada:</span>
            <strong id="modalVentaSubtotal">$0.00</strong>
          </div>
          <div style="display:flex; justify-content:space-between">
            <span style="color:#94a3b8">IVA (13% incluido):</span>
            <strong id="modalVentaIva" style="color:#38bdf8">$0.00</strong>
          </div>
          <div style="display:flex; justify-content:space-between; border-top:1px solid #2c3244; padding-top:6px; margin-top:4px">
            <span style="font-size:1.1rem; font-weight:800">TOTAL:</span>
            <span id="modalVentaTotal" style="font-size:1.25rem; font-weight:900; color:#10b981">$0.00</span>
          </div>
        </div>
      </div>
    </div>

    {{-- Footer Modal --}}
    <div style="padding:14px 20px; border-top:1px solid #2c3244; display:flex; justify-content:space-between; align-items:center; background:#151822">
      <div style="display:flex; gap:8px">
        <a id="modalVentaBtnPdf" href="#" target="_blank" class="btn btn-primary btn-sm">
          <i class="fa-solid fa-file-lines"></i> Ver PDF DTE
        </a>
        <a id="modalVentaBtnTicket" href="#" target="_blank" class="btn btn-light btn-sm">
          <i class="fa-solid fa-print"></i> Ticket
        </a>
      </div>
      <button onclick="cerrarModalVerVenta()" class="btn btn-secondary btn-sm">Cerrar</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
function mostrarDetalleVenta(v) {
  document.getElementById('modalVentaCodigo').textContent = v.codigo;
  document.getElementById('modalVentaFecha').textContent = new Date(v.created_at).toLocaleString('es-SV');
  document.getElementById('modalVentaCliente').textContent = (v.cliente && v.cliente.nombre) ? v.cliente.nombre : 'Clientes Varios';
  document.getElementById('modalVentaUsuario').textContent = (v.usuario && v.usuario.nombre) ? v.usuario.nombre : 'Sistema';
  document.getElementById('modalVentaTipo').textContent = 'Contado (Efectivo)';
  document.getElementById('modalVentaEstado').textContent = (v.estado || 'completada').toUpperCase();

  const tbody = document.getElementById('modalVentaItems');
  tbody.innerHTML = '';
  if (v.items && v.items.length) {
    v.items.forEach(i => {
      const tr = document.createElement('tr');
      tr.innerHTML = `
        <td><strong>${i.nombre_producto || 'Producto'}</strong></td>
        <td style="text-align:center">${i.cantidad}</td>
        <td style="text-align:right">$${parseFloat(i.precio_unitario).toFixed(2)}</td>
        <td style="text-align:right"><strong>$${parseFloat(i.subtotal).toFixed(2)}</strong></td>
      `;
      tbody.appendChild(tr);
    });
  } else {
    tbody.innerHTML = '<tr><td colspan="4" class="text-center text-muted">Sin items registrados</td></tr>';
  }

  document.getElementById('modalVentaSubtotal').textContent = '$' + parseFloat(v.subtotal || 0).toFixed(2);
  document.getElementById('modalVentaIva').textContent = '$' + parseFloat(v.impuesto || 0).toFixed(2);
  document.getElementById('modalVentaTotal').textContent = '$' + parseFloat(v.total || 0).toFixed(2);

  document.getElementById('modalVentaBtnPdf').href = `/ventas/${v.id}/pdf`;
  document.getElementById('modalVentaBtnTicket').href = `/impresoras/ticket/${v.id}`;

  const modal = document.getElementById('modalVerVenta');
  modal.style.display = 'flex';
}

function cerrarModalVerVenta() {
  document.getElementById('modalVerVenta').style.display = 'none';
}

// Cerrar con Escape o clic fuera
document.addEventListener('keydown', e => { if (e.key === 'Escape') cerrarModalVerVenta(); });
document.getElementById('modalVerVenta').addEventListener('click', e => {
  if (e.target.id === 'modalVerVenta') cerrarModalVerVenta();
});
</script>
@endpush
@endsection