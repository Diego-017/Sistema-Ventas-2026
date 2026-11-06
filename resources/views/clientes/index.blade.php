@extends('layouts.main')
@section('title','Clientes')
@section('content')

<div class="page-header" style="margin-bottom: 20px">
  <div>
    <h1 style="color: #38bdf8; font-size: 1.6rem; font-weight: 800; margin:0">👥 Administrar Clientes</h1>
    <p class="page-sub" style="color: #94a3b8; margin-top:4px">Catálogo de clientes, perfiles tributarios y límites de crédito</p>
  </div>
  <div>
    <a href="{{ route('clientes.crear') }}" class="btn btn-primary" style="font-weight:800; display:flex; align-items:center; gap:6px">
      + Agregar Cliente
    </a>
  </div>
</div>

<div class="card" style="border-radius:10px">
  <div class="card-body p-0">
    <div class="table-toolbar" style="padding:12px 18px">
      <input type="search" id="searchTable" placeholder="Buscar por nombre, documento o email..." class="form-control" style="max-width:320px">
    </div>
    <div style="overflow-x:auto">
      <table class="table" id="clientTable" style="margin:0">
        <thead>
          <tr>
            <th>Nombre / Razón Social</th>
            <th>Documento (DUI/NIT)</th>
            <th>Teléfono</th>
            <th>Tipo</th>
            <th>Email</th>
            <th style="text-align:right">Acciones</th>
          </tr>
        </thead>
        <tbody>
          @forelse($clientes as $c)
          <tr>
            <td>
              <a href="{{ route('clientes.show',$c->id) }}" style="font-weight:700; color:#38bdf8">
                {{ $c->nombre }}
              </a>
              @if($c->nombre_comercial)
                <div style="font-size:0.75rem; color:#94a3b8">{{ $c->nombre_comercial }}</div>
              @endif
              <div style="margin-top:2px">
                @if($c->retiene_iva)<span class="badge badge-warning" style="font-size:.65rem">RET. IVA</span>@endif
                @if($c->aplica_percepcion)<span class="badge badge-info" style="font-size:.65rem">PERCEPCIÓN</span>@endif
              </div>
            </td>
            <td>{{ $c->dui ?? $c->nit ?? '—' }}</td>
            <td>{{ $c->telefono ?? '—' }}</td>
            <td>
              <span class="badge badge-{{ $c->tipo==='credito'?'warning':'success' }}">
                {{ $c->tipo === 'credito' ? '💳 Crédito' : '💵 Contado' }}
              </span>
            </td>
            <td>{{ $c->email ?? '—' }}</td>
            <td class="actions" style="text-align:right; white-space:nowrap">
              <a href="{{ route('clientes.show',$c->id) }}" class="btn btn-sm btn-light" title="Ver Perfil y Ficha">👁️ Ver</a>
              <a href="{{ route('clientes.editar',$c->id) }}" class="btn btn-sm btn-primary" title="Editar Cliente">✏️ Editar</a>
              @if(session('user.rol')==='admin')
              <a href="{{ route('clientes.confirmar_eliminar',$c->id) }}" class="btn btn-sm btn-danger" title="Eliminar Cliente">🗑️</a>
              @endif
            </td>
          </tr>
          @empty
          <tr><td colspan="6" class="text-center text-muted" style="padding:2.5rem">Sin clientes registrados en la base de datos.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div class="table-footer">{{ $clientes->links() }}</div>
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
</script>
@endpush

@endsection
