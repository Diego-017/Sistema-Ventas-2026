@extends('layouts.main')
@section('title','Proveedores')
@section('content')

<div class="page-header" style="margin-bottom: 20px">
  <div>
    <h1 style="color: #38bdf8; font-size: 1.6rem; font-weight: 800; margin:0">🏭 Administrar Proveedores</h1>
    <p class="page-sub" style="color: #94a3b8; margin-top:4px">Catálogo de proveedores de compras, insumos y mercadería</p>
  </div>
  <div>
    <a href="{{ route('proveedores.crear') }}" class="btn btn-primary" style="font-weight:800; display:flex; align-items:center; gap:6px">
      + Agregar Proveedor
    </a>
  </div>
</div>

<div class="card" style="border-radius:10px">
  <div class="card-body p-0">
    <div class="table-toolbar" style="padding:12px 18px">
      <input type="search" id="searchTable" placeholder="Buscar por nombre, NIT o contacto..." class="form-control" style="max-width:320px">
    </div>
    <div style="overflow-x:auto">
      <table class="table" id="provTable" style="margin:0">
        <thead>
          <tr>
            <th>Nombre / Proveedor</th>
            <th>NIT / NRC</th>
            <th>Contacto</th>
            <th>Teléfono</th>
            <th>Email</th>
            <th>Dirección</th>
            <th style="text-align:right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($proveedores as $p)
          <tr>
            <td>
              <strong style="color:#38bdf8">{{ $p->nombre }}</strong>
              @if($p->razon_social)
                <div style="font-size:0.75rem; color:#94a3b8">{{ $p->razon_social }}</div>
              @endif
              <div style="margin-top:2px">
                @if($p->aplica_percepcion)<span class="badge badge-info" style="font-size:.65rem">PERCEPCIÓN</span>@endif
                @if($p->aplica_retencion_renta)<span class="badge badge-warning" style="font-size:.65rem">RET. RENTA</span>@endif
              </div>
            </td>
            <td>
              <div>{{ $p->nit ?? '—' }}</div>
              @if($p->nrc)<div style="font-size:0.75rem; color:#94a3b8">NRC: {{ $p->nrc }}</div>@endif
            </td>
            <td>{{ $p->contacto ?? '—' }}</td>
            <td>{{ $p->telefono ?? '—' }}</td>
            <td>{{ $p->email ?? '—' }}</td>
            <td>{{ $p->direccion ?? '—' }}</td>
            <td class="actions" style="text-align:right; white-space:nowrap">
              <a href="{{ route('proveedores.editar', $p->id) }}" class="btn btn-sm btn-primary" title="Editar Proveedor">✏️ Editar</a>
              @if(session('user.rol')==='admin')
              <a href="{{ route('proveedores.confirmar_eliminar', $p->id) }}" class="btn btn-sm btn-danger" title="Eliminar Proveedor">🗑️</a>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="7" class="text-center text-muted" style="padding:2.5rem">Sin proveedores registrados.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@push('scripts')
<script>
document.getElementById('searchTable').addEventListener('input', function() {
  const q = this.value.toLowerCase().trim();
  document.querySelectorAll('#provTable tbody tr').forEach(r => {
    r.style.display = r.textContent.toLowerCase().includes(q) ? '' : 'none';
  });
});
</script>
@endpush

@endsection
