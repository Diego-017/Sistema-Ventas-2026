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

    <nav class="sidebar-nav" id="sidebarNav">
      <a href="{{ route('dashboard') }}" class="nav-item nav-top {{ request()->routeIs('dashboard') ? 'active':'' }}">
        <span class="nav-icon">📊</span><span class="nav-label">Inicio</span>
      </a>

      @php
        $stockBajoCount   = \App\Models\Producto::activo()->lowStock()->count();
        $creditosVencidos = \App\Models\Credito::where('estado','vencido')->count();
        $cajaAbiertaNav   = \App\Models\Caja::abierta();
        $esAdmin          = session('user.rol') === 'admin';

        // Determina qué grupo debe abrirse automáticamente según la ruta activa
        $openVentas      = request()->routeIs(['ventas.*']);
        $openCotizaciones= request()->routeIs('cotizaciones.*');
        $openClientes    = request()->routeIs('clientes.*');
        $openCreditos    = request()->routeIs('creditos.*');
        $openProductos   = request()->routeIs(['productos.*','categorias.*','subcategorias.*']);
        $openInventario  = request()->routeIs(['compras.*','inventario.*']);
        $openProveedores = request()->routeIs('proveedores.*');
        $openImpresoras  = request()->routeIs('impresoras.*');
        $openCajas       = request()->is('caja*');
        $openReportes    = request()->routeIs('reportes.*');
        $openAdmin       = request()->routeIs(['usuarios.*','configuracion.*']);
      @endphp

      {{-- VENTAS --}}
      <div class="nav-group {{ $openVentas ? 'open':'' }}" data-group="ventas">
        <button type="button" class="nav-group-header {{ $openVentas ? 'active':'' }}" onclick="toggleNavGroup(this)">
          <span class="nav-icon">🧾</span><span class="nav-label">Ventas</span>
          <span class="nav-chevron">❯</span>
        </button>
        <div class="nav-group-wrap"><div class="nav-group-body">
          <a href="{{ route('ventas.nueva') }}" class="nav-subitem {{ request()->routeIs('ventas.nueva') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Agregar Ventas</span>
          </a>
          <a href="{{ route('ventas.index') }}" class="nav-subitem {{ request()->routeIs(['ventas.index','ventas.ver']) ? 'active':'' }}">
            <span class="nav-dot"></span><span>Administrar Ventas</span>
          </a>
          @if($esAdmin)
          <a href="{{ route('ventas.vendedor') }}" class="nav-subitem {{ request()->routeIs('ventas.vendedor') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Ventas por Vendedor</span>
          </a>
          @endif
        </div></div>
      </div>

      {{-- COTIZACIONES --}}
      <div class="nav-group {{ $openCotizaciones ? 'open':'' }}" data-group="cotizaciones">
        <button type="button" class="nav-group-header {{ $openCotizaciones ? 'active':'' }}" onclick="toggleNavGroup(this)">
          <span class="nav-icon">📝</span><span class="nav-label">Cotizaciones</span>
          <span class="nav-chevron">❯</span>
        </button>
        <div class="nav-group-wrap"><div class="nav-group-body">
          <a href="{{ route('cotizaciones.nueva') }}" class="nav-subitem {{ request()->routeIs('cotizaciones.nueva') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Nueva Cotización</span>
          </a>
          <a href="{{ route('cotizaciones.index') }}" class="nav-subitem {{ request()->routeIs(['cotizaciones.index','cotizaciones.show']) ? 'active':'' }}">
            <span class="nav-dot"></span><span>Administrar Cotizaciones</span>
          </a>
        </div></div>
      </div>

      {{-- CLIENTES --}}
      <div class="nav-group {{ $openClientes ? 'open':'' }}" data-group="clientes">
        <button type="button" class="nav-group-header {{ $openClientes ? 'active':'' }}" onclick="toggleNavGroup(this)">
          <span class="nav-icon">👥</span><span class="nav-label">Clientes</span>
          <span class="nav-chevron">❯</span>
        </button>
        <div class="nav-group-wrap"><div class="nav-group-body">
          <a href="{{ route('clientes.index') }}" class="nav-subitem {{ request()->routeIs('clientes.*') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Administrar Cliente</span>
          </a>
        </div></div>
      </div>

      {{-- CUENTAS POR COBRAR --}}
      <div class="nav-group {{ $openCreditos ? 'open':'' }}" data-group="creditos">
        <button type="button" class="nav-group-header {{ $openCreditos ? 'active':'' }}" onclick="toggleNavGroup(this)">
          <span class="nav-icon">💳</span><span class="nav-label">Cuentas por Cobrar</span>
          @if($creditosVencidos > 0)<span class="badge-nav danger">{{ $creditosVencidos }}</span>@endif
          <span class="nav-chevron">❯</span>
        </button>
        <div class="nav-group-wrap"><div class="nav-group-body">
          <a href="{{ route('creditos.index') }}" class="nav-subitem {{ request()->routeIs('creditos.*') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Administrar Créditos</span>
          </a>
        </div></div>
      </div>

      {{-- PRODUCTOS --}}
      <div class="nav-group {{ $openProductos ? 'open':'' }}" data-group="productos">
        <button type="button" class="nav-group-header {{ $openProductos ? 'active':'' }}" onclick="toggleNavGroup(this)">
          <span class="nav-icon">🛍️</span><span class="nav-label">Productos</span>
          @if($stockBajoCount > 0)<span class="badge-nav danger">{{ $stockBajoCount }}</span>@endif
          <span class="nav-chevron">❯</span>
        </button>
        <div class="nav-group-wrap"><div class="nav-group-body">
          <a href="{{ route('productos.index') }}" class="nav-subitem {{ request()->routeIs('productos.*') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Administrar Productos</span>
          </a>
          <a href="{{ route('categorias.index') }}" class="nav-subitem {{ request()->routeIs('categorias.*') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Categorias</span>
          </a>
          <a href="{{ route('subcategorias.index') }}" class="nav-subitem {{ request()->routeIs('subcategorias.*') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Subcategorias</span>
          </a>
        </div></div>
      </div>

      {{-- INVENTARIO --}}
      <div class="nav-group {{ $openInventario ? 'open':'' }}" data-group="inventario">
        <button type="button" class="nav-group-header {{ $openInventario ? 'active':'' }}" onclick="toggleNavGroup(this)">
          <span class="nav-icon">➕</span><span class="nav-label">Inventario</span>
          <span class="nav-chevron">❯</span>
        </button>
        <div class="nav-group-wrap"><div class="nav-group-body">
          <a href="{{ route('compras.index') }}" class="nav-subitem {{ request()->routeIs('compras.*') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Administrar Compras</span>
          </a>
          <a href="{{ route('inventario.stock') }}" class="nav-subitem {{ request()->is('inventario/stock') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Consultar Stock</span>
          </a>
          <a href="{{ route('inventario.stock_lote') }}" class="nav-subitem {{ request()->is('inventario/stock-lote') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Consulta de Stock Lote</span>
          </a>
          <a href="{{ route('inventario.traslados') }}" class="nav-subitem {{ request()->is('inventario/traslados') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Administrar Traslados</span>
          </a>
        </div></div>
      </div>

      {{-- PROVEEDORES --}}
      <div class="nav-group {{ $openProveedores ? 'open':'' }}" data-group="proveedores">
        <button type="button" class="nav-group-header {{ $openProveedores ? 'active':'' }}" onclick="toggleNavGroup(this)">
          <span class="nav-icon">🏭</span><span class="nav-label">Proveedores</span>
          <span class="nav-chevron">❯</span>
        </button>
        <div class="nav-group-wrap"><div class="nav-group-body">
          <a href="{{ route('proveedores.index') }}" class="nav-subitem {{ request()->routeIs('proveedores.*') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Administrar Proveedor</span>
          </a>
        </div></div>
      </div>

      @if($esAdmin)
      {{-- IMPRESORAS --}}
      <div class="nav-group {{ $openImpresoras ? 'open':'' }}" data-group="impresoras">
        <button type="button" class="nav-group-header {{ $openImpresoras ? 'active':'' }}" onclick="toggleNavGroup(this)">
          <span class="nav-icon">🖨️</span><span class="nav-label">Impresoras</span>
          <span class="nav-chevron">❯</span>
        </button>
        <div class="nav-group-wrap"><div class="nav-group-body">
          <a href="{{ route('impresoras.index') }}" class="nav-subitem {{ request()->routeIs('impresoras.*') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Administrar Impresoras</span>
          </a>
        </div></div>
      </div>
      @endif

      {{-- CAJAS --}}
      <div class="nav-group {{ $openCajas ? 'open':'' }}" data-group="cajas">
        <button type="button" class="nav-group-header {{ $openCajas ? 'active':'' }}" onclick="toggleNavGroup(this)">
          <span class="nav-icon">🧮</span><span class="nav-label">Cajas</span>
          @if($cajaAbiertaNav)<span class="badge-nav success">Abierta</span>@endif
          <span class="nav-chevron">❯</span>
        </button>
        <div class="nav-group-wrap"><div class="nav-group-body">
          <a href="{{ route('caja.index') }}" class="nav-subitem {{ request()->is('caja') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Administrar Cajas</span>
          </a>
          <a href="{{ route('caja.historial') }}" class="nav-subitem {{ request()->is('caja/historial') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Administrar Corte</span>
          </a>
          <a href="{{ route('caja.movimientos') }}" class="nav-subitem {{ request()->is('caja/movimientos') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Administrar Movimientos</span>
          </a>
        </div></div>
      </div>

      @if($esAdmin)
      {{-- REPORTES --}}
      <div class="nav-group {{ $openReportes ? 'open':'' }}" data-group="reportes">
        <button type="button" class="nav-group-header {{ $openReportes ? 'active':'' }}" onclick="toggleNavGroup(this)">
          <span class="nav-icon">📄</span><span class="nav-label">Reportes</span>
          <span class="nav-chevron">❯</span>
        </button>
        <div class="nav-group-wrap"><div class="nav-group-body">
          <a href="{{ route('reportes.hoja_conteo') }}" class="nav-subitem {{ request()->is('reportes/hoja*') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Hoja de Conteo</span>
          </a>
          <a href="{{ route('reportes.kardex.buscar') }}" class="nav-subitem {{ request()->is('reportes/kardex*') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Reporte Kardex</span>
          </a>
          <a href="{{ route('reportes.index') }}" class="nav-subitem {{ request()->is('reportes') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Reportes Generales</span>
          </a>
        </div></div>
      </div>

      {{-- ADMINISTRACIÓN --}}
      <div class="nav-group {{ $openAdmin ? 'open':'' }}" data-group="admin">
        <button type="button" class="nav-group-header {{ $openAdmin ? 'active':'' }}" onclick="toggleNavGroup(this)">
          <span class="nav-icon">⚙️</span><span class="nav-label">Administración</span>
          <span class="nav-chevron">❯</span>
        </button>
        <div class="nav-group-wrap"><div class="nav-group-body">
          <a href="{{ route('usuarios.index') }}" class="nav-subitem {{ request()->routeIs('usuarios.*') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Usuarios</span>
          </a>
          <a href="{{ route('configuracion.index') }}" class="nav-subitem {{ request()->routeIs('configuracion.*') ? 'active':'' }}">
            <span class="nav-dot"></span><span>Configuración</span>
          </a>
        </div></div>
      </div>
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
