@extends('layouts.main')
@section('title','Análisis de Gráficas y Estadísticas')
@section('content')

<div class="page-header" style="margin-bottom: 22px">
  <div>
    <h1 style="color: #38bdf8; font-size: 1.75rem; font-weight: 800; margin:0">📊 Análisis y Estadísticas</h1>
    <p class="page-sub" style="color: #94a3b8; margin-top:4px">Visualización gráfica avanzada, métricas de rendimiento y tendencias comerciales</p>
  </div>
  <div style="display:flex; align-items:center; gap:8px; flex-wrap:wrap">
    <div style="background:#1e2433; border:1px solid #334155; border-radius:8px; padding:4px; display:inline-flex; gap:4px">
      @foreach([7 => '7 Días', 15 => '15 Días', 30 => '30 Días', 90 => '3 Meses', 365 => '1 Año'] as $val => $lbl)
      <a href="{{ route('reportes.estadisticas', ['dias' => $val]) }}"
         class="btn btn-sm {{ $dias == $val ? 'btn-primary' : 'btn-light' }}"
         style="font-size:0.8rem; font-weight:700; padding:4px 10px; border-radius:6px">
        {{ $lbl }}
      </a>
      @endforeach
    </div>
  </div>
</div>

{{-- KPIs de Alto Nivel del Período --}}
<div class="stats-grid" style="margin-bottom: 24px">
  <div class="stat-card stat-success">
    <div class="stat-icon-wrap">💵</div>
    <div class="stat-info">
      <div class="stat-value">${{ number_format($totalIngresos, 2) }}</div>
      <div class="stat-label">Ingresos del Período</div>
      <div class="stat-sub">Ventas completadas en {{ $dias }} días</div>
    </div>
  </div>
  <div class="stat-card stat-primary">
    <div class="stat-icon-wrap">🧾</div>
    <div class="stat-info">
      <div class="stat-value">{{ number_format($totalTransacciones) }}</div>
      <div class="stat-label">Transacciones Realizadas</div>
      <div class="stat-sub">Órdenes facturadas</div>
    </div>
  </div>
  <div class="stat-card stat-purple">
    <div class="stat-icon-wrap">🎯</div>
    <div class="stat-info">
      <div class="stat-value">${{ number_format($ticketPromedio, 2) }}</div>
      <div class="stat-label">Ticket Promedio</div>
      <div class="stat-sub">Ingreso promedio por venta</div>
    </div>
  </div>
  <div class="stat-card stat-warning">
    <div class="stat-icon-wrap">📦</div>
    <div class="stat-info">
      <div class="stat-value">${{ number_format($totalCompras, 2) }}</div>
      <div class="stat-label">Inversión en Compras</div>
      <div class="stat-sub">Abastecimiento en {{ $dias }} días</div>
    </div>
  </div>
</div>

{{-- ── GRÁFICAS PRINCIPALES ── --}}
<div class="charts-grid" style="grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 24px">
  {{-- 1. Gráfica de Tendencia de Ventas Diarias --}}
  <div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center">
      <h3>📈 Tendencia de Ventas (Últimos {{ $dias }} días)</h3>
      <span style="font-size:0.8rem; color:#94a3b8">Monto diario ($)</span>
    </div>
    <div class="card-body">
      <div style="position:relative; height:290px">
        <canvas id="chartTendenciaVentas"></canvas>
      </div>
    </div>
  </div>

  {{-- 2. Gráfica de Métodos de Pago --}}
  <div class="card">
    <div class="card-header">
      <h3>💳 Métodos de Pago</h3>
    </div>
    <div class="card-body">
      <div style="position:relative; height:290px">
        <canvas id="chartMetodosPago"></canvas>
      </div>
    </div>
  </div>
</div>

<div class="charts-grid" style="grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 24px">
  {{-- 3. Top 10 Productos Más Vendidos --}}
  <div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center">
      <h3>🏆 Top 10 Productos Más Vendidos</h3>
      <span style="font-size:0.8rem; color:#94a3b8">Unidades</span>
    </div>
    <div class="card-body">
      <div style="position:relative; height:310px">
        <canvas id="chartTopProductos"></canvas>
      </div>
    </div>
  </div>

  {{-- 4. Comparativa Mensual Ventas vs Compras --}}
  <div class="card">
    <div class="card-header" style="display:flex; justify-content:space-between; align-items:center">
      <h3>📅 Comparativa Mensual: Ventas vs Compras (Último Año)</h3>
      <span style="font-size:0.8rem; color:#94a3b8">Flujo comercial ($)</span>
    </div>
    <div class="card-body">
      <div style="position:relative; height:310px">
        <canvas id="chartComparativaMensual"></canvas>
      </div>
    </div>
  </div>
</div>

{{-- Tabla Detallada del Top de Productos --}}
<div class="card" style="margin-bottom: 24px">
  <div class="card-header" style="display:flex; justify-content:space-between; align-items:center">
    <h3>📋 Detalle de Productos Estrella (Período Seleccionado)</h3>
    <span style="font-size:0.82rem; color:#64748b">Generadores de mayor volumen de venta</span>
  </div>
  <div class="card-body p-0">
    <table class="table">
      <thead>
        <tr>
          <th>Ranking</th>
          <th>Producto</th>
          <th>Unidades Vendidas</th>
          <th>Ingresos Generados</th>
          <th>% del Total del Período</th>
        </tr>
      </thead>
      <tbody>
        @forelse($topProductos as $idx => $prod)
        <tr>
          <td>
            <span class="badge {{ $idx === 0 ? 'badge-warning' : ($idx === 1 ? 'badge-info' : ($idx === 2 ? 'badge-primary' : 'badge-light')) }}"
                  style="font-size:0.85rem; font-weight:800; padding:4px 8px">
              #{{ $idx + 1 }}
            </span>
          </td>
          <td><strong>{{ $prod->nombre_producto }}</strong></td>
          <td><span class="badge badge-success">{{ number_format($prod->total_vendido) }} uds</span></td>
          <td><strong style="color:#10b981">${{ number_format($prod->ingresos, 2) }}</strong></td>
          <td>
            @php $porc = $totalIngresos > 0 ? round(($prod->ingresos / $totalIngresos) * 100, 1) : 0; @endphp
            <div style="display:flex; align-items:center; gap:8px">
              <div style="flex:1; background:#e2e8f0; height:7px; border-radius:4px; overflow:hidden">
                <div style="width:{{ min(100, $porc) }}%; background:#3b82f6; height:100%"></div>
              </div>
              <span style="font-size:0.8rem; font-weight:700; width:38px">{{ $porc }}%</span>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="5" class="text-center text-muted" style="padding:2rem">Sin ventas registradas en el período.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4/dist/chart.umd.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
  const isDarkMode = document.body.classList.contains('dark-mode') || document.documentElement.classList.contains('dark-mode');
  const textColor = isDarkMode ? '#cbd5e1' : '#475569';
  const gridColor = isDarkMode ? 'rgba(255,255,255,0.08)' : 'rgba(0,0,0,0.06)';

  // 1. Tendencia de Ventas
  const vDias = @json($ventasPorDia);
  new Chart(document.getElementById('chartTendenciaVentas'), {
    type: 'line',
    data: {
      labels: vDias.map(r => r.fecha),
      datasets: [{
        label: 'Ventas ($)',
        data: vDias.map(r => parseFloat(r.total) || 0),
        borderColor: '#38bdf8',
        backgroundColor: 'rgba(56, 189, 248, 0.12)',
        tension: 0.35,
        fill: true,
        pointRadius: 4,
        pointBackgroundColor: '#38bdf8'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            label: (ctx) => ' $' + parseFloat(ctx.raw).toFixed(2)
          }
        }
      },
      scales: {
        x: { ticks: { color: textColor }, grid: { color: gridColor } },
        y: {
          beginAtZero: true,
          ticks: { color: textColor, callback: (v) => '$' + v },
          grid: { color: gridColor }
        }
      }
    }
  });

  // 2. Métodos de Pago
  const mPago = @json($metodosPago);
  const coloresMetodos = {
    'efectivo': '#10b981',
    'tarjeta': '#3b82f6',
    'transferencia': '#8b5cf6',
    'credito': '#f59e0b'
  };
  new Chart(document.getElementById('chartMetodosPago'), {
    type: 'doughnut',
    data: {
      labels: mPago.map(r => r.metodo_pago ? r.metodo_pago.toUpperCase() : 'OTRO'),
      datasets: [{
        data: mPago.map(r => parseFloat(r.total) || 0),
        backgroundColor: mPago.map(r => coloresMetodos[r.metodo_pago] || '#64748b'),
        borderWidth: 2,
        borderColor: isDarkMode ? '#1e2433' : '#fff'
      }]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'bottom',
          labels: { color: textColor, boxWidth: 12, font: { weight: 600 } }
        },
        tooltip: {
          callbacks: {
            label: (ctx) => ' ' + ctx.label + ': $' + parseFloat(ctx.raw).toFixed(2)
          }
        }
      }
    }
  });

  // 3. Top 10 Productos
  const tProd = @json($topProductos);
  new Chart(document.getElementById('chartTopProductos'), {
    type: 'bar',
    data: {
      labels: tProd.map(r => r.nombre_producto.length > 18 ? r.nombre_producto.substring(0, 18) + '...' : r.nombre_producto),
      datasets: [{
        label: 'Unidades Vendidas',
        data: tProd.map(r => parseInt(r.total_vendido) || 0),
        backgroundColor: '#6366f1',
        borderRadius: 6
      }]
    },
    options: {
      indexAxis: 'y',
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false }
      },
      scales: {
        x: { beginAtZero: true, ticks: { color: textColor }, grid: { color: gridColor } },
        y: { ticks: { color: textColor }, grid: { display: false } }
      }
    }
  });

  // 4. Comparativa Mensual Ventas vs Compras
  const cMeses = @json($meses);
  new Chart(document.getElementById('chartComparativaMensual'), {
    type: 'bar',
    data: {
      labels: cMeses.map(r => r.periodo),
      datasets: [
        {
          label: 'Ventas ($)',
          data: cMeses.map(r => r.ventas),
          backgroundColor: '#10b981',
          borderRadius: 4
        },
        {
          label: 'Compras ($)',
          data: cMeses.map(r => r.compras),
          backgroundColor: '#ef4444',
          borderRadius: 4
        }
      ]
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: {
          position: 'top',
          labels: { color: textColor, font: { weight: 600 } }
        },
        tooltip: {
          callbacks: {
            label: (ctx) => ' ' + ctx.dataset.label + ': $' + parseFloat(ctx.raw).toFixed(2)
          }
        }
      },
      scales: {
        x: { ticks: { color: textColor }, grid: { display: false } },
        y: {
          beginAtZero: true,
          ticks: { color: textColor, callback: (v) => '$' + v },
          grid: { color: gridColor }
        }
      }
    }
  });
});
</script>
@endpush

@endsection
