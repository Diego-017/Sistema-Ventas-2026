<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>@yield('title','Dashboard') — DigitalsPos</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;500;600;700;800;900&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/app.css') }}">
@stack('head')
</head>
<body>
<div class="app-wrapper">

  <aside class="sidebar" id="sidebar">
    <a href="{{ route('dashboard') }}" class="sidebar-brand">
      <span class="brand-icon">🏪</span>
      <div>
        <div class="brand-name">{{ \App\Models\Configuracion::get('eslogan','DigitalsPos') }}</div>
        <div class="brand-sub">{{ \App\Models\Configuracion::get('nombre_negocio','Mi Negocio') }}</div>
      </div>
    </a>

    <nav class="sidebar-nav">
      <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active':'' }}">
        <span class="nav-icon">📊</span><span>Inicio</span>
      </a>

      {{-- VENTAS --}}
      <div class="nav-section">Ventas</div>
      <a href="{{ route('ventas.nueva') }}" class="nav-item {{ request()->routeIs('ventas.nueva') ? 'active':'' }}">
        <span class="nav-icon">➕</span><span>Agregar Ventas</span>
      </a>
      <a href="{{ route('ventas.index') }}" class="nav-item {{ request()->routeIs('ventas.index') ? 'active':'' }}">
        <span class="nav-icon">📋</span><span>Administrar Ventas</span>
      </a>
      @if(session('user.rol')==='admin')
      <a href="{{ route('ventas.vendedor') }}" class="nav-item {{ request()->routeIs('ventas.vendedor') ? 'active':'' }}">
        <span class="nav-icon">🏅</span><span>Ventas por Vendedor</span>
      </a>
      @endif

      {{-- COTIZACIONES --}}
      <div class="nav-section">Cotizaciones</div>
      <a href="{{ route('cotizaciones.nueva') }}" class="nav-item {{ request()->routeIs('cotizaciones.nueva') ? 'active':'' }}">
        <span class="nav-icon">📝</span><span>Nueva Cotización</span>
      </a>
      <a href="{{ route('cotizaciones.index') }}" class="nav-item {{ request()->routeIs('cotizaciones.index') ? 'active':'' }}">
        <span class="nav-icon">📄</span><span>Administrar Cotizaciones</span>
      </a>

      {{-- CLIENTES --}}
      <div class="nav-section">Clientes</div>
      <a href="{{ route('clientes.index') }}" class="nav-item {{ request()->routeIs('clientes.*') ? 'active':'' }}">
        <span class="nav-icon">👥</span><span>Administrar Cliente</span>
      </a>

      {{-- CUENTAS POR COBRAR --}}
      <div class="nav-section">Cuentas por Cobrar</div>
      <a href="{{ route('creditos.index') }}" class="nav-item {{ request()->routeIs('creditos.*') ? 'active':'' }}">
        <span class="nav-icon">💳</span><span>Administrar Créditos</span>
        @php $creditosVencidos = \App\Models\Credito::where('estado','vencido')->count(); @endphp
        @if($creditosVencidos > 0)<span class="badge-nav danger">{{ $creditosVencidos }}</span>@endif
      </a>

      {{-- PRODUCTOS --}}
      <div class="nav-section">Productos</div>
      <a href="{{ route('productos.index') }}" class="nav-item {{ request()->routeIs('productos.*') ? 'active':'' }}">
        <span class="nav-icon">📦</span><span>Administrar Productos</span>
        @php $stockBajoCount = \App\Models\Producto::activo()->lowStock()->count(); @endphp
        @if($stockBajoCount > 0)<span class="badge-nav danger">{{ $stockBajoCount }}</span>@endif
      </a>
      <a href="{{ route('categorias.index') }}" class="nav-item {{ request()->routeIs('categorias.*') ? 'active':'' }}">
        <span class="nav-icon">🏷️</span><span>Categorias</span>
      </a>
      <a href="{{ route('subcategorias.index') }}" class="nav-item {{ request()->routeIs('subcategorias.*') ? 'active':'' }}">
        <span class="nav-icon">🔖</span><span>Subcategorias</span>
      </a>

      {{-- INVENTARIO --}}
      <div class="nav-section">Inventario</div>
      <a href="{{ route('compras.index') }}" class="nav-item {{ request()->routeIs('compras.*') ? 'active':'' }}">
        <span class="nav-icon">🛒</span><span>Administrar Compras</span>
      </a>
      <a href="{{ route('inventario.stock') }}" class="nav-item {{ request()->is('inventario/stock') ? 'active':'' }}">
        <span class="nav-icon">📊</span><span>Consultar Stock</span>
      </a>
      <a href="{{ route('inventario.stock_lote') }}" class="nav-item {{ request()->is('inventario/stock-lote') ? 'active':'' }}">
        <span class="nav-icon">🔢</span><span>Consulta de Stock Lote</span>
      </a>
      <a href="{{ route('inventario.traslados') }}" class="nav-item {{ request()->is('inventario/traslados') ? 'active':'' }}">
        <span class="nav-icon">🔄</span><span>Administrar Traslados</span>
      </a>

      {{-- PROVEEDORES --}}
      <div class="nav-section">Proveedores</div>
      <a href="{{ route('proveedores.index') }}" class="nav-item {{ request()->routeIs('proveedores.*') ? 'active':'' }}">
        <span class="nav-icon">🏭</span><span>Administrar Proveedor</span>
      </a>

      {{-- IMPRESORAS --}}
      @if(session('user.rol')==='admin')
      <div class="nav-section">Impresoras</div>
      <a href="{{ route('impresoras.index') }}" class="nav-item {{ request()->routeIs('impresoras.*') ? 'active':'' }}">
        <span class="nav-icon">🖨️</span><span>Administrar Impresoras</span>
      </a>
      @endif

      {{-- CAJAS --}}
      <div class="nav-section">Cajas</div>
      <a href="{{ route('caja.index') }}" class="nav-item {{ request()->is('caja') ? 'active':'' }}">
        <span class="nav-icon">💰</span><span>Administrar Cajas</span>
        @if(\App\Models\Caja::abierta())<span class="badge-nav success">Abierta</span>@endif
      </a>
      <a href="{{ route('caja.historial') }}" class="nav-item {{ request()->is('caja/historial') ? 'active':'' }}">
        <span class="nav-icon">📋</span><span>Administrar Corte</span>
      </a>

      @if(session('user.rol') === 'admin')
      {{-- REPORTES --}}
      <div class="nav-section">Reportes</div>
      <a href="{{ route('reportes.hoja_conteo') }}" class="nav-item {{ request()->is('reportes/hoja*') ? 'active':'' }}">
        <span class="nav-icon">📝</span><span>Hoja de Conteo</span>
      </a>
      <a href="{{ route('reportes.index') }}" class="nav-item {{ request()->is('reportes') ? 'active':'' }}">
        <span class="nav-icon">📈</span><span>Reportes</span>
      </a>

      {{-- ADMIN --}}
      <div class="nav-section">Administración</div>
      <a href="{{ route('usuarios.index') }}" class="nav-item {{ request()->routeIs('usuarios.*') ? 'active':'' }}">
        <span class="nav-icon">👤</span><span>Usuarios</span>
      </a>
      <a href="{{ route('configuracion.index') }}" class="nav-item {{ request()->routeIs('configuracion.*') ? 'active':'' }}">
        <span class="nav-icon">⚙️</span><span>Configuración</span>
      </a>
      @endif
    </nav>

    <div class="sidebar-user">
      <div class="user-info">
        <span class="user-avatar">{{ strtoupper(substr(session('user.nombre','U'),0,1)) }}</span>
        <div>
          <div class="user-name">{{ session('user.nombre') }}</div>
          <div class="user-role">{{ session('user.rol') }}</div>
        </div>
      </div>
      <div class="user-actions">
        <a href="{{ route('perfil') }}" class="btn-profile">Mi Perfil</a>
        <a href="{{ route('logout') }}" class="btn-logout">Salir</a>
      </div>
    </div>
  </aside>

  <main class="main-content">
    <header class="top-bar">
      <button class="sidebar-toggle" id="sidebarToggle" onclick="toggleSidebar()">☰</button>
      <div class="top-bar-right">
        <span class="top-date">🕐 {{ now()->format('d/m/Y H:i') }}</span>
        @if(\App\Models\Caja::abierta())
          <span class="caja-badge open">🟢 Caja Abierta</span>
        @else
          <span class="caja-badge closed">🔴 Sin Caja</span>
        @endif
      </div>
    </header>

    <div class="content-area">
      @if(session('success'))
        <div class="alert alert-success" id="flash-msg">
          <span>✅ {{ session('success') }}</span>
          <button onclick="this.parentElement.remove()">✕</button>
        </div>
      @endif
      @if(session('error'))
        <div class="alert alert-error" id="flash-msg">
          <span>❌ {{ session('error') }}</span>
          <button onclick="this.parentElement.remove()">✕</button>
        </div>
      @endif
      @if($errors->any())
        <div class="alert alert-error">
          <ul style="margin:0;padding-left:16px">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
          </ul>
        </div>
      @endif

      @yield('content')
    </div>
  </main>
</div>
<script src="{{ asset('js/app.js') }}"></script>
@stack('scripts')
</body>
</html>
