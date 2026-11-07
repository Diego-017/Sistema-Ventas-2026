@extends('layouts.main')
@section('title','Clientes')
@section('content')
<div class="page-header" style="margin-bottom:20px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px">
  <div>
    <h1 style="color:#38bdf8; font-size:1.75rem; font-weight:800; margin:0">
      <i class="fa-solid fa-users"></i> Administrar Clientes
    </h1>
    <p style="color:#94a3b8; margin-top:4px; font-size:0.88rem">Directorio de clientes, cartera de crédito y datos fiscales DTE</p>
  </div>
  <a href="{{ route('clientes.crear') }}" class="btn btn-primary" style="display:flex; align-items:center; gap:6px; font-weight:800">
    <i class="fa-solid fa-user-plus"></i> Nuevo Cliente
  </a>
</div>

<div class="card" style="border-radius:10px; background:#1a1e29; border:1px solid #2c3244">
  <div class="card-body p-0">
    <div class="table-toolbar" style="padding:14px 18px; display:flex; justify-content:space-between; align-items:center; flex-wrap:wrap; gap:12px; background:#202534; border-bottom:1px solid #2c3244">
      <input type="search" id="searchTable" placeholder="Buscar por nombre, documento, teléfono..." class="form-control" style="max-width:320px; background:#12141c; color:#ffffff; border-color:#2c3244">
      <span style="color:#94a3b8; font-size:0.85rem">Total: <strong>{{ $clientes->total() }}</strong> clientes registrados</span>
    </div>

    <div style="overflow-x:auto">
      <table class="table" id="clientTable" style="margin:0; width:100%; border-collapse:collapse">
        <thead>
          <tr style="background:#202534; color:#94a3b8; text-align:left; border-bottom:1px solid #2c3244">
            <th style="padding:12px 16px">NOMBRE / RAZÓN SOCIAL</th>
            <th style="padding:12px 16px">DOCUMENTO</th>
            <th style="padding:12px 16px">TELÉFONO</th>
            <th style="padding:12px 16px">CONDICIÓN</th>
            <th style="padding:12px 16px">CORREO</th>
            <th style="padding:12px 16px; text-align:right">ACCIONES</th>
          </tr>
        </thead>
        <tbody>
          @forelse($clientes as $c)
          <tr style="border-bottom:1px solid #282d3c">
            <td style="padding:12px 16px">
              <button type="button" onclick='mostrarDetalleCliente(@json($c))'
                      style="background:none; border:none; padding:0; font-weight:800; color:#38bdf8; cursor:pointer; text-align:left">
                {{ $c->nombre }}
              </button>
              @if($c->nombre_comercial)
                <div style="font-size:0.75rem; color:#94a3b8">{{ $c->nombre_comercial }}</div>
              @endif
              <div style="display:flex; gap:4px; margin-top:4px">
                @if($c->retiene_iva)<span class="badge badge-warning" style="font-size:.65rem">RET. IVA</span>@endif
                @if($c->aplica_percepcion)<span class="badge badge-info" style="font-size:.65rem">PERCEPCIÓN</span>@endif
              </div>
            </td>
            <td style="padding:12px 16px; color:#cbd5e1">{{ $c->dui ?? $c->nit ?? '—' }}</td>
            <td style="padding:12px 16px; color:#cbd5e1">{{ $c->telefono ?? '—' }}</td>
            <td style="padding:12px 16px">
              <span class="badge badge-{{ $c->tipo==='credito'?'warning':'success' }}">
                {!! $c->tipo === 'credito' ? '<i class="fa-solid fa-credit-card"></i> Crédito' : '<i class="fa-solid fa-money-bill-wave"></i> Contado' !!}
              </span>
            </td>
            <td style="padding:12px 16px; color:#cbd5e1">{{ $c->email ?? '—' }}</td>
            <td style="padding:12px 16px; text-align:right; white-space:nowrap">
              <button type="button" onclick='mostrarDetalleCliente(@json($c))' class="btn btn-sm btn-secondary" title="Ver Perfil y Ficha en Pantalla">
                <i class="fa-solid fa-eye"></i> Ver
              </button>
              <a href="{{ route('clientes.editar',$c->id) }}" class="btn btn-sm btn-primary" title="Editar Cliente">
                <i class="fa-solid fa-pen"></i> Editar
              </a>
              @if(session('user.rol')==='admin')
              <a href="{{ route('clientes.confirmar_eliminar',$c->id) }}" class="btn btn-sm btn-danger" title="Eliminar Cliente">
                <i class="fa-solid fa-trash"></i>
              </a>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center text-muted" style="padding:2.5rem; text-align:center">Sin clientes registrados en la base de datos.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="table-footer" style="padding:12px 18px; background:#202534; border-top:1px solid #2c3244">
      {{ $clientes->links() }}
    </div>
  </div>
</div>

{{-- MODAL INLINE: VER PERFIL DE CLIENTE EN LA MISMA PANTALLA --}}
<div id="modalDetalleCliente" class="modal-overlay" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,0.75); z-index:9999; align-items:center; justify-content:center; padding:15px">
  <div style="background:#1a1e29; border:1px solid #2c3244; border-radius:12px; max-width:680px; width:100%; box-shadow:0 12px 40px rgba(0,0,0,0.6); overflow:hidden">
    <div style="background:#202534; padding:14px 20px; border-bottom:1px solid #2c3244; display:flex; justify-content:space-between; align-items:center">
      <h3 style="margin:0; font-size:1.15rem; color:#38bdf8; display:flex; align-items:center; gap:8px">
        <i class="fa-solid fa-address-card"></i> Ficha del Cliente: <span id="modalClienteNombre" style="color:#ffffff"></span>
      </h3>
      <button type="button" onclick="cerrarDetalleCliente()" style="background:none; border:none; color:#94a3b8; font-size:1.4rem; cursor:pointer">&times;</button>
    </div>

    <div style="padding:20px; max-height:75vh; overflow-y:auto">
      <div style="display:grid; grid-template-columns:1fr 1fr; gap:14px; background:#141822; padding:14px 18px; border-radius:8px; border:1px solid #282d3c; margin-bottom:16px; font-size:0.88rem">
        <div><span style="color:#94a3b8">Nombre Comercial:</span> <strong id="modalClienteComercial" style="color:#f1f5f9; display:block"></strong></div>
        <div><span style="color:#94a3b8">Tipo de Operación:</span> <span id="modalClienteTipo" style="display:block; font-weight:700"></span></div>
        <div><span style="color:#94a3b8">DUI / NIT / Doc:</span> <strong id="modalClienteDoc" style="color:#38bdf8; display:block"></strong></div>
        <div><span style="color:#94a3b8">NRC / Registro:</span> <span id="modalClienteNrc" style="color:#cbd5e1; display:block"></span></div>
        <div><span style="color:#94a3b8">Teléfono:</span> <span id="modalClienteTel" style="color:#cbd5e1; display:block"></span></div>
        <div><span style="color:#94a3b8">Correo Electrónico:</span> <span id="modalClienteEmail" style="color:#cbd5e1; display:block"></span></div>
        <div style="grid-column:1 / -1"><span style="color:#94a3b8">Giro / Actividad Económica:</span> <span id="modalClienteGiro" style="color:#cbd5e1; display:block"></span></div>
        <div style="grid-column:1 / -1"><span style="color:#94a3b8">Dirección:</span> <span id="modalClienteDir" style="color:#cbd5e1; display:block"></span></div>
        <div><span style="color:#94a3b8">Departamento:</span> <span id="modalClienteDepto" style="color:#cbd5e1; display:block"></span></div>
        <div><span style="color:#94a3b8">Municipio:</span> <span id="modalClienteMun" style="color:#cbd5e1; display:block"></span></div>
      </div>
    </div>

    <div style="padding:14px 20px; border-top:1px solid #2c3244; display:flex; justify-content:space-between; align-items:center; background:#151822">
      <div style="display:flex; gap:8px">
        <a id="modalClienteBtnFicha" href="#" class="btn btn-primary btn-sm">
          <i class="fa-solid fa-arrow-up-right-from-square"></i> Ver Historial y Saldo
        </a>
        <a id="modalClienteBtnEditar" href="#" class="btn btn-secondary btn-sm">
          <i class="fa-solid fa-pen"></i> Editar
        </a>
      </div>
      <button type="button" onclick="cerrarDetalleCliente()" class="btn btn-secondary btn-sm">Cerrar</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.getElementById('searchTable').addEventListener('input', function() {
  const q = this.value.toLowerCase().trim();
  document.querySelectorAll('#clientTable tbody tr').forEach(r => {
    r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
});

function mostrarDetalleCliente(c) {
  document.getElementById('modalClienteNombre').textContent = c.nombre;
  document.getElementById('modalClienteComercial').textContent = c.nombre_comercial || '—';
  document.getElementById('modalClienteTipo').innerHTML = c.tipo === 'credito'
    ? '<span style="color:#f59e0b"><i class="fa-solid fa-credit-card"></i> Crédito</span>'
    : '<span style="color:#10b981"><i class="fa-solid fa-money-bill-wave"></i> Contado</span>';
  document.getElementById('modalClienteDoc').textContent = c.dui || c.nit || c.documento || '—';
  document.getElementById('modalClienteNrc').textContent = c.nrc || '—';
  document.getElementById('modalClienteTel').textContent = c.telefono || '—';
  document.getElementById('modalClienteEmail').textContent = c.email || '—';
  document.getElementById('modalClienteGiro').textContent = c.giro || '—';
  document.getElementById('modalClienteDir').textContent = c.direccion || '—';
  document.getElementById('modalClienteDepto').textContent = c.departamento || '—';
  document.getElementById('modalClienteMun').textContent = c.municipio || '—';

  document.getElementById('modalClienteBtnFicha').href = `/clientes/${c.id}`;
  document.getElementById('modalClienteBtnEditar').href = `/clientes/${c.id}/editar`;

  const modal = document.getElementById('modalDetalleCliente');
  modal.style.display = 'flex';
}

function cerrarDetalleCliente() {
  document.getElementById('modalDetalleCliente').style.display = 'none';
}
</script>
@endpush
@endsection
