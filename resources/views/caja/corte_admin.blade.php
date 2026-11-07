@extends('layouts.main')
@section('title', 'Corte caja')

@section('content')
<div class="corte-admin-container">
  {{-- Header con Icono y Breadcrumb exacto a la imagen de referencia --}}
  <div class="corte-page-header">
    <div class="corte-title-wrap">
      <span class="corte-main-icon"><i class="fa-solid fa-trash"></i></span>
      <h1 class="corte-main-title">Corte caja</h1>
    </div>
    <div class="corte-breadcrumb">
      <a href="{{ route('dashboard') }}">Inicio</a> <span>/</span> <span class="active">Corte caja</span>
    </div>
  </div>

  @if(session('success'))
    <div class="alert alert-success" style="background:#10b98122; border:1px solid #10b981; color:#6ee7b7; padding:12px 16px; border-radius:8px; margin-bottom:18px">
      <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
    </div>
  @endif

  @if(session('error'))
    <div class="alert alert-danger" style="background:#ef444422; border:1px solid #ef4444; color:#fca5a5; padding:12px 16px; border-radius:8px; margin-bottom:18px">
      <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
    </div>
  @endif

  @if(session('warning'))
    <div class="alert alert-warning" style="background:#f59e0b22; border:1px solid #f59e0b; color:#fcd34d; padding:12px 16px; border-radius:8px; margin-bottom:18px">
      <i class="fa-solid fa-circle-exclamation"></i> {{ session('warning') }}
    </div>
  @endif

  {{-- Card Principal Empresarial --}}
  <div class="corte-enterprise-card">
    <form method="GET" action="{{ route('caja.administrar_corte') }}" id="filtroCorteForm">
      <div class="corte-selectors-row">
        {{-- Selector Sucursal --}}
        <div class="corte-select-group">
          <select name="sucursal" class="corte-select" onchange="document.getElementById('filtroCorteForm').submit()">
            @foreach($sucursales as $s)
              <option value="{{ $s }}" {{ $sucursalSeleccionada === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
        </div>

        {{-- Selector Caja --}}
        <div class="corte-select-group">
          <select name="caja_id" class="corte-select" onchange="document.getElementById('filtroCorteForm').submit()">
            @foreach($cajas as $c)
              <option value="{{ $c->id }}" {{ $cajaActual && $cajaActual->id == $c->id ? 'selected' : '' }}>
                {{ $c->nombre }} ({{ ucfirst($c->estado) }})
              </option>
            @endforeach
          </select>
        </div>
      </div>
    </form>

    {{-- Panel de Estado y Acciones --}}
    @if(!$cajaActual || $cajaActual->estado === 'cerrada')
      {{-- ESTADO: SIN APERTURA DE CAJA --}}
      <div class="corte-status-panel">
        <div class="corte-status-badge-row">
          <span class="badge-sin-apertura">Sin apertura de caja</span>
        </div>
        <div class="corte-action-btn-row">
          <a href="{{ route('caja.form_apertura') }}?sucursal={{ urlencode($sucursalSeleccionada) }}&caja_id={{ $cajaActual->id ?? '' }}"
             class="btn-realizar-apertura" style="text-decoration:none">
            <i class="fa-solid fa-lock-open" style="margin-right:6px"></i> Realizar Apertura
          </a>
        </div>
      </div>
    @else
      {{-- ESTADO: APERTURA VIGENTE (Exacto a la Imagen de Referencia 5) --}}
      <div class="corte-status-panel open-state">
        <div class="corte-status-badge-row">
          <span class="badge-apertura-rapida" style="background:#10b981; color:#ffffff; font-weight:800; padding:6px 16px; border-radius:20px; font-size:0.88rem; display:inline-block">
            Apertura Vigente
          </span>
        </div>

        {{-- Grid Fiel al Diseño de la Imagen 5 --}}
        <div class="corte-info-grid" style="display:grid; grid-template-columns:repeat(3, 1fr); border:1px solid #334155; border-radius:6px; overflow:hidden; background:#181e2b; margin:16px 0">
          <div class="corte-info-item" style="padding:12px 16px; border-right:1px solid #334155; border-bottom:1px solid #334155">
            <span class="info-label" style="font-weight:700; color:#cbd5e1">Nombre:</span>
            <span class="info-value" style="font-weight:800; color:#38bdf8; margin-left:6px">
              {{ $cajaActual->usuario->nombre ?? session('user.nombre', 'karen') }}
            </span>
          </div>
          <div class="corte-info-item" style="padding:12px 16px; border-right:1px solid #334155; border-bottom:1px solid #334155">
            <span class="info-label" style="font-weight:700; color:#cbd5e1">Fecha Apertura:</span>
            <span class="info-value" style="font-weight:700; color:#ffffff; margin-left:6px">
              {{ $cajaActual->abierta_at ? $cajaActual->abierta_at->format('Y-m-d') : date('Y-m-d') }}
            </span>
          </div>
          <div class="corte-info-item" style="padding:12px 16px; border-bottom:1px solid #334155">
            <span class="info-label" style="font-weight:700; color:#cbd5e1">Hora Apertura:</span>
            <span class="info-value" style="font-weight:700; color:#ffffff; margin-left:6px">
              {{ $cajaActual->abierta_at ? $cajaActual->abierta_at->format('H:i:s') : date('H:i:s') }}
            </span>
          </div>
          <div class="corte-info-item" style="padding:12px 16px; border-right:1px solid #334155">
            <span class="info-label" style="font-weight:700; color:#cbd5e1">Monto Apertura Turno:</span>
            <span class="info-value" style="font-weight:800; color:#10b981; margin-left:6px">
              {{ number_format($cajaActual->monto_apertura, 2) }}
            </span>
          </div>
          <div class="corte-info-item" style="padding:12px 16px; border-right:1px solid #334155">
            <span class="info-label" style="font-weight:700; color:#cbd5e1">Turno:</span>
            <span class="info-value" style="font-weight:800; color:#f59e0b; margin-left:6px">
              {{ $cajaActual->turno ?? '1' }}
            </span>
          </div>
          <div class="corte-info-item" style="padding:12px 16px">
            <span class="info-label" style="font-weight:700; color:#cbd5e1">Monto Apertura:</span>
            <span class="info-value" style="font-weight:800; color:#10b981; margin-left:6px">
              {{ number_format($cajaActual->monto_apertura, 4) }}
            </span>
          </div>
        </div>

        {{-- Control de Permisos para Cierre / Corte:
             "Que otros usuarios no puedan cerrar las cajas o cortes si fue abierto ya sea por el administrador o por otro vendedor" --}}
        @php
          $puedeCerrar = $cajaActual->usuario_id == session('user.id');
        @endphp

        @if(!$puedeCerrar)
          <div class="alert alert-warning" style="background:#f59e0b18; border:1px solid #f59e0b; color:#fcd34d; padding:12px 16px; border-radius:8px; margin-top:14px; font-size:0.9rem">
            <i class="fa-solid fa-lock" style="margin-right:6px"></i>
            Esta caja está asignada al cajero <strong>{{ $cajaActual->usuario->nombre ?? 'otro usuario' }}</strong>.
            Por política de seguridad fiscal, únicamente dicho usuario puede registrar el cierre o corte Z.
          </div>
        @endif

        {{-- Botones de Acción --}}
        <div class="corte-actions-bar" style="margin-top:18px; display:flex; gap:12px; flex-wrap:wrap">
          <button type="button" class="btn-corte-action btn-corte-x" onclick="openCorteModal('modalCorteX')">
            <i class="fa-solid fa-file-invoice"></i> Corte X (Lectura Parcial)
          </button>

          @if($puedeCerrar)
            <button type="button" class="btn-corte-action btn-corte-z" onclick="openCorteModal('modalCorteZ')">
              <i class="fa-solid fa-lock"></i> Realizar Corte Z (Cierre de Caja)
            </button>
          @else
            <button type="button" class="btn-corte-action btn-corte-z" disabled style="opacity:0.5; cursor:not-allowed" title="Solo el cajero responsable puede cerrar la caja">
              <i class="fa-solid fa-lock"></i> Corte Z (Bloqueado a otros usuarios)
            </button>
          @endif

          <a href="{{ route('caja.movimientos') }}" class="btn-corte-action btn-corte-mov">
            <i class="fa-solid fa-money-bill-transfer"></i> Administrar Movimientos
          </a>
        </div>
      </div>
    @endif
  </div>

  {{-- Historial Reciente de Cortes de esta Caja:
       "también deshabilita la opción de historial de cortes para el empleado o vendedor, esto lo puede ver el administrador" --}}
  @php
    $esAdmin = session('user.rol') === 'admin';
  @endphp

  @if($esAdmin && $cajaActual && $cajaActual->cortes->count() > 0)
  <div class="corte-history-card" style="margin-top:24px">
    <div class="history-card-header" style="display:flex; justify-content:space-between; align-items:center; padding:14px 18px; background:#202534; border-radius:8px 8px 0 0; border:1px solid #2c3244; border-bottom:none">
      <h3 style="margin:0; font-size:1.05rem; font-weight:700; color:#38bdf8">
        <i class="fa-solid fa-clipboard-list"></i> Últimos Cortes Registrados
      </h3>
      <a href="{{ route('caja.historial') }}" class="btn-ver-historial" style="color:#38bdf8; font-weight:700; font-size:0.85rem; text-decoration:none">
        Ver Todo el Historial →
      </a>
    </div>
    <div style="overflow-x:auto; border:1px solid #2c3244; border-radius:0 0 8px 8px">
      <table class="table-corte-history" style="width:100%; border-collapse:collapse; background:#1a1e29; font-size:0.88rem">
        <thead>
          <tr style="background:#202534; color:#94a3b8; text-align:left; border-bottom:1px solid #2c3244">
            <th style="padding:10px 14px">Fecha Corte</th>
            <th style="padding:10px 14px">Sucursal / Turno</th>
            <th style="padding:10px 14px">Cajero</th>
            <th style="padding:10px 14px">Esperado</th>
            <th style="padding:10px 14px">Contado</th>
            <th style="padding:10px 14px">Diferencia</th>
            <th style="padding:10px 14px">Venta Efectivo</th>
            <th style="padding:10px 14px; text-align:right">Acción</th>
          </tr>
        </thead>
        <tbody>
          @foreach($cajaActual->cortes->take(5) as $cort)
          <tr style="border-bottom:1px solid #282d3c">
            <td style="padding:10px 14px; color:#e2e8f0">{{ $cort->created_at->format('d/m/Y H:i') }}</td>
            <td style="padding:10px 14px"><span class="badge" style="background:#1e293b; color:#38bdf8">{{ $cort->sucursal ?? 'Central' }} (T:{{ $cort->turno ?? '1' }})</span></td>
            <td style="padding:10px 14px; font-weight:700; color:#ffffff">{{ $cort->usuario->nombre ?? 'N/A' }}</td>
            <td style="padding:10px 14px; color:#94a3b8">${{ number_format($cort->efectivo_esperado, 2) }}</td>
            <td style="padding:10px 14px"><strong style="color:#10b981">${{ number_format($cort->efectivo_contado, 2) }}</strong></td>
            <td style="padding:10px 14px; color: {{ $cort->diferencia >= 0 ? '#10b981' : '#ef4444' }}; font-weight:700">
              {{ $cort->diferencia >= 0 ? '+' : '' }}${{ number_format($cort->diferencia, 2) }}
            </td>
            <td style="padding:10px 14px; color:#e2e8f0">${{ number_format($cort->ventas_efectivo, 2) }}</td>
            <td style="padding:10px 14px; text-align:right">
              <a href="{{ route('caja.corte', $cajaActual->id) }}" class="btn btn-sm btn-secondary" style="padding:4px 10px; font-size:0.8rem">
                Ver Ticket
              </a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif
</div>

{{-- MODAL CORTE X (LECTURA DE TURNO) --}}
@if($cajaActual && $cajaActual->estado === 'abierta' && $datosLectura)
<div id="modalCorteX" class="corte-modal-overlay" style="display:none">
  <div class="corte-modal-content" style="max-width:540px">
    <div class="corte-modal-header">
      <h3><i class="fa-solid fa-file-invoice"></i> Corte X — Lectura Parcial de Turno</h3>
      <button type="button" class="close-modal-btn" onclick="closeCorteModal('modalCorteX')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <div class="corte-modal-body" style="padding:16px 20px">
      <div style="background:#151822; padding:12px 16px; border-radius:6px; margin-bottom:14px; border:1px solid #282d3c">
        <div style="display:flex; justify-content:space-between; margin-bottom:6px">
          <span style="color:#94a3b8">Caja:</span><strong>{{ $cajaActual->nombre }} ({{ $cajaActual->sucursal ?? 'Central' }})</strong>
        </div>
        <div style="display:flex; justify-content:space-between; margin-bottom:6px">
          <span style="color:#94a3b8">Turno / Cajero:</span><strong>Turno {{ $cajaActual->turno ?? '1' }} — {{ $cajaActual->usuario->nombre ?? session('user.nombre') }}</strong>
        </div>
        <div style="display:flex; justify-content:space-between">
          <span style="color:#94a3b8">Apertura:</span><span>{{ $cajaActual->abierta_at ? $cajaActual->abierta_at->format('d/m/Y H:i:s') : '' }}</span>
        </div>
      </div>

      <table style="width:100%; border-collapse:collapse; font-size:0.9rem; margin-bottom:14px">
        <tr style="border-bottom:1px solid #282d3c"><td style="padding:6px 0; color:#94a3b8">(+) Fondo Apertura Turno:</td><td style="text-align:right; font-weight:700">${{ number_format($cajaActual->monto_apertura, 2) }}</td></tr>
        <tr style="border-bottom:1px solid #282d3c"><td style="padding:6px 0; color:#94a3b8">(+) Ventas en Efectivo:</td><td style="text-align:right; font-weight:700; color:#10b981">${{ number_format($datosLectura['efectivo_ventas'], 2) }}</td></tr>
        <tr style="border-bottom:1px solid #282d3c"><td style="padding:6px 0; color:#94a3b8">(+) Ingresos Extras en Gaveta:</td><td style="text-align:right; font-weight:700">${{ number_format($datosLectura['ingresos_extra'], 2) }}</td></tr>
        <tr style="border-bottom:1px solid #282d3c"><td style="padding:6px 0; color:#94a3b8">(-) Gastos / Retiros de Gaveta:</td><td style="text-align:right; font-weight:700; color:#ef4444">-${{ number_format($datosLectura['egresos_gastos'], 2) }}</td></tr>
        <tr style="border-top:2px solid #38bdf8"><td style="padding:10px 0; font-weight:800; font-size:1.05rem">(=) EFECTIVO ESPERADO EN GAVETA:</td><td style="text-align:right; font-weight:900; font-size:1.15rem; color:#38bdf8">${{ number_format($datosLectura['total_gaveta_esperado'], 2) }}</td></tr>
      </table>

      <div style="display:flex; justify-content:flex-end; gap:10px; margin-top:16px">
        <button type="button" class="btn btn-secondary" onclick="closeCorteModal('modalCorteX')">Cerrar</button>
        <button type="button" class="btn btn-primary" onclick="window.print()"><i class="fa-solid fa-print"></i> Imprimir Lectura X</button>
      </div>
    </div>
  </div>
</div>
@endif

{{-- MODAL CORTE Z (CIERRE DEFINITIVO) --}}
@if($cajaActual && $cajaActual->estado === 'abierta')
<div id="modalCorteZ" class="corte-modal-overlay" style="display:none">
  <div class="corte-modal-content" style="max-width:540px">
    <div class="corte-modal-header" style="background:#b91c1c">
      <h3 style="color:#ffffff"><i class="fa-solid fa-lock"></i> Realizar Corte Z (Cierre de Caja Definitivo)</h3>
      <button type="button" class="close-modal-btn" onclick="closeCorteModal('modalCorteZ')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <form action="{{ route('caja.cerrar') }}" method="POST">
      @csrf
      <div class="corte-modal-body" style="padding:18px 22px">
        <div style="background:#151822; padding:12px 14px; border-radius:6px; margin-bottom:16px; border:1px solid #282d3c; font-size:0.88rem">
          <p style="margin:0 0 6px 0"><strong>Caja:</strong> {{ $cajaActual->nombre }} ({{ $cajaActual->sucursal ?? 'Central' }}) — Turno {{ $cajaActual->turno ?? '1' }}</p>
          <p style="margin:0; color:#94a3b8"><strong>Cajero:</strong> {{ $cajaActual->usuario->nombre ?? session('user.nombre') }}</p>
        </div>

        <div class="modal-form-group" style="margin-bottom:14px">
          <label style="display:block; font-size:0.88rem; font-weight:700; color:#e2e8f0; margin-bottom:6px">
            Efectivo Contado en Gaveta ($) <span style="color:#ef4444">*</span>:
          </label>
          <input type="number" step="0.01" min="0" name="monto_cierre" class="form-control" placeholder="0.00" required autofocus
                 style="font-size:1.2rem; font-weight:900; color:#10b981; height:46px; background:#12141c; border-color:#2c3244">
          <span class="input-hint" style="color:#94a3b8; font-size:0.75rem; display:block; margin-top:4px">
            Ingrese el total de billetes y monedas contados físicamente en la gaveta.
          </span>
        </div>

        <div class="modal-form-group" style="margin-bottom:14px">
          <label style="display:block; font-size:0.88rem; font-weight:700; color:#e2e8f0; margin-bottom:6px">Observaciones de Cierre:</label>
          <textarea name="notas_cierre" class="form-control" rows="2" placeholder="Observaciones o incidencias en el arqueo..."
                    style="background:#12141c; border-color:#2c3244; color:#ffffff"></textarea>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #282d3c; padding-top:14px; margin-top:16px">
          <button type="button" class="btn btn-secondary" onclick="closeCorteModal('modalCorteZ')">Cancelar</button>
          <button type="submit" class="btn btn-danger" style="background:#dc2626; border:none; font-weight:800; padding:10px 20px"
                  onclick="return confirm('¿Está seguro de cerrar definitivamente la caja y emitir el Corte Z?')">
            <i class="fa-solid fa-check"></i> Confirmar Cierre (Corte Z)
          </button>
        </div>
      </div>
    </form>
  </div>
</div>
@endif

<style>
/* Estilos Empresariales Corte Caja */
.corte-admin-container { padding: 15px 0; }
.corte-page-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; flex-wrap: wrap; gap: 10px; }
.corte-title-wrap { display: flex; align-items: center; gap: 12px; }
.corte-main-icon { font-size: 1.8rem; color: #38bdf8; }
.corte-main-title { font-size: 1.6rem; font-weight: 800; margin: 0; color: #38bdf8; }
.corte-breadcrumb { font-size: 0.9rem; color: #94a3b8; }
.corte-breadcrumb a { color: #38bdf8; text-decoration: none; }
.corte-breadcrumb .active { color: #f1f5f9; font-weight: 600; }
.corte-enterprise-card { background: #1a1e29; border: 1px solid #2c3244; border-radius: 10px; padding: 22px; box-shadow: 0 4px 20px rgba(0,0,0,0.25); }
.corte-selectors-row { display: flex; gap: 14px; margin-bottom: 20px; flex-wrap: wrap; }
.corte-select-group { min-width: 220px; }
.corte-select { width: 100%; height: 42px; background: #12141c; color: #f1f5f9; border: 1px solid #334155; border-radius: 6px; padding: 0 14px; font-weight: 700; font-size: 0.95rem; }
.corte-status-panel { padding: 20px; border: 1px dashed #334155; border-radius: 8px; text-align: center; }
.corte-status-panel.open-state { border: 1px solid #334155; text-align: left; background: #141822; }
.badge-sin-apertura { background: #ef444422; border: 1px solid #ef4444; color: #f87171; padding: 6px 14px; border-radius: 20px; font-weight: 700; font-size: 0.85rem; }
.btn-realizar-apertura { margin-top: 14px; display: inline-block; background: #38bdf8; color: #0f172a; padding: 10px 24px; border-radius: 6px; font-weight: 800; font-size: 0.95rem; border: none; cursor: pointer; transition: 0.2s; }
.btn-realizar-apertura:hover { background: #0284c7; color: #ffffff; }
.btn-corte-action { padding: 10px 18px; border-radius: 6px; font-weight: 700; font-size: 0.9rem; border: none; cursor: pointer; display: inline-flex; align-items: center; gap: 6px; text-decoration: none; }
.btn-corte-x { background: #0284c7; color: #ffffff; }
.btn-corte-x:hover { background: #0369a1; }
.btn-corte-z { background: #dc2626; color: #ffffff; }
.btn-corte-z:hover { background: #b91c1c; }
.btn-corte-mov { background: #475569; color: #ffffff; }
.btn-corte-mov:hover { background: #334155; }
.corte-modal-overlay { position: fixed; inset: 0; background: rgba(0,0,0,0.75); display: flex; align-items: center; justify-content: center; z-index: 9999; padding: 15px; }
.corte-modal-content { background: #1a1e29; border: 1px solid #2c3244; border-radius: 10px; width: 100%; box-shadow: 0 10px 30px rgba(0,0,0,0.5); overflow: hidden; animation: popIn 0.2s ease-out; }
.corte-modal-header { display: flex; justify-content: space-between; align-items: center; padding: 14px 18px; background: #202534; border-bottom: 1px solid #2c3244; }
.corte-modal-header h3 { margin: 0; font-size: 1.05rem; font-weight: 700; color: #38bdf8; display: flex; align-items: center; gap: 8px; }
.close-modal-btn { background: none; border: none; color: #94a3b8; font-size: 1.2rem; cursor: pointer; }
.close-modal-btn:hover { color: #ffffff; }
@keyframes popIn { from { transform: scale(0.95); opacity: 0; } to { transform: scale(1); opacity: 1; } }
</style>

<script>
function openCorteModal(id) {
  const m = document.getElementById(id);
  if (m) m.style.display = 'flex';
}
function closeCorteModal(id) {
  const m = document.getElementById(id);
  if (m) m.style.display = 'none';
}
</script>
@endsection
