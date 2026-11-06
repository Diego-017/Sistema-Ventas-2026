@extends('layouts.main')
@section('title', 'Corte caja')

@section('content')
<div class="corte-admin-container">
  {{-- Header con Icono y Breadcrumb exacto a la imagen real --}}
  <div class="corte-page-header">
    <div class="corte-title-wrap">
      <span class="corte-main-icon"><i class="fa-solid fa-trash"></i></span>
      <h1 class="corte-main-title">Corte caja</h1>
    </div>
    <div class="corte-breadcrumb">
      <a href="{{ route('dashboard') }}">Inicio</a> <span>/</span> <span class="active">Corte caja</span>
    </div>
  </div>

  {{-- Card Principal con fondo oscuro empresarial --}}
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
      {{-- ESTADO: SIN APERTURA DE CAJA (Exacto a la imagen del usuario) --}}
      <div class="corte-status-panel">
        <div class="corte-status-badge-row">
          <span class="badge-sin-apertura">Sin apertura de caja</span>
        </div>
        <div class="corte-action-btn-row">
          <button type="button" class="btn-realizar-apertura" onclick="openCorteModal('modalApertura')">
            Realizar Apertura
          </button>
        </div>
      </div>
    @else
      {{-- ESTADO: CAJA ABIERTA (Exacto a la Página 15 del PDF) --}}
      <div class="corte-status-panel open-state">
        <div class="corte-status-badge-row">
          <span class="badge-apertura-rapida">Apertura Rápida</span>
        </div>

        {{-- Datos informativos del turno y cajero --}}
        <div class="corte-info-grid">
          <div class="corte-info-item">
            <span class="info-label">Nombre:</span>
            <span class="info-value">{{ $cajaActual->usuario->nombre ?? session('user.nombre') }}</span>
          </div>
          <div class="corte-info-item">
            <span class="info-label">Fecha Apertura:</span>
            <span class="info-value">{{ $cajaActual->abierta_at->format('Y-m-d') }}</span>
          </div>
          <div class="corte-info-item">
            <span class="info-label">Hora Apertura:</span>
            <span class="info-value">{{ $cajaActual->abierta_at->format('H:i:s') }}</span>
          </div>
          <div class="corte-info-item">
            <span class="info-label">Monto Apertura Turno:</span>
            <span class="info-value">$0.00</span>
          </div>
          <div class="corte-info-item">
            <span class="info-label">Turno:</span>
            <span class="info-value">1</span>
          </div>
          <div class="corte-info-item">
            <span class="info-label">Monto Apertura:</span>
            <span class="info-value">${{ number_format($cajaActual->monto_apertura, 4) }}</span>
          </div>
        </div>

        {{-- Botones de Acción de Sistemas Reales: Corte X, Corte Z y Movimientos --}}
        <div class="corte-actions-bar">
          <button type="button" class="btn-corte-action btn-corte-x" onclick="openCorteModal('modalCorteX')">
            <i class="fa-solid fa-file-invoice"></i> Corte X (Lectura de Turno)
          </button>
          <button type="button" class="btn-corte-action btn-corte-z" onclick="openCorteModal('modalCorteZ')">
            <i class="fa-solid fa-lock"></i> Corte Z (Cierre Fiscal Definitivo)
          </button>
          <a href="{{ route('caja.movimientos') }}" class="btn-corte-action btn-corte-mov">
            <i class="fa-solid fa-money-bill-transfer"></i> Administrar Movimientos
          </a>
        </div>
      </div>
    @endif
  </div>

  {{-- Historial Reciente de Cortes de esta Caja --}}
  @if($cajaActual && $cajaActual->cortes->count() > 0)
  <div class="corte-history-card">
    <div class="history-card-header">
      <h3><i class="fa-solid fa-clipboard-list"></i> Últimos Cortes Registrados</h3>
      <a href="{{ route('caja.historial') }}" class="btn-ver-historial">Ver Todo el Historial →</a>
    </div>
    <div style="overflow-x:auto">
      <table class="table-corte-history">
        <thead>
          <tr>
            <th>Fecha Corte</th>
            <th>Cajero</th>
            <th>Esperado</th>
            <th>Contado</th>
            <th>Diferencia</th>
            <th>Venta Efectivo</th>
            <th>Venta Tarjeta</th>
            <th>Acción</th>
          </tr>
        </thead>
        <tbody>
          @foreach($cajaActual->cortes->take(5) as $cort)
          <tr>
            <td>{{ $cort->created_at->format('d/m/Y H:i') }}</td>
            <td>{{ $cort->usuario->nombre ?? 'N/A' }}</td>
            <td>${{ number_format($cort->efectivo_esperado, 2) }}</td>
            <td><strong>${{ number_format($cort->efectivo_contado, 2) }}</strong></td>
            <td style="color: {{ $cort->diferencia >= 0 ? '#10b981' : '#ef4444' }}; font-weight:700">
              {{ $cort->diferencia >= 0 ? '+' : '' }}${{ number_format($cort->diferencia, 2) }}
            </td>
            <td>${{ number_format($cort->ventas_efectivo, 2) }}</td>
            <td>${{ number_format($cort->ventas_tarjeta, 2) }}</td>
            <td>
              <a href="{{ route('caja.corte', $cajaActual->id) }}" class="btn-ticket-sm">Ver Ticket</a>
            </td>
          </tr>
          @endforeach
        </tbody>
      </table>
    </div>
  </div>
  @endif
</div>

{{-- MODAL REALIZAR APERTURA --}}
<div id="modalApertura" class="corte-modal-overlay" style="display:none">
  <div class="corte-modal-content">
    <div class="corte-modal-header">
      <h3><i class="fa-solid fa-lock-open"></i> Realizar Apertura de Caja</h3>
      <button type="button" class="close-modal-btn" onclick="closeCorteModal('modalApertura')"><i class="fa-solid fa-xmark"></i></button>
    </div>
    <form action="{{ route('caja.abrir') }}" method="POST">
      @csrf
      <input type="hidden" name="nombre" value="{{ $cajaActual->nombre ?? 'CAJA 1' }}">

      <div class="modal-form-group">
        <label>Caja a Abrir:</label>
        <input type="text" class="corte-modal-input" value="{{ $cajaActual->nombre ?? 'CAJA 1' }}" readonly>
      </div>

      <div class="modal-form-group">
        <label>Cajero / Responsable:</label>
        <input type="text" class="corte-modal-input" value="{{ session('user.nombre') }}" readonly>
      </div>

      <div class="modal-form-group">
        <label>Monto de Apertura ($) <span style="color:#ef4444">*</span>:</label>
        <input type="number" step="0.01" min="0" name="monto_apertura" class="corte-modal-input" placeholder="0.00" value="5.00" required autofocus>
        <span class="input-hint">Fondo inicial en efectivo para dar cambio en gaveta.</span>
      </div>

      <div class="modal-form-group">
        <label>Observaciones (Opcional):</label>
        <input type="text" name="notas_apertura" class="corte-modal-input" placeholder="Ej: Apertura Turno 1">
      </div>

      <div class="corte-modal-footer">
        <button type="button" class="btn-cancelar-modal" onclick="closeCorteModal('modalApertura')">Cancelar</button>
        <button type="submit" class="btn-confirmar-apertura">Confirmar Apertura</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL CORTE X (LECTURA PARCIAL DE TURNO) --}}
@if($datosLectura)
<div id="modalCorteX" class="corte-modal-overlay" style="display:none">
  <div class="corte-modal-content corte-modal-receipt">
    <div class="corte-modal-header">
      <div>
        <h3 style="margin:0; font-size:1.15rem; color:#fff"><i class="fa-solid fa-file-invoice"></i> LECTURA CORTE X</h3>
        <span style="font-size:0.75rem; color:#94a3b8">Arqueo parcial informativo — La caja continúa abierta</span>
      </div>
      <button type="button" class="close-modal-btn" onclick="closeCorteModal('modalCorteX')"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <div class="receipt-preview-box">
      <div class="receipt-brand-center">
        <h4>{{ \App\Models\Configuracion::get('nombre_negocio', 'Super Emmanuel') }}</h4>
        <p>REPORTE DE CORTE X (PARCIAL)</p>
        <p style="font-size:0.72rem; color:#64748b">{{ now()->format('d/m/Y h:i:s A') }}</p>
      </div>

      <div class="receipt-divider"></div>

      <div class="receipt-row-line"><span>Caja:</span><span><strong>{{ $cajaActual->nombre }}</strong></span></div>
      <div class="receipt-row-line"><span>Cajero:</span><span>{{ $cajaActual->usuario->nombre ?? session('user.nombre') }}</span></div>
      <div class="receipt-row-line"><span>Apertura:</span><span>{{ $cajaActual->abierta_at->format('d/m/Y H:i') }}</span></div>
      <div class="receipt-row-line"><span>Transacciones:</span><span>{{ $datosLectura['total_transacciones'] }}</span></div>

      <div class="receipt-divider"></div>

      <div class="receipt-row-line"><span>Fondo Inicial:</span><span>${{ number_format($cajaActual->monto_apertura, 2) }}</span></div>
      <div class="receipt-row-line"><span>(+) Ventas Efectivo:</span><span style="color:#10b981">+${{ number_format($datosLectura['efectivo_ventas'], 2) }}</span></div>
      <div class="receipt-row-line"><span>(+) Ventas Tarjeta / Vouchers:</span><span>${{ number_format($datosLectura['tarjeta_ventas'], 2) }}</span></div>
      <div class="receipt-row-line"><span>(+) Ventas Transferencia:</span><span>${{ number_format($datosLectura['transf_ventas'], 2) }}</span></div>
      <div class="receipt-row-line"><span>(+) Ventas Crédito:</span><span>${{ number_format($datosLectura['credito_ventas'], 2) }}</span></div>
      <div class="receipt-row-line"><span>(+) Entradas Caja Chica:</span><span style="color:#10b981">+${{ number_format($datosLectura['ingresos_extra'], 2) }}</span></div>
      <div class="receipt-row-line"><span>(-) Salidas / Gastos Caja:</span><span style="color:#ef4444">-${{ number_format($datosLectura['egresos_gastos'], 2) }}</span></div>

      <div class="receipt-divider-thick"></div>

      <div class="receipt-row-line total-highlight">
        <span>EFECTIVO EN GAVETA:</span>
        <span>${{ number_format($datosLectura['total_gaveta_esperado'], 2) }}</span>
      </div>
      <div class="receipt-row-line" style="margin-top:4px">
        <span>TOTAL VENTAS TURNO:</span>
        <span>${{ number_format($datosLectura['total_ventas_bruto'], 2) }}</span>
      </div>
    </div>

    <div class="corte-modal-footer">
      <a href="{{ route('caja.corte_x', $cajaActual->id) }}" target="_blank" class="btn-imprimir-ticket">
        <i class="fa-solid fa-print"></i> Imprimir Tirilla Corte X
      </a>
      <button type="button" class="btn-cancelar-modal" onclick="closeCorteModal('modalCorteX')">Cerrar</button>
    </div>
  </div>
</div>

{{-- MODAL CORTE Z (CIERRE FISCAL DEFINITIVO) --}}
<div id="modalCorteZ" class="corte-modal-overlay" style="display:none">
  <div class="corte-modal-content">
    <div class="corte-modal-header" style="background:#dc2626">
      <div>
        <h3 style="margin:0; font-size:1.15rem; color:#fff"><i class="fa-solid fa-lock"></i> CORTE Z — CIERRE FISCAL DEFINITIVO</h3>
        <span style="font-size:0.75rem; color:#fee2e2">Este proceso cerrará la caja del día y generará el reporte consolidado</span>
      </div>
      <button type="button" class="close-modal-btn" onclick="closeCorteModal('modalCorteZ')"><i class="fa-solid fa-xmark"></i></button>
    </div>

    <form method="POST" action="{{ route('caja.cerrar') }}" id="formCorteZ">
      @csrf
      <div style="padding:18px">
        <div class="corte-z-summary-card">
          <div class="summary-line"><span>Efectivo Esperado en Gaveta:</span><strong id="esperadoTxt">${{ number_format($datosLectura['total_gaveta_esperado'], 2) }}</strong></div>
          <div class="summary-line"><span>Ventas Tarjeta (Vouchers POS):</span><strong>${{ number_format($datosLectura['tarjeta_ventas'], 2) }}</strong></div>
          <div class="summary-line"><span>Ventas Totales del Día:</span><strong>${{ number_format($datosLectura['total_ventas_bruto'], 2) }}</strong></div>
        </div>

        <p style="font-size:0.8rem; color:#94a3b8; margin: 12px 0 6px 0; font-weight:600">
          Conteo de Billetes y Monedas (Se calcula automáticamente):
        </p>

        <div class="denominaciones-mini-grid">
          @foreach([100,50,20,10,5,1,0.25,0.10,0.05] as $d)
          <div class="denom-mini-box">
            <span>${{ $d }}</span>
            <input type="number" min="0" class="input-denom-val" data-valor="{{ $d }}" placeholder="0" oninput="recalcularConteoZ()">
          </div>
          @endforeach
        </div>
        <input type="hidden" name="denominaciones" id="denominacionesJsonZ">

        <div class="modal-form-group" style="margin-top:14px">
          <label>Efectivo Físico Contado Total ($) <span style="color:#ef4444">*</span>:</label>
          <input type="number" step="0.01" min="0" name="monto_cierre" id="montoContadoZ" class="corte-modal-input" placeholder="0.00" required oninput="calcularDiferenciaZ()">
        </div>

        <div class="diferencia-alert-box" id="diferenciaBox">
          <span>Diferencia de Cuadratura:</span>
          <strong id="diferenciaTxt">$0.00</strong>
        </div>

        <div class="modal-form-group">
          <label>Notas / Observaciones del Cierre:</label>
          <input type="text" name="notas_cierre" class="corte-modal-input" placeholder="Ej: Cierre de jornada sin novedades">
        </div>
      </div>

      <div class="corte-modal-footer">
        <button type="button" class="btn-cancelar-modal" onclick="closeCorteModal('modalCorteZ')">Cancelar</button>
        <button type="submit" class="btn-ejecutar-corte-z" onclick="return confirm('¿Confirmas el Cierre Fiscal Z definitivo de esta caja?')">
          <i class="fa-solid fa-lock"></i> Ejecutar Corte Z y Cerrar Caja
        </button>
      </div>
    </form>
  </div>
</div>
@endif

<style>
/* Estilos Empresariales Corte Caja */
.corte-admin-container {
  max-width: 1050px;
  margin: 0 auto;
}
.corte-page-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 22px;
}
.corte-title-wrap {
  display: flex;
  align-items: center;
  gap: 12px;
}
.corte-main-icon {
  font-size: 2rem;
  color: #38bdf8;
}
.corte-main-title {
  font-size: 1.8rem;
  font-weight: 800;
  color: #38bdf8;
  margin: 0;
}
.corte-breadcrumb {
  font-size: 0.95rem;
  color: #64748b;
}
.corte-breadcrumb a {
  color: #38bdf8;
  text-decoration: none;
  font-weight: 600;
}
.corte-breadcrumb .active {
  color: #94a3b8;
}

/* Card Principal con fondo oscuro como en la imagen */
.corte-enterprise-card {
  background: #252836;
  border-radius: 8px;
  border: 1px solid #33384c;
  padding: 24px;
  box-shadow: 0 4px 20px rgba(0,0,0,0.25);
  margin-bottom: 25px;
}
.corte-selectors-row {
  display: flex;
  flex-direction: column;
  gap: 14px;
  max-width: 280px;
  margin-bottom: 24px;
}
.corte-select {
  width: 100%;
  padding: 10px 14px;
  background: #ffffff;
  color: #0f172a;
  border: 1px solid #cbd5e1;
  border-radius: 6px;
  font-weight: 700;
  font-size: 0.95rem;
  outline: none;
  cursor: pointer;
}

/* Panel de estado central */
.corte-status-panel {
  border-top: 1px solid #353b52;
  border-bottom: 1px solid #353b52;
  padding: 24px 10px;
  text-align: center;
}
.badge-sin-apertura {
  display: inline-block;
  background: #10b981;
  color: #ffffff;
  padding: 6px 20px;
  border-radius: 6px;
  font-weight: 800;
  font-size: 0.95rem;
  margin-bottom: 16px;
  box-shadow: 0 2px 8px rgba(16,185,129,0.3);
}
.btn-realizar-apertura {
  background: #2563eb;
  color: #ffffff;
  border: none;
  padding: 10px 24px;
  border-radius: 6px;
  font-weight: 700;
  font-size: 0.95rem;
  cursor: pointer;
  transition: all 0.2s;
}
.btn-realizar-apertura:hover {
  background: #1d4ed8;
  transform: translateY(-1px);
}

/* Estado abierto */
.badge-apertura-rapida {
  display: inline-block;
  background: #10b981;
  color: #ffffff;
  padding: 6px 20px;
  border-radius: 6px;
  font-weight: 800;
  font-size: 0.95rem;
  margin-bottom: 18px;
}
.corte-info-grid {
  display: grid;
  grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
  gap: 14px;
  background: #1e2230;
  padding: 18px;
  border-radius: 8px;
  border: 1px solid #31374a;
  text-align: left;
  margin-bottom: 20px;
}
.corte-info-item .info-label {
  display: block;
  font-size: 0.78rem;
  color: #94a3b8;
  text-transform: uppercase;
  font-weight: 700;
}
.corte-info-item .info-value {
  font-size: 1.05rem;
  font-weight: 800;
  color: #f1f5f9;
}

/* Barra de acciones de Corte */
.corte-actions-bar {
  display: flex;
  justify-content: center;
  gap: 12px;
  flex-wrap: wrap;
}
.btn-corte-action {
  padding: 12px 20px;
  border-radius: 6px;
  font-weight: 700;
  font-size: 0.9rem;
  border: none;
  cursor: pointer;
  text-decoration: none;
  display: inline-flex;
  align-items: center;
  gap: 8px;
  transition: all 0.2s;
}
.btn-corte-x { background: #38bdf8; color: #0f172a; }
.btn-corte-x:hover { background: #0284c7; color: #fff; }
.btn-corte-z { background: #ef4444; color: #fff; }
.btn-corte-z:hover { background: #dc2626; }
.btn-corte-mov { background: #475569; color: #fff; }
.btn-corte-mov:hover { background: #334155; }

/* Historial de cortes */
.corte-history-card {
  background: #252836;
  border-radius: 8px;
  border: 1px solid #33384c;
  padding: 20px;
}
.history-card-header {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 15px;
}
.history-card-header h3 {
  margin: 0;
  color: #f1f5f9;
  font-size: 1.1rem;
}
.btn-ver-historial {
  color: #38bdf8;
  text-decoration: none;
  font-size: 0.85rem;
  font-weight: 700;
}
.table-corte-history {
  width: 100%;
  border-collapse: collapse;
  font-size: 0.85rem;
  color: #cbd5e1;
}
.table-corte-history th {
  background: #1e2230;
  padding: 10px;
  text-align: left;
  border-bottom: 2px solid #33384c;
  color: #94a3b8;
}
.table-corte-history td {
  padding: 10px;
  border-bottom: 1px solid #33384c;
}
.btn-ticket-sm {
  background: #334155;
  color: #fff;
  padding: 4px 8px;
  border-radius: 4px;
  text-decoration: none;
  font-size: 0.75rem;
  font-weight: 600;
}

/* Modales */
.corte-modal-overlay {
  position: fixed;
  top: 0; left: 0;
  width: 100%; height: 100%;
  background: rgba(0,0,0,0.7);
  backdrop-filter: blur(4px);
  z-index: 9999;
  display: flex;
  align-items: center;
  justify-content: center;
  padding: 20px;
}
.corte-modal-content {
  background: #1e2230;
  border: 1px solid #33384c;
  border-radius: 10px;
  width: 100%;
  max-width: 480px;
  color: #f1f5f9;
  box-shadow: 0 10px 40px rgba(0,0,0,0.5);
  overflow: hidden;
}
.corte-modal-receipt {
  max-width: 420px;
}
.corte-modal-header {
  background: #252836;
  padding: 16px 20px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  border-bottom: 1px solid #33384c;
}
.close-modal-btn {
  background: none;
  border: none;
  color: #94a3b8;
  font-size: 1.2rem;
  cursor: pointer;
}
.modal-form-group {
  padding: 0 20px;
  margin-bottom: 14px;
}
.modal-form-group label {
  display: block;
  font-size: 0.85rem;
  font-weight: 600;
  margin-bottom: 4px;
  color: #cbd5e1;
}
.corte-modal-input {
  width: 100%;
  padding: 10px 12px;
  background: #0f172a;
  border: 1px solid #33384c;
  border-radius: 6px;
  color: #fff;
  font-size: 0.95rem;
  font-weight: 600;
}
.input-hint {
  font-size: 0.75rem;
  color: #94a3b8;
  margin-top: 4px;
  display: block;
}
.corte-modal-footer {
  background: #252836;
  padding: 14px 20px;
  display: flex;
  justify-content: flex-end;
  gap: 10px;
  border-top: 1px solid #33384c;
}
.btn-cancelar-modal {
  background: #334155;
  color: #fff;
  border: none;
  padding: 8px 16px;
  border-radius: 6px;
  cursor: pointer;
  font-weight: 600;
}
.btn-confirmar-apertura {
  background: #10b981;
  color: #fff;
  border: none;
  padding: 8px 18px;
  border-radius: 6px;
  font-weight: 700;
  cursor: pointer;
}
.btn-imprimir-ticket {
  background: #38bdf8;
  color: #0f172a;
  padding: 8px 16px;
  border-radius: 6px;
  text-decoration: none;
  font-weight: 700;
  font-size: 0.85rem;
}
.btn-ejecutar-corte-z {
  background: #dc2626;
  color: #fff;
  border: none;
  padding: 8px 18px;
  border-radius: 6px;
  font-weight: 700;
  cursor: pointer;
}

/* Tirilla Preview Corte X */
.receipt-preview-box {
  background: #ffffff;
  color: #000000;
  padding: 20px;
  font-family: 'Courier New', monospace;
  font-size: 0.82rem;
  max-height: 420px;
  overflow-y: auto;
}
.receipt-brand-center { text-align: center; margin-bottom: 8px; }
.receipt-brand-center h4 { margin: 0; font-size: 1rem; }
.receipt-divider { border-bottom: 1px dashed #475569; margin: 8px 0; }
.receipt-divider-thick { border-bottom: 2px solid #000; margin: 8px 0; }
.receipt-row-line { display: flex; justify-content: space-between; margin-bottom: 3px; }
.receipt-row-line.total-highlight { font-weight: 900; font-size: 0.95rem; }

/* Corte Z inputs */
.corte-z-summary-card {
  background: #0f172a;
  border: 1px solid #33384c;
  padding: 12px;
  border-radius: 6px;
  font-size: 0.85rem;
}
.summary-line { display: flex; justify-content: space-between; margin-bottom: 4px; }
.denominaciones-mini-grid {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 6px;
}
.denom-mini-box {
  background: #0f172a;
  border: 1px solid #33384c;
  border-radius: 4px;
  padding: 4px;
  text-align: center;
}
.denom-mini-box span { font-size: 0.7rem; color: #94a3b8; font-weight: 700; }
.input-denom-val {
  width: 100%;
  background: transparent;
  border: none;
  border-top: 1px solid #33384c;
  color: #fff;
  text-align: center;
  font-size: 0.8rem;
  padding-top: 2px;
}
.diferencia-alert-box {
  background: #1e2230;
  border: 1px solid #33384c;
  padding: 10px 14px;
  border-radius: 6px;
  display: flex;
  justify-content: space-between;
  align-items: center;
  font-size: 0.9rem;
  margin-bottom: 14px;
}
</style>

<script>
function openCorteModal(id) {
  const el = document.getElementById(id);
  if (el) el.style.display = 'flex';
}
function closeCorteModal(id) {
  const el = document.getElementById(id);
  if (el) el.style.display = 'none';
}

function recalcularConteoZ() {
  let total = 0;
  const denoms = {};
  document.querySelectorAll('.input-denom-val').forEach(inp => {
    const cant = parseInt(inp.value) || 0;
    const valor = parseFloat(inp.dataset.valor);
    total += cant * valor;
    if (cant > 0) denoms[valor] = cant;
  });
  document.getElementById('montoContadoZ').value = total.toFixed(2);
  document.getElementById('denominacionesJsonZ').value = JSON.stringify(denoms);
  calcularDiferenciaZ();
}

function calcularDiferenciaZ() {
  const esperadoTxt = document.getElementById('esperadoTxt');
  if (!esperadoTxt) return;
  const esperado = parseFloat(esperadoTxt.innerText.replace('$', '').replace(',', '')) || 0;
  const contado = parseFloat(document.getElementById('montoContadoZ').value) || 0;
  const dif = contado - esperado;
  
  const difEl = document.getElementById('diferenciaTxt');
  const difBox = document.getElementById('diferenciaBox');
  if (difEl) {
    difEl.innerText = (dif >= 0 ? '+' : '') + '$' + dif.toFixed(2);
    difEl.style.color = (dif === 0 ? '#10b981' : (dif > 0 ? '#38bdf8' : '#ef4444'));
  }
}
</script>
@endsection
