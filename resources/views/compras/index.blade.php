@extends('layouts.main')
@section('title','Compras')
@section('content')
<div class="page-header" style="margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px">
  <div>
    <h1 style="color:#38bdf8; font-size:1.75rem; font-weight:800; margin:0">
      <i class="fa-solid fa-cart-shopping"></i> Compras / Entradas de Inventario
    </h1>
    <p style="color:#94a3b8; margin-top:4px; font-size:0.88rem">Gestión de abastecimiento, ingresos de mercancía y facturas de proveedores</p>
  </div>
  <a href="{{ route('compras.crear') }}" class="btn btn-primary" style="display:flex; align-items:center; gap:6px; font-weight:800">
    <i class="fa-solid fa-plus"></i> Nueva Compra
  </a>
</div>

<div class="card" style="border-radius:10px; background:#1a1e29; border:1px solid #2c3244">
  <div class="card-body p-0">
    <div class="table-toolbar" style="padding:14px 18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; background:#202534; border-bottom:1px solid #2c3244">
      <input type="search" id="searchTable" placeholder="Buscar por código, proveedor..." class="form-control" style="max-width:320px; background:#12141c; color:#ffffff; border-color:#2c3244">
      <span style="color:#94a3b8; font-size:0.85rem">Total: <strong>{{ $compras->total() }}</strong> compras registradas</span>
    </div>

    <div style="overflow-x:auto">
      <table class="table" id="comprasTable" style="margin:0; width:100%; border-collapse:collapse">
        <thead>
          <tr style="background:#202534; color:#94a3b8; text-align:left; border-bottom:1px solid #2c3244">
            <th style="padding:12px 16px">CÓDIGO</th>
            <th style="padding:12px 16px">FECHA</th>
            <th style="padding:12px 16px">PROVEEDOR</th>
            <th style="padding:12px 16px">REGISTRÓ</th>
            <th style="padding:12px 16px">TOTAL</th>
            <th style="padding:12px 16px">ESTADO</th>
            <th style="padding:12px 16px; text-align:right">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          @forelse($compras as $c)
          <tr style="border-bottom:1px solid #282d3c">
            <td style="padding:12px 16px">
              <button type="button" onclick='mostrarDetalleCompra(@json($c))'
                      style="background:none; border:none; padding:0; font-family:monospace; font-weight:700; color:#38bdf8; cursor:pointer; text-decoration:underline">
                {{ $c->codigo }}
              </button>
            </td>
            <td style="padding:12px 16px; color:#94a3b8">{{ $c->created_at->format('d/m/Y H:i') }}</td>
            <td style="padding:12px 16px"><strong style="color:#f1f5f9">{{ $c->proveedor->nombre ?? 'Sin proveedor' }}</strong></td>
            <td style="padding:12px 16px; color:#94a3b8">{{ $c->usuario->nombre ?? 'Sistema' }}</td>
            <td style="padding:12px 16px"><strong style="color:#10b981">${{ number_format($c->total, 2) }}</strong></td>
            <td style="padding:12px 16px">
              <span class="badge badge-{{ $c->estado==='completada'?'success':($c->estado==='anulada'?'danger':'warning') }}">
                {{ ucfirst($c->estado) }}
              </span>
            </td>
            <td style="padding:12px 16px; text-align:right; white-space:nowrap">
              <button type="button" onclick='mostrarDetalleCompra(@json($c))' class="btn btn-sm btn-secondary" title="Ver Detalle en Pantalla">
                <i class="fa-solid fa-eye"></i> Ver
              </button>
              <a href="{{ route('compras.pdf', $c->id) }}" target="_blank" class="btn btn-sm btn-primary" title="Generar PDF">
                <i class="fa-solid fa-file-lines"></i> PDF
              </a>
              @if($c->estado==='completada')
              <button onclick="anularCompra({{ $c->id }})" class="btn btn-sm btn-danger" title="Anular Compra">
                <i class="fa-solid fa-ban"></i>
              </button>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="7" class="text-center text-muted" style="padding:2.5rem; text-align:center">
              <i class="fa-solid fa-cart-shopping" style="font-size:2rem; opacity:0.3; display:block; margin-bottom:8px"></i>
              Sin compras registradas.
            </td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>

    <div class="table-footer" style="padding:12px 18px; background:#202534; border-top:1px solid #2c3244">
      {{ $compras->links() }}
    </div>
  </div>
</div>

{{-- MODAL INLINE: VER DETALLE DE COMPRA EN LA MISMA PANTALLA --}}
<div id="modalDetalleCompra" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.75); z-index:9999; align-items:center; justify-content:center; padding:15px">
  <div style="background:#1a1e29; border:1px solid #2c3244; border-radius:12px; max-width:750px; width:100%; box-shadow:0 12px 40px rgba(0,0,0,0.6); overflow:hidden">
    <div style="background:#202534; padding:14px 20px; border-bottom:1px solid #2c3244; display:flex; justify-content:space-between; align-items:center">
      <h3 style="margin:0; font-size:1.15rem; color:#38bdf8; display:flex; align-items:center; gap:8px">
        <i class="fa-solid fa-cart-flatbed"></i> Detalle de Entrada / Compra: <span id="modalCompraCodigo" style="color:#ffffff"></span>
      </h3>
      <button type="button" onclick="cerrarDetalleCompra()" style="background:none; border:none; color:#94a3b8; font-size:1.4rem; cursor:pointer">&times;</button>
    </div>

    <div style="padding:20px; max-height:75vh; overflow-y:auto">
      <div style="display:grid; grid-template-columns:repeat(3, 1fr); gap:12px; background:#141822; padding:12px 16px; border-radius:8px; border:1px solid #282d3c; margin-bottom:16px; font-size:0.88rem">
        <div><span style="color:#94a3b8">Proveedor:</span> <strong id="modalCompraProveedor" style="color:#f1f5f9; display:block"></strong></div>
        <div><span style="color:#94a3b8">Fecha Emisión:</span> <span id="modalCompraFecha" style="color:#cbd5e1; display:block"></span></div>
        <div><span style="color:#94a3b8">Registrado por:</span> <span id="modalCompraUsuario" style="color:#cbd5e1; display:block"></span></div>
      </div>

      <h4 style="font-size:0.92rem; font-weight:800; color:#38bdf8; margin:0 0 10px 0; text-transform:uppercase">Ítems Ingresados al Inventario</h4>
      <table class="table" style="margin-bottom:16px; width:100%; font-size:0.88rem; border-collapse:collapse">
        <thead>
          <tr style="background:#202534; color:#94a3b8">
            <th style="padding:8px 12px">Producto</th>
            <th style="padding:8px 12px; text-align:center">Cant.</th>
            <th style="padding:8px 12px; text-align:right">Costo Unit.</th>
            <th style="padding:8px 12px; text-align:right">Subtotal</th>
          </tr>
        </thead>
        <tbody id="modalCompraItemsBody"></tbody>
      </table>

      <div style="display:flex; justify-content:flex-end">
        <div style="width:260px; font-size:0.9rem; line-height:1.8">
          <div style="display:flex; justify-content:space-between; border-top:2px solid #38bdf8; padding-top:6px">
            <span style="font-size:1.05rem; font-weight:800; color:#ffffff">TOTAL COMPRA:</span>
            <span id="modalCompraTotal" style="font-size:1.25rem; font-weight:900; color:#10b981">$0.00</span>
          </div>
        </div>
      </div>
    </div>

    <div style="padding:14px 20px; border-top:1px solid #2c3244; display:flex; justify-content:space-between; align-items:center; background:#151822">
      <a id="modalCompraBtnPdf" href="#" target="_blank" class="btn btn-primary btn-sm" style="display:inline-flex; align-items:center; gap:6px">
        <i class="fa-solid fa-file-pdf"></i> Imprimir Comprobante PDF
      </a>
      <button type="button" onclick="cerrarDetalleCompra()" class="btn btn-secondary btn-sm">Cerrar</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.getElementById('searchTable').addEventListener('input', function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll('#comprasTable tbody tr').forEach(r => {
    r.style.display = r.textContent.toLowerCase().includes(q) ? '':'none';
  });
});

function anularCompra(id) {
  if (!confirm('¿Anular esta compra? Se revertirá el stock ingresado.')) return;
  fetch(`/compras/${id}/anular`, {
    method:'PATCH',
    headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'}
  }).then(r=>r.json()).then(d=>{ if(d.ok) location.reload(); });
}

function mostrarDetalleCompra(compra) {
  document.getElementById('modalCompraCodigo').textContent = compra.codigo;
  document.getElementById('modalCompraProveedor').textContent = (compra.proveedor && compra.proveedor.nombre) ? compra.proveedor.nombre : 'Sin proveedor';
  document.getElementById('modalCompraFecha').textContent = new Date(compra.created_at).toLocaleString('es-SV');
  document.getElementById('modalCompraUsuario').textContent = (compra.usuario && compra.usuario.nombre) ? compra.usuario.nombre : 'Sistema';
  document.getElementById('modalCompraTotal').textContent = '$' + parseFloat(compra.total).toFixed(2);
  document.getElementById('modalCompraBtnPdf').href = `/compras/${compra.id}/pdf`;

  const tbody = document.getElementById('modalCompraItemsBody');
  tbody.innerHTML = '';
  if (compra.items && compra.items.length) {
    compra.items.forEach(i => {
      const tr = document.createElement('tr');
      tr.style.borderBottom = '1px solid #282d3c';
      tr.innerHTML = `
        <td style="padding:8px 12px"><strong>${i.nombre_producto || (i.producto ? i.producto.nombre : 'Producto')}</strong></td>
        <td style="padding:8px 12px; text-align:center">${i.cantidad}</td>
        <td style="padding:8px 12px; text-align:right">$${parseFloat(i.costo_unitario || i.precio_unitario || 0).toFixed(2)}</td>
        <td style="padding:8px 12px; text-align:right; font-weight:700; color:#10b981">$${parseFloat(i.subtotal || 0).toFixed(2)}</td>
      `;
      tbody.appendChild(tr);
    });
  } else {
    tbody.innerHTML = '<tr><td colspan="4" style="text-align:center; padding:1rem; color:#94a3b8">Sin ítems desglosados</td></tr>';
  }

  const modal = document.getElementById('modalDetalleCompra');
  modal.style.display = 'flex';
}

function cerrarDetalleCompra() {
  document.getElementById('modalDetalleCompra').style.display = 'none';
}
</script>
@endpush
@endsection
