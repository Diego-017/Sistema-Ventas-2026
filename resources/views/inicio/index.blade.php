@extends('layouts.main')
@section('title', (session('user.rol') ?? (is_array(session('user')) ? (session('user')['rol'] ?? '') : (session('user')->rol ?? ''))) === 'admin' ? 'Panel de Administración' : 'Panel de Ventas')
@section('content')

@php
  $esAdmin = (session('user.rol') ?? (is_array(session('user')) ? (session('user')['rol'] ?? '') : (session('user')->rol ?? ''))) === 'admin';
@endphp

{{-- Encabezado Principal estilo Software Empresarial --}}
<div class="page-header" style="margin-bottom: 20px">
  <div>
    <h1 style="color: #38bdf8; font-size: 1.75rem; font-weight: 800; margin:0">
      {{ $esAdmin ? 'Panel de Administración' : 'Panel de Ventas' }}
    </h1>
    <p class="page-sub" style="color: #94a3b8; margin-top:4px">Bienvenido, {{ session('user.nombre') }} — {{ now()->translatedFormat('l, d \\de F Y') }}</p>
  </div>
  <div style="display:flex; align-items:center; gap:10px">
    @if($esAdmin)
    <a href="{{ route('reportes.estadisticas') }}" class="btn btn-primary" style="display:flex; align-items:center; gap:6px; font-weight:700">
      <i class="fa-solid fa-chart-pie"></i> Ver Gráficas y Estadísticas
    </a>
    @endif
    <span class="date-badge" style="background:#282d3c; color:#38bdf8; border:1px solid #363d50; padding:6px 12px; border-radius:6px; font-weight:700">
      <i class="fa-regular fa-clock" style="margin-right:4px"></i> {{ now()->format('H:i:s') }}
    </span>
  </div>
</div>

{{-- ── 6 GRANDES BLOQUES DE ACCESO RÁPIDO (Exacto a la Imagen 2 de referencia) ── --}}
<div class="enterprise-tiles-grid" style="margin-bottom: 24px">
  {{-- 1. Venta (POS) --}}
  <a href="{{ route('ventas.nueva') }}" class="ent-tile tile-orange">
    <div class="ent-tile-title">Venta</div>
    <div class="ent-tile-sub">POS</div>
    <span class="ent-tile-icon"><i class="fa-solid fa-cart-shopping"></i></span>
  </a>

  {{-- 2. Clientes --}}
  <a href="{{ route('clientes.index') }}" class="ent-tile tile-green">
    <div class="ent-tile-title">Clientes</div>
    <div class="ent-tile-sub">Administración</div>
    <span class="ent-tile-icon"><i class="fa-solid fa-users"></i></span>
  </a>

  {{-- 3. Productos --}}
  <a href="{{ route('productos.index') }}" class="ent-tile tile-cyan">
    <div class="ent-tile-title">Productos</div>
    <div class="ent-tile-sub">Administración</div>
    <span class="ent-tile-icon"><i class="fa-solid fa-box-open"></i></span>
  </a>

  {{-- 4. Creditos --}}
  <a href="{{ route('creditos.index') }}" class="ent-tile tile-dark">
    <div class="ent-tile-title">Creditos</div>
    <div class="ent-tile-sub">Administración</div>
    <span class="ent-tile-icon"><i class="fa-solid fa-credit-card"></i></span>
  </a>

  {{-- 5. Stock --}}
  <a href="{{ route('inventario.stock') }}" class="ent-tile tile-slate">
    <div class="ent-tile-title">Stock</div>
    <div class="ent-tile-sub">Consultar</div>
    <span class="ent-tile-icon"><i class="fa-solid fa-file-invoice-dollar"></i></span>
  </a>

  {{-- 6. Corte Caja --}}
  <a href="{{ route('caja.administrar_corte') }}" class="ent-tile tile-red">
    <div class="ent-tile-title">Corte Caja</div>
    <div class="ent-tile-sub">Administración</div>
    <span class="ent-tile-icon"><i class="fa-solid fa-cash-register"></i></span>
  </a>
</div>

{{-- EXCLUSIVO PARA ADMINISTRADOR: Métricas globales, supervisor y estadísticas --}}
@if($esAdmin)
{{-- ── CONTENIDO DEL SISTEMA (Exacto a la Imagen 3 de referencia) ── --}}
<div style="margin-bottom: 22px">
  <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:12px">
    <h3 style="font-size:1.05rem; font-weight:800; color:var(--text-color, #1e293b); margin:0; display:flex; align-items:center; gap:8px">
      <i class="fa-solid fa-folder-open" style="color:#eab308"></i> Contenido del Sistema
    </h3>
    <span style="font-size:0.8rem; color:#64748b">Métricas y catálogos globales</span>
  </div>

  <div class="system-counters-grid">
    {{-- 1. Usuarios Registrados (Amarillo) --}}
    <div class="sys-card sys-yellow">
      <div class="sys-card-body">
        <div class="sys-card-left">
          <div class="sys-count">{{ $totalUsuarios }}</div>
          <div class="sys-label">Usuarios Registrados</div>
        </div>
        <div class="sys-watermark"><i class="fa-solid fa-user-plus"></i></div>
      </div>
      <a href="{{ route('usuarios.index') }}" class="sys-card-footer">
        <span>Más detalle</span> <i class="fa-solid fa-arrow-right" style="margin-left:4px"></i>
      </a>
    </div>

    {{-- 2. Roles Registrados (Cyan) --}}
    <div class="sys-card sys-cyan">
      <div class="sys-card-body">
        <div class="sys-card-left">
          <div class="sys-count">{{ $totalRoles }}</div>
          <div class="sys-label">Roles Registrados</div>
        </div>
        <div class="sys-watermark"><i class="fa-solid fa-id-card"></i></div>
      </div>
      <a href="{{ route('usuarios.index') }}" class="sys-card-footer">
        <span>Más detalle</span> <i class="fa-solid fa-arrow-right" style="margin-left:4px"></i>
      </a>
    </div>

    {{-- 3. Categorías Registradas (Verde) --}}
    <div class="sys-card sys-green">
      <div class="sys-card-body">
        <div class="sys-card-left">
          <div class="sys-count">{{ $totalCategorias }}</div>
          <div class="sys-label">Categorías Registradas</div>
        </div>
        <div class="sys-watermark"><i class="fa-solid fa-tags"></i></div>
      </div>
      <a href="{{ route('categorias.index') }}" class="sys-card-footer">
        <span>Más detalle</span> <i class="fa-solid fa-arrow-right" style="margin-left:4px"></i>
      </a>
    </div>

    {{-- 4. Productos Registrados (Azul) --}}
    <div class="sys-card sys-blue">
      <div class="sys-card-body">
        <div class="sys-card-left">
          <div class="sys-count">{{ $totalProductos }}</div>
          <div class="sys-label">Productos Registrados</div>
        </div>
        <div class="sys-watermark"><i class="fa-solid fa-boxes-stacked"></i></div>
      </div>
      <a href="{{ route('productos.index') }}" class="sys-card-footer">
        <span>Más detalle</span> <i class="fa-solid fa-arrow-right" style="margin-left:4px"></i>
      </a>
    </div>

    {{-- 5. Proveedores Registrados (Gris Oscuro) --}}
    <div class="sys-card sys-dark">
      <div class="sys-card-body">
        <div class="sys-card-left">
          <div class="sys-count">{{ $totalProveedores }}</div>
          <div class="sys-label">Proveedores Registrados</div>
        </div>
        <div class="sys-watermark"><i class="fa-solid fa-truck"></i></div>
      </div>
      <a href="{{ route('proveedores.index') }}" class="sys-card-footer">
        <span>Más detalle</span> <i class="fa-solid fa-arrow-right" style="margin-left:4px"></i>
      </a>
    </div>

    {{-- 6. Compras Registradas (Rojo) --}}
    <div class="sys-card sys-red">
      <div class="sys-card-body">
        <div class="sys-card-left">
          <div class="sys-count">{{ $totalCompras }}</div>
          <div class="sys-label">Compras Registradas</div>
        </div>
        <div class="sys-watermark"><i class="fa-solid fa-cart-arrow-down"></i></div>
      </div>
      <a href="{{ route('compras.index') }}" class="sys-card-footer">
        <span>Más detalle</span> <i class="fa-solid fa-arrow-right" style="margin-left:4px"></i>
      </a>
    </div>
  </div>
</div>

{{-- ── BANNER DIRECTO AL MÓDULO SEPARADO DE ANÁLISIS Y ESTADÍSTICAS ── --}}
<div class="analytics-banner-card" style="margin-bottom: 22px">
  <div class="analytics-banner-content">
    <div class="analytics-banner-icon"><i class="fa-solid fa-chart-column"></i></div>
    <div>
      <h3 style="margin:0 0 4px; font-size:1.15rem; font-weight:800; color:#fff">
        Módulo Especializado: Análisis de Gráficas y Estadísticas
      </h3>
      <p style="margin:0; font-size:0.88rem; color:rgba(255,255,255,0.85)">
        Consulta tendencias de ventas, comparativas mensuales, rentabilidad, márgenes de ganancia y los productos más vendidos en un módulo dedicado de alta resolución.
      </p>
    </div>
  </div>
  <a href="{{ route('reportes.estadisticas') }}" class="analytics-banner-btn">
    Abrir Módulo de Estadísticas <i class="fa-solid fa-arrow-right" style="margin-left:6px"></i>
  </a>
</div>

{{-- KPIs de Ventas y Financieros --}}
<div class="stats-grid" style="margin-bottom: 20px">
  <div class="stat-card stat-success">
    <div class="stat-icon-wrap"><i class="fa-solid fa-dollar-sign"></i></div>
    <div class="stat-info">
      <div class="stat-value">${{ number_format($totalHoy,2) }}</div>
      <div class="stat-label">Ventas Hoy</div>
      <div class="stat-sub">{{ $ventasHoy }} transacciones</div>
    </div>
  </div>
  <div class="stat-card stat-primary">
    <div class="stat-icon-wrap"><i class="fa-solid fa-calendar-days"></i></div>
    <div class="stat-info">
      <div class="stat-value">${{ number_format($ventasMes,2) }}</div>
      <div class="stat-label">Ventas del Mes</div>
      <div class="stat-sub {{ $variacion >= 0 ? 'text-success':'text-danger' }}">
        {{ $variacion >= 0 ? '▲':'▼' }} {{ abs($variacion) }}% vs mes anterior
      </div>
    </div>
  </div>
  <div class="stat-card stat-purple">
    <div class="stat-icon-wrap"><i class="fa-solid fa-arrow-trend-up"></i></div>
    <div class="stat-info">
      <div class="stat-value">${{ number_format($margenHoy,2) }}</div>
      <div class="stat-label">Margen Hoy</div>
      <div class="stat-sub">Ganancia estimada</div>
    </div>
  </div>
  <div class="stat-card stat-warning">
    <div class="stat-icon-wrap"><i class="fa-solid fa-bag-shopping"></i></div>
    <div class="stat-info">
      <div class="stat-value">${{ number_format($gastoHoy,2) }}</div>
      <div class="stat-label">Compras Hoy</div>
      <div class="stat-sub"><a href="{{ route('compras.index') }}" style="color:inherit">Ver compras →</a></div>
    </div>
  </div>
</div>

<div class="stats-grid" style="grid-template-columns:1fr 1fr 1fr 1fr; margin-bottom: 22px">
  <div class="stat-card stat-danger">
    <div class="stat-icon-wrap"><i class="fa-solid fa-triangle-exclamation"></i></div>
    <div class="stat-info">
      <div class="stat-value">{{ count($stockBajo) }}</div>
      <div class="stat-label">Stock Bajo</div>
      <div class="stat-sub"><a href="{{ route('inventario.stock') }}" style="color:inherit">Revisar inventario →</a></div>
    </div>
  </div>
  <div class="stat-card stat-warning">
    <div class="stat-icon-wrap"><i class="fa-solid fa-credit-card"></i></div>
    <div class="stat-info">
      <div class="stat-value">{{ $creditosPendientes }}</div>
      <div class="stat-label">Créditos Pendientes</div>
      <div class="stat-sub"><a href="{{ route('creditos.index') }}" style="color:inherit">Ver créditos →</a></div>
    </div>
  </div>
  <div class="stat-card stat-danger">
    <div class="stat-icon-wrap"><i class="fa-solid fa-bell"></i></div>
    <div class="stat-info">
      <div class="stat-value">{{ $creditosVencidos }}</div>
      <div class="stat-label">Créditos Vencidos</div>
      <div class="stat-sub"><a href="{{ route('creditos.index') }}" style="color:inherit">Gestionar cobro →</a></div>
    </div>
  </div>
  <div class="stat-card stat-info">
    <div class="stat-icon-wrap"><i class="fa-solid fa-file-lines"></i></div>
    <div class="stat-info">
      <div class="stat-value">{{ $cotizacionesVigentes }}</div>
      <div class="stat-label">Cotizaciones Vigentes</div>
      <div class="stat-sub"><a href="{{ route('cotizaciones.index') }}" style="color:inherit">Ver cotizaciones →</a></div>
    </div>
  </div>
</div>

{{-- AUDITORÍA EN TIEMPO REAL / FEED DEL ADMINISTRADOR --}}
@if(isset($actividadesRecientes) && $actividadesRecientes->count() > 0)
<div class="card" style="border-radius:10px; margin-bottom:22px; border-left: 5px solid #2563eb;">
  <div class="card-header" style="display:flex; justify-content:space-between; align-items:center; padding:12px 18px">
    <h3 style="margin:0; font-size:1.05rem; font-weight:800; display:flex; align-items:center; gap:8px;">
      <i class="fa-solid fa-shield-halved" style="color:#2563eb"></i> Actividad Reciente en Tiempo Real (Panel de Supervisión)
    </h3>
    <span style="font-size:0.75rem; color:#64748b; background:rgba(0,0,0,0.06); padding:3px 8px; border-radius:4px; font-weight:700;">Supervisión Global</span>
  </div>

  <div style="overflow-x:auto;">
    <table class="table" style="width:100%; font-size:0.85rem; margin:0">
      <thead>
        <tr>
          <th>Hora</th>
          <th>Usuario</th>
          <th>Módulo</th>
          <th>Acción</th>
          <th>Detalle</th>
          <th>Monto</th>
        </tr>
      </thead>
      <tbody>
        @foreach($actividadesRecientes as $act)
        <tr>
          <td style="color:#64748b; font-weight:600;">{{ $act->created_at->format('H:i:s') }}</td>
          <td style="font-weight:700;">{{ $act->usuario->nombre ?? 'Sistema' }}</td>
          <td><span class="badge badge-info" style="font-size:0.75rem;">{{ $act->modulo }}</span></td>
          <td style="font-weight:600;">{{ $act->accion }}</td>
          <td>{{ $act->detalle }}</td>
          <td style="font-weight:800; color:#16a34a;">
            {{ $act->monto ? '$'.number_format($act->monto, 2) : '—' }}
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>
@endif

<div class="grid-2" style="margin-bottom: 22px">
  <div class="card">
    <div class="card-header">
      <h3 style="display:flex; align-items:center; gap:8px"><i class="fa-solid fa-receipt" style="color:#38bdf8"></i> Últimas Ventas</h3>
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
          <tr><td colspan="4" class="text-center text-muted" style="padding:1.5rem">Sin ventas hoy</td></tr>
          @endforelse
        </tbody>
      </table>
    </div>
  </div>

  <div class="card">
    <div class="card-header">
      <h3 style="display:flex; align-items:center; gap:8px"><i class="fa-solid fa-triangle-exclamation" style="color:#ef4444"></i> Productos con Stock Bajo</h3>
      <a href="{{ route('compras.crear') }}" class="btn btn-sm btn-primary">+ Compra</a>
    </div>
    <div class="card-body p-0">
      @if($stockBajo->isEmpty())
        <div class="empty-state" style="padding:2rem; text-align:center"><i class="fa-solid fa-circle-check" style="color:#10b981; margin-right:6px"></i> Todos los productos tienen stock suficiente.</div>
      @else
      <table class="table">
        <thead><tr><th>Producto</th><th>Stock</th><th>Mínimo</th><th></th></tr></thead>
        <tbody>
          @foreach($stockBajo->take(6) as $p)
          <tr class="row-danger">
            <td>{{ $p->nombre }}</td>
            <td><span class="badge badge-danger">{{ $p->stock }}</span></td>
            <td>{{ $p->stock_minimo }}</td>
            <td><a href="{{ route('productos.editar',$p->id) }}" class="btn btn-sm btn-light" title="Editar"><i class="fa-solid fa-pen-to-square"></i></a></td>
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
    <h3 style="display:flex; align-items:center; gap:8px"><i class="fa-solid fa-vault" style="color:#10b981"></i> Caja Actual — <span class="badge badge-success">ABIERTA</span></h3>
    <a href="{{ route('caja.administrar_corte') }}" class="btn btn-sm btn-light">Gestionar Caja / Corte</a>
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
<div class="alert alert-warning" style="display:flex; align-items:center; justify-content:space-between">
  <div>
    <i class="fa-solid fa-triangle-exclamation" style="margin-right:6px"></i> No hay caja abierta. Para registrar ventas en este turno, primero realiza la apertura.
  </div>
  <a href="{{ route('caja.administrar_corte') }}" class="btn btn-sm btn-primary">Realizar Apertura</a>
</div>
@endif

@endif {{-- Fin @if($esAdmin) --}}

<footer class="app-footer" style="text-align:center; padding:18px 0; color:#94a3b8; font-size:0.8rem; margin-top:30px">
  Copyright © {{ date('Y') }} <strong>DigitalsMindsSystems</strong>. Todos los derechos reservados.
</footer>

@endsection
