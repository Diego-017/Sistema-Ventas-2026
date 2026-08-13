@extends('layouts.main')
@section('title','Cuentas por Cobrar')
@section('content')
<div class="page-header">
  <div>
    <h1>💳 Cuentas por Cobrar</h1>
    <p class="page-sub">Administración de créditos y pagos pendientes</p>
  </div>
</div>

<div class="stats-grid" style="grid-template-columns:1fr 1fr 1fr">
  <div class="stat-card stat-orange">
    <div class="stat-icon">⏳</div>
    <div class="stat-value">${{ number_format($totalPendiente,2) }}</div>
    <div class="stat-label">Saldo Pendiente</div>
  </div>
  <div class="stat-card stat-red">
    <div class="stat-icon">🔴</div>
    <div class="stat-value">${{ number_format($totalVencido,2) }}</div>
    <div class="stat-label">Saldo Vencido</div>
  </div>
  <div class="stat-card stat-green">
    <div class="stat-icon">✅</div>
    <div class="stat-value">{{ $creditos->where('estado','pagado')->count() }}</div>
    <div class="stat-label">Créditos Pagados (total)</div>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-toolbar" style="display:flex;gap:12px">
      <input type="search" id="searchTable" placeholder="Buscar cliente..." class="form-control" style="max-width:260px">
      <select id="filtroEstado" class="form-control" style="max-width:160px">
        <option value="">Todos</option>
        <option value="pendiente">Pendiente</option>
        <option value="vencido">Vencido</option>
        <option value="pagado">Pagado</option>
      </select>
    </div>
    <table class="table" id="creditTable">
      <thead>
        <tr><th>Código Venta</th><th>Cliente</th><th>Total</th><th>Pagado</th><th>Saldo</th><th>Vence</th><th>Estado</th><th></th></tr>
      </thead>
      <tbody>
        @forelse($creditos as $c)
        <tr data-estado="{{ $c->estado }}">
          <td>
            @if($c->venta)
              <a href="{{ route('ventas.ver',$c->venta->id) }}"><code>{{ $c->venta->codigo }}</code></a>
            @else —
            @endif
          </td>
          <td><strong>{{ $c->cliente->nombre }}</strong></td>
          <td>${{ number_format($c->monto_total,2) }}</td>
          <td class="text-success">${{ number_format($c->monto_pagado,2) }}</td>
          <td class="{{ $c->estado==='vencido'?'text-danger':'text-warning' }}">
            <strong>${{ number_format($c->saldo,2) }}</strong>
          </td>
          <td>
            @if($c->fecha_vencimiento)
              {{ $c->fecha_vencimiento->format('d/m/Y') }}
              @if($c->estaVencido())
                <span class="badge badge-danger">Vencido</span>
              @endif
            @else —
            @endif
          </td>
          <td>
            <span class="badge badge-{{ $c->estado==='pagado'?'success':($c->estado==='vencido'?'danger':'warning') }}">
              {{ $c->estado }}
            </span>
          </td>
          <td>
            <a href="{{ route('creditos.show',$c->id) }}" class="btn btn-sm btn-secondary">👁️ Ver</a>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted" style="padding:2rem">Sin créditos registrados.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="table-footer">{{ $creditos->links() }}</div>
  </div>
</div>
@push('scripts')
<script>
document.getElementById('searchTable').addEventListener('input', filtrar);
document.getElementById('filtroEstado').addEventListener('change', filtrar);
function filtrar(){
  const q=document.getElementById('searchTable').value.toLowerCase();
  const e=document.getElementById('filtroEstado').value;
  document.querySelectorAll('#creditTable tbody tr').forEach(r=>{
    const textOk=r.textContent.toLowerCase().includes(q);
    const estadoOk=!e||r.dataset.estado===e;
    r.style.display=(textOk&&estadoOk)?'':'none';
  });
}
</script>
@endpush
@endsection
