@extends('layouts.main')
@section('title','Dashboard')
@section('content')

<div class="page-header">
  <div>
    <h1>📊 Dashboard</h1>
    <p class="page-sub">Bienvenido, {{ session('user.nombre') }} — {{ now()->translatedFormat('l, d \\de F Y') }}</p>
  </div>
  <span class="date-badge">{{ now()->format('H:i') }}</span>
</div>

<div class="stats-grid">
  <div class="stat-card stat-success">
    <div class="stat-icon-wrap">💰</div>
    <div class="stat-info">
      <div class="stat-value">${{ number_format($totalHoy,2) }}</div>
      <div class="stat-label">Ventas Hoy</div>
      <div class="stat-sub">{{ $ventasHoy }} transacciones</div>
    </div>
  </div>
  <div class="stat-card stat-primary">
    <div class="stat-icon-wrap">📅</div>
    <div class="stat-info">
      <div class="stat-value">${{ number_format($ventasMes,2) }}</div>
      <div class="stat-label">Ventas del Mes</div>
      <div class="stat-sub {{ $variacion >= 0 ? 'text-success':'text-danger' }}">
        {{ $variacion >= 0 ? '▲':'▼' }} {{ abs($variacion) }}% vs mes anterior
      </div>
    </div>
  </div>
  <div class="stat-card stat-purple">
    <div class="stat-icon-wrap">📈</div>
    <div class="stat-info">
      <div class="stat-value">${{ number_format($margenHoy,2) }}</div>
      <div class="stat-label">Margen Hoy</div>
      <div class="stat-sub">Ganancia estimada</div>
    </div>
  </div>
  <div class="stat-card stat-warning">
    <div class="stat-icon-wrap">💳</div>
    <div class="stat-info">
      <div class="stat-value">${{ number_format($vouchersHoy,2) }}</div>
      <div class="stat-label">Vouchers Tarjeta</div>
      <div class="stat-sub"><a href="{{ route('caja.chica') }}" style="color:inherit">Ver Caja Chica →</a></div>
    </div>
  </div>
</div>

<div class="stats-grid" style="grid-template-columns:1fr 1fr 1fr 1fr">
  <div class="stat-card stat-info">
    <div class="stat-icon-wrap">🟢</div>
    <div class="stat-info">
      <div class="stat-value">+${{ number_format($ingresosCaja,2) }}</div>
      <div class="stat-label">Ingresos Caja Chica</div>
    </div>
  </div>
  <div class="stat-card stat-danger">
    <div class="stat-icon-wrap">🔴</div>
    <div class="stat-info">
      <div class="stat-value">-${{ number_format($egresosCaja,2) }}</div>
      <div class="stat-label">Egresos Caja Chica</div>
    </div>
  </div>
  <div class="stat-card stat-warning">
    <div class="stat-icon-wrap">⏳</div>
    <div class="stat-info">
      <div class="stat-value">{{ $creditosPendientes }}</div>
      <div class="stat-label">Créditos Pendientes</div>
    </div>
  </div>
  <div class="stat-card stat-info">
    <div class="stat-icon-wrap">📄</div>
    <div class="stat-info">
      <div class="stat-value">{{ $cotizacionesVigentes }}</div>
      <div class="stat-label">Cotizaciones Vigentes</div>
      <div class="stat-sub"><a href="{{ route('cotizaciones.index') }}" style="color:inherit">Ver →</a></div>
    </div>
  </div>
</div>

<div class="charts-grid">
  <div class="card">
    <div class="card-header"><h3>📈 Ventas últimos 7 días</h3></div>
    <div class="card-body"><canvas id="chartVentas" height="110"></canvas></div>
  </div>
  <div class="card">
    <div class="card-header"><h3>🏆 Top 5 Productos (mes)</h3></div>
    <div class="card-body"><canvas id="chartTop" height="110"></canvas></div>
  </div>
</div>

{{-- AUDITORÍA EN TIEMPO REAL / FEED DEL ADMINISTRADOR --}}
@if(session('user.rol') === 'admin')
<div class="card" style="background:#fff; border-radius:12px; padding:20px; box-shadow:0 1px 3px rgba(0,0,0,0.1); margin-bottom:25px; border-left: 5px solid #2563eb;">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:15px;">
    <h3 style="margin:0; font-size:1.1rem; color:#0f172a; font-weight:800; display:flex; align-items:center; gap:8px;">
      🔔 Actividad Reciente en Tiempo Real (Panel de Administración)
    </h3>
    <span style="font-size:0.8rem; color:#64748b; background:#f1f5f9; padding:4px 8px; border-radius:4px; font-weight:600;">Supervisión Global</span>
  </div>

  <div style="overflow-x:auto;">
    <table class="table" style="width:100%; font-size:0.85rem;">
      <thead>
        <tr style="background:#f8fafc; color:#475569;">
          <th style="padding:8px;">Hora</th>
          <th style="padding:8px;">Usuario</th>
          <th style="padding:8px;">Módulo</th>
          <th style="padding:8px;">Acción</th>
          <th style="padding:8px;">Detalle</th>
          <th style="padding:8px;">Monto</th>
        </tr>
      </thead>
      <tbody>
        @forelse($actividadesRecientes as $act)
        <tr style="border-bottom:1px solid #f1f5f9;">
          <td style="padding:8px; color:#64748b; font-weight:600;">{{ $act->created_at->format('H:i:s') }}</td>
          <td style="padding:8px; font-weight:700; color:#0f172a;">{{ $act->usuario->nombre ?? 'Sistema' }}</td>
          <td style="padding:8px;"><span style="background:#e0f2fe; color:#0369a1; padding:2px 6px; border-radius:4px; font-weight:700; font-size:0.75rem;">{{ $act->modulo }}</span></td>
          <td style="padding:8px; font-weight:600;">{{ $act->accion }}</td>
          <td style="padding:8px; color:#475569;">{{ $act->detalle }}</td>
          <td style="padding:8px; font-weight:800; color:#16a34a;">
            {{ $act->monto ? '$'.number_format($act->monto, 2) : '-' }}
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="6" class="text-center text-muted" style="padding:15px;">No hay actividades registradas en la bitácora aún.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endif

<div class="grid-2">
  <div class="card">
    <div class="card-header">
      <h3>🧾 Últimas Ventas</h3>
      <a href="{{ route('ventas.index') }}" class="btn btn-sm btn-light">Ver todas</a>
    </div>
    <div class="card-body p-0">
      <table class="table">
        <thead><tr><th>Código</th><th>Cliente</th><th>Total</th><th>Estado</th></tr></thead>
        <tbody>
          @forelse($ultimasVentas as $v)
          <tr>
            <td><a href="{{ route('ventas.ver',$v->id) }}"><code>{{ $v->codigo }}</code></a></td>
            <td>{{ $v->cliente->nombre ?? 'General' }}</td>
            <td><strong>${{ number_format($v->total,2) }}</strong></td>
            <td><span class="badge badge-{{ $v->estado==='completada'?'success':'danger' }}">{{ $v->estado }}</span></td>
          </tr>
          @empty
          <tr><td colspan="4" class="text-center text-muted">Sin ventas hoy</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h3>⚠️ Productos con Stock Bajo</h3>
      <a href="{{ route('compras.crear') }}" class="btn btn-sm btn-primary">+ Compra</a>
    </div>
    <div class="card-body p-0">
      @if($stockBajo->isEmpty())
        <div class="empty-state">✅ Todos los productos tienen stock suficiente.</div>
      @else
      <table class="table">
        <thead><tr><th>Producto</th><th>Stock</th><th>Mínimo</th><th></th></tr></thead>
        <tbody>
          @foreach($stockBajo->take(6) as $p)
          <tr class="row-danger">
            <td>{{ $p->nombre }}</td>
            <td><span class="badge badge-danger">{{ $p->stock }}</span></td>
            <td>{{ $p->stock_minimo }}</td>
            <td><a href="{{ route('productos.editar',$p->id) }}" class="btn btn-sm btn-light">✏️</a></td>
          </tr>
          @endforeach
        </tbody>
      </table>
      @endif
    </div>
  </div>
</div>

@if($caja)
<div class="card">
  <div class="card-header">
    <h3>💰 Caja Actual — <span class="text-success">ABIERTA</span></h3>
    <a href="{{ route('caja.index') }}" class="btn btn-sm btn-light">Gestionar Caja</a>
  </div>
  <div class="card-body">
    <div class="caja-stats">
      <div class="caja-stat"><div class="caja-stat-label">Apertura</div><div class="caja-stat-value">${{ number_format($caja->monto_apertura,2) }}</div></div>
      <div class="caja-stat"><div class="caja-stat-label">Ventas del día</div><div class="caja-stat-value text-success">${{ number_format($caja->total_ventas,2) }}</div></div>
      <div class="caja-stat"><div class="caja-stat-label">Gastos / Retiros</div><div class="caja-stat-value text-danger">${{ number_format($caja->total_gastos,2) }}</div></div>
      <div class="caja-stat"><div class="caja-stat-label">Balance Estimado</div><div class="caja-stat-value text-primary">${{ number_format($caja->balance_estimado,2) }}</div></div>
    </div>
  </div>
</div>
@else
<div class="alert alert-warning">
  ⚠️ No hay caja abierta. Las ventas no se podrán registrar correctamente.
  <a href="{{ route('caja.index') }}" class="btn btn-sm btn-primary" style="margin-left:8px">Abrir Caja</a>
</div>
@endif

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const vData=@json($ventasSemana);
const tData=@json($topProductos);
document.addEventListener('DOMContentLoaded',()=>{
  new Chart(document.getElementById('chartVentas'),{
    type:'line',
    data:{labels:vData.map(r=>r.fecha),datasets:[{label:'Total ($)',data:vData.map(r=>parseFloat(r.total)||0),borderColor:'#4e73df',backgroundColor:'rgba(78,115,223,.08)',tension:.4,fill:true,pointRadius:4,pointBackgroundColor:'#4e73df'}]},
    options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
  });
  new Chart(document.getElementById('chartTop'),{
    type:'bar',
    data:{labels:tData.map(r=>r.nombre_producto.substring(0,18)),datasets:[{label:'Unidades',data:tData.map(r=>parseInt(r.total_vendido)||0),backgroundColor:['#4e73df','#6f42c1','#e83e8c','#1cc88a','#f6c23e'],borderRadius:6}]},
    options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
  });
});
</script>
@endpush
@endsection
