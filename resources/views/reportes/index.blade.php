@extends('layouts.main')
@section('title','Reportes')
@section('content')
<div class="page-header">
  <div>
    <h1>📈 Reportes y Análisis</h1>
    <p class="page-sub">Estadísticas de los últimos 30 días</p>
  </div>
  <div style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
    <input type="date" id="desde" value="{{ now()->startOfMonth()->toDateString() }}" class="form-control" style="width:auto">
    <span class="text-muted">hasta</span>
    <input type="date" id="hasta" value="{{ today()->toDateString() }}" class="form-control" style="width:auto">
    <a id="btnExport" href="{{ route('reportes.exportar') }}" class="btn btn-success">⬇️ Exportar CSV</a>
  </div>
</div>

<div class="stats-grid">
  <div class="stat-card stat-green">
    <div class="stat-icon">💰</div>
    <div class="stat-value">${{ number_format($ventasSemana->sum('total'),2) }}</div>
    <div class="stat-label">Ingresos 30 días</div>
  </div>
  <div class="stat-card stat-blue">
    <div class="stat-icon">🧾</div>
    <div class="stat-value">{{ $ventasSemana->sum('cantidad') }}</div>
    <div class="stat-label">Ventas 30 días</div>
  </div>
  <div class="stat-card stat-purple">
    <div class="stat-icon">📦</div>
    <div class="stat-value">{{ $topProductos->sum('total_vendido') }}</div>
    <div class="stat-label">Unidades Vendidas</div>
  </div>
  <div class="stat-card stat-orange">
    <div class="stat-icon">💳</div>
    <div class="stat-value">${{ number_format($creditosPendientes,2) }}</div>
    <div class="stat-label">Créditos Pendientes</div>
  </div>
</div>

<div class="charts-grid">
  <div class="card">
    <div class="card-header"><h3>Ingresos Diarios (30 días)</h3></div>
    <div class="card-body"><canvas id="chartVentas" height="120"></canvas></div>
  </div>
  <div class="card">
    <div class="card-header"><h3>Distribución por Método de Pago</h3></div>
    <div class="card-body"><canvas id="chartMetodos" height="120"></canvas></div>
  </div>
</div>

<div class="grid-2">
  <div class="card">
    <div class="card-header"><h3>🏆 Top 10 Productos</h3></div>
    <div class="card-body p-0">
      <table class="table">
        <thead><tr><th>#</th><th>Producto</th><th>Unidades</th><th>Ingresos</th></tr></thead>
        <tbody>
          @forelse($topProductos as $i => $p)
          <tr>
            <td><span class="badge badge-{{ $i<3?'success':'warning' }}">{{ $i+1 }}</span></td>
            <td>
              {{ $p->nombre_producto }}
              <br><small><a href="{{ route('reportes.kardex', \App\Models\Producto::where('nombre',$p->nombre_producto)->value('id') ?? 0) }}" class="text-muted">Ver Kardex →</a></small>
            </td>
            <td><strong>{{ number_format($p->total_vendido) }}</strong></td>
            <td>${{ number_format($p->ingresos,2) }}</td>
          </tr>
          @empty
          <tr><td colspan="4" class="text-center text-muted">Sin datos.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card-header"><h3>📅 Resumen Mensual</h3></div>
    <div class="card-body p-0">
      <table class="table">
        <thead><tr><th>Mes</th><th>Ventas</th><th>Total</th></tr></thead>
        <tbody>
          @forelse($resumenMensual as $r)
          <tr>
            <td>{{ \Carbon\Carbon::create($r->anio,$r->mes)->translatedFormat('F Y') }}</td>
            <td>{{ $r->ventas }}</td>
            <td><strong>${{ number_format($r->total,2) }}</strong></td>
          </tr>
          @empty
          <tr><td colspan="3" class="text-center text-muted">Sin datos.</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
const vData=@json($ventasSemana);
const mData=@json($ventasPorMetodo);
const colores={'efectivo':'#4f46e5','tarjeta':'#059669','transferencia':'#d97706','credito':'#dc2626'};

document.addEventListener('DOMContentLoaded',()=>{
  new Chart(document.getElementById('chartVentas'),{
    type:'bar',
    data:{labels:vData.map(r=>r.fecha),datasets:[{label:'Ingresos ($)',data:vData.map(r=>parseFloat(r.total)||0),backgroundColor:'#4f46e5',borderRadius:4}]},
    options:{responsive:true,plugins:{legend:{display:false}},scales:{y:{beginAtZero:true}}}
  });
  new Chart(document.getElementById('chartMetodos'),{
    type:'doughnut',
    data:{labels:mData.map(r=>r.metodo_pago),datasets:[{data:mData.map(r=>parseFloat(r.total)||0),backgroundColor:mData.map(r=>colores[r.metodo_pago]||'#94a3b8'),borderWidth:2,borderColor:'#fff'}]},
    options:{responsive:true,plugins:{legend:{position:'bottom'}}}
  });
});

document.getElementById('desde').addEventListener('change',updateExport);
document.getElementById('hasta').addEventListener('change',updateExport);
function updateExport(){
  const d=document.getElementById('desde').value;
  const h=document.getElementById('hasta').value;
  document.getElementById('btnExport').href=`{{ route('reportes.exportar') }}?desde=${d}&hasta=${h}`;
}
</script>
@endpush
@endsection
