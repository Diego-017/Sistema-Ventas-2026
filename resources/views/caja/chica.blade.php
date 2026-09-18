@extends('layouts.main')
@section('title', 'Caja Chica — DigitalsPos')

@section('content')
<div class="page-header" style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
  <div>
    <h1 style="margin: 0; font-size: 1.8rem; font-weight: 800; color: #1e293b;">Caja Chica</h1>
    <div style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">
      📱 PIN: <strong style="color: #0f172a; font-family: monospace; font-size: 1rem;">998909</strong>
    </div>
  </div>
  <div>
    <a href="{{ route('caja.index') }}" class="btn-primary" style="background: #64748b; text-decoration: none; padding: 8px 16px; border-radius: 6px; color: #fff; font-weight: 600;">
      ✓ Ventas Registradas
    </a>
  </div>
</div>

{{-- ACCIONES RÁPIDAS --}}
<div class="card" style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px;">
  <div style="font-weight: 700; font-size: 1.1rem; color: #0f172a; margin-bottom: 15px; display: flex; align-items: center; gap: 8px;">
    ⚡ Acciones Rápidas
  </div>

  <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(130px, 1fr)); gap: 12px;">
    <!-- 🟢 INGRESO -->
    <button type="button" onclick="openModal('modalIngreso')" style="background: #22c55e; color: #fff; border: none; padding: 14px 10px; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 6px; transition: transform 0.1s;">
      <span style="font-size: 1.3rem;">➕</span>
      <span>Ingreso</span>
    </button>

    <!-- 🔴 EGRESO -->
    <button type="button" onclick="openModal('modalEgreso')" style="background: #ef4444; color: #fff; border: none; padding: 14px 10px; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 6px;">
      <span style="font-size: 1.3rem;">➖</span>
      <span>Egreso</span>
    </button>

    <!-- 📄 FACTURAS PROVEEDOR -->
    <a href="{{ route('compras.index') }}" style="background: #ffffff; color: #dc2626; border: 2px solid #fca5a5; padding: 12px 10px; border-radius: 8px; font-weight: 700; text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 6px; text-align: center;">
      <span style="font-size: 1.3rem;">📚</span>
      <span>Facturas Proveedor</span>
    </a>

    <!-- 🟡 VOUCHERS POS -->
    <button type="button" onclick="openModal('modalVoucher')" style="background: #eab308; color: #fff; border: none; padding: 14px 10px; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 6px;">
      <span style="font-size: 1.3rem;">💳</span>
      <span>Vouchers</span>
    </button>

    <!-- 🩵 SERVICIO RÁPIDO -->
    <button type="button" onclick="openModal('modalEgreso'); setCategoria('Servicio Rápido');" style="background: #06b6d4; color: #fff; border: none; padding: 14px 10px; border-radius: 8px; font-weight: 700; cursor: pointer; display: flex; flex-direction: column; align-items: center; gap: 6px;">
      <span style="font-size: 1.3rem;">📲</span>
      <span>Servicio Rápido</span>
    </button>

    <!-- 🔷 APERTURA ARQUEO -->
    <a href="{{ route('caja.cuadraturas') }}" style="background: #2563eb; color: #fff; border: none; padding: 14px 10px; border-radius: 8px; font-weight: 700; text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 6px; text-align: center;">
      <span style="font-size: 1.3rem;">⚖️</span>
      <span>Apertura / Arqueo</span>
    </a>

    <!-- 🟩 CIERRE RÁPIDO -->
    <a href="{{ route('caja.index') }}" style="background: #16a34a; color: #fff; border: none; padding: 14px 10px; border-radius: 8px; font-weight: 700; text-decoration: none; display: flex; flex-direction: column; align-items: center; gap: 6px; text-align: center;">
      <span style="font-size: 1.3rem;">💸</span>
      <span>Cierre Rápido</span>
    </a>
  </div>
</div>

{{-- FILTROS Y SWITCH ADMIN --}}
<div class="card" style="background: #fff; border-radius: 12px; padding: 16px 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px;">
  <form method="GET" action="{{ route('caja.chica') }}" style="display: flex; align-items: center; gap: 15px; flex-wrap: wrap;">
    <div>
      <label style="font-weight: 600; font-size: 0.85rem; color: #64748b; display: block; margin-bottom: 4px;">Filtrar por Fecha:</label>
      <input type="date" name="fecha" value="{{ $fecha }}" onchange="this.form.submit()" style="padding: 6px 12px; border: 1px solid #cbd5e1; border-radius: 6px; font-weight: 600;">
    </div>

    @if(session('user.rol') === 'admin')
    <div style="display: flex; align-items: center; gap: 10px; margin-top: 18px;">
      <label class="switch" style="position: relative; display: inline-block; width: 44px; height: 24px;">
        <input type="checkbox" name="ver_todos" value="1" {{ $verTodos ? 'checked' : '' }} onchange="this.form.submit()" style="opacity: 0; width: 0; height: 0;">
        <span class="slider" style="position: absolute; cursor: pointer; top: 0; left: 0; right: 0; bottom: 0; background-color: {{ $verTodos ? '#2563eb' : '#ccc' }}; transition: .4s; border-radius: 24px;"></span>
      </label>
      <div>
        <strong style="font-size: 0.9rem; color: #0f172a; display: block;">Ver todos los movimientos de la sucursal</strong>
        <span style="font-size: 0.75rem; color: #64748b;">Por defecto solo ves tus movimientos</span>
      </div>
    </div>
    @endif
  </form>

  {{-- RESUMEN DEL DÍA --}}
  <div style="display: flex; gap: 20px;">
    <div style="text-align: right;">
      <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Ingresos</div>
      <div style="font-size: 1.2rem; font-weight: 800; color: #16a34a;">+${{ number_format($totalIngresos, 2) }}</div>
    </div>
    <div style="text-align: right;">
      <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Egresos</div>
      <div style="font-size: 1.2rem; font-weight: 800; color: #dc2626;">-${{ number_format($totalEgresos, 2) }}</div>
    </div>
    <div style="text-align: right;">
      <div style="font-size: 0.75rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Vouchers POS</div>
      <div style="font-size: 1.2rem; font-weight: 800; color: #ca8a04;">${{ number_format($totalVouchers, 2) }}</div>
    </div>
  </div>
</div>

{{-- TABLA DE MOVIMIENTOS --}}
<div class="card" style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 15px;">
    <h3 style="margin: 0; font-size: 1.1rem; color: #0f172a; font-weight: 700;">Mis Movimientos - Hoy</h3>
    <span style="font-size: 0.85rem; color: #64748b;">Mostrando {{ $movimientos->count() }} registros</span>
  </div>

  <div style="overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
      <thead>
        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569;">
          <th style="padding: 10px;">Código</th>
          <th style="padding: 10px;">Fecha y Hora</th>
          <th style="padding: 10px;">Tipo</th>
          <th style="padding: 10px;">Categoría</th>
          <th style="padding: 10px;">Descripción</th>
          <th style="padding: 10px;">Comprobante</th>
          <th style="padding: 10px;">Monto</th>
          <th style="padding: 10px;">Usuario</th>
          <th style="padding: 10px;">Pago a</th>
          <th style="padding: 10px;">Estado</th>
        </tr>
      </thead>
      <tbody>
        @forelse($movimientos as $m)
        <tr style="border-bottom: 1px solid #f1f5f9;">
          <td style="padding: 10px; font-weight: 700; color: #0f172a;">#{{ str_pad($m->id, 5, '0', STR_PAD_LEFT) }}</td>
          <td style="padding: 10px; color: #64748b;">{{ $m->created_at->format('d/m/Y H:i') }}</td>
          <td style="padding: 10px;">
            @if($m->tipo === 'ingreso')
              <span style="background: #dcfce7; color: #15803d; padding: 3px 8px; border-radius: 4px; font-weight: 700; font-size: 0.75rem;">↑ Entrada</span>
            @else
              <span style="background: #fee2e2; color: #b91c1c; padding: 3px 8px; border-radius: 4px; font-weight: 700; font-size: 0.75rem;">↓ Salida</span>
            @endif
          </td>
          <td style="padding: 10px; font-weight: 600; color: #334155;">{{ $m->categoria }}</td>
          <td style="padding: 10px; color: #475569;">{{ $m->descripcion }}</td>
          <td style="padding: 10px; color: #64748b;">{{ $m->comprobante ?? '-' }}</td>
          <td style="padding: 10px; font-weight: 800; color: {{ $m->tipo === 'ingreso' ? '#16a34a' : '#dc2626' }};">
            {{ $m->tipo === 'ingreso' ? '+' : '-' }}${{ number_format($m->monto, 2) }}
          </td>
          <td style="padding: 10px; color: #334155; font-weight: 600;">{{ $m->usuario->nombre ?? 'Sistema' }}</td>
          <td style="padding: 10px; color: #64748b;">{{ $m->pago_a ?? 'N/A' }}</td>
          <td style="padding: 10px;">
            <span style="background: #dcfce7; color: #166534; padding: 2px 8px; border-radius: 12px; font-size: 0.75rem; font-weight: 700;">
              ✓ {{ ucfirst($m->estado) }}
            </span>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="10" style="text-align: center; padding: 30px; color: #94a3b8;">No hay movimientos registrados para esta fecha.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="margin-top: 15px;">{{ $movimientos->links() }}</div>
</div>

{{-- MODAL INGRESO --}}
<div id="modalIngreso" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; justify-content:center; align-items:center;">
  <div style="background:#fff; border-radius:12px; padding:25px; width:100%; max-width:480px;">
    <h3 style="margin-top:0; color:#15803d; font-weight:800;">🟢 Registrar Ingreso a Caja Chica</h3>
    <form action="{{ route('caja.movimiento.store') }}" method="POST">
      @csrf
      <input type="hidden" name="tipo" value="ingreso">
      
      <div style="margin-bottom:12px;">
        <label style="font-weight:600; font-size:0.85rem;">Categoría de Ingreso:</label>
        <select name="categoria" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1;" required>
          <option value="Inyección de Caja">Inyección / Fondo de Caja</option>
          <option value="Entrada Inter sucursal">Entrada Inter-sucursal</option>
          <option value="Cobro Pendiente">Cobro de Cuenta / Ajuste</option>
          <option value="Ingreso Varios">Otros Ingresos</option>
        </select>
      </div>

      <div style="margin-bottom:12px;">
        <label style="font-weight:600; font-size:0.85rem;">Descripción:</label>
        <input type="text" name="descripcion" placeholder="Ej: Inyección de efectivo para cambio" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1;" required>
      </div>

      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:12px;">
        <div>
          <label style="font-weight:600; font-size:0.85rem;">Monto ($):</label>
          <input type="number" step="0.01" name="monto" placeholder="0.00" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1;" required>
        </div>
        <div>
          <label style="font-weight:600; font-size:0.85rem;">Recibido de:</label>
          <input type="text" name="pago_a" placeholder="Nombre persona/sucursal" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1;">
        </div>
      </div>

      <div style="margin-bottom:15px;">
        <label style="font-weight:600; font-size:0.85rem;">N° Comprobante/Recibo:</label>
        <input type="text" name="comprobante" placeholder="Ej: REC-0012" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1;">
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px;">
        <button type="button" onclick="closeModal('modalIngreso')" style="background:#e2e8f0; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:600;">Cancelar</button>
        <button type="submit" style="background:#22c55e; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:700;">Guardar Ingreso</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL EGRESO --}}
<div id="modalEgreso" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; justify-content:center; align-items:center;">
  <div style="background:#fff; border-radius:12px; padding:25px; width:100%; max-width:480px;">
    <h3 style="margin-top:0; color:#b91c1c; font-weight:800;">🔴 Registrar Egreso / Gasto de Caja Chica</h3>
    <form action="{{ route('caja.movimiento.store') }}" method="POST">
      @csrf
      <input type="hidden" name="tipo" value="egreso">
      
      <div style="margin-bottom:12px;">
        <label style="font-weight:600; font-size:0.85rem;">Categoría de Egreso:</label>
        <select name="categoria" id="categoriaEgresoSelect" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1;" required>
          <option value="Gastos Operativos">Gastos Operativos (Agua, Limpieza)</option>
          <option value="Compra Menor">Compra Menor / Insumos</option>
          <option value="Servicio Rápido">Servicio Rápido / Flete</option>
          <option value="Viáticos">Viáticos / Alimentación</option>
          <option value="Pago a Proveedor">Pago a Proveedor</option>
          <option value="Salida Varios">Otros Egresos</option>
        </select>
      </div>

      <div style="margin-bottom:12px;">
        <label style="font-weight:600; font-size:0.85rem;">Descripción:</label>
        <input type="text" name="descripcion" placeholder="Ej: Compra de garrafón de agua y café" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1;" required>
      </div>

      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:12px;">
        <div>
          <label style="font-weight:600; font-size:0.85rem;">Monto ($):</label>
          <input type="number" step="0.01" name="monto" placeholder="0.00" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1;" required>
        </div>
        <div>
          <label style="font-weight:600; font-size:0.85rem;">Pagado a:</label>
          <input type="text" name="pago_a" placeholder="Nombre de proveedor/persona" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1;">
        </div>
      </div>

      <div style="margin-bottom:15px;">
        <label style="font-weight:600; font-size:0.85rem;">N° Comprobante / Factura / Ticket:</label>
        <input type="text" name="comprobante" placeholder="Ej: FAC-4412" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1;">
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px;">
        <button type="button" onclick="closeModal('modalEgreso')" style="background:#e2e8f0; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:600;">Cancelar</button>
        <button type="submit" style="background:#ef4444; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:700;">Guardar Egreso</button>
      </div>
    </form>
  </div>
</div>

{{-- MODAL VOUCHER POS --}}
<div id="modalVoucher" class="modal" style="display:none; position:fixed; top:0; left:0; width:100%; height:100%; background:rgba(0,0,0,0.5); z-index:999; justify-content:center; align-items:center;">
  <div style="background:#fff; border-radius:12px; padding:25px; width:100%; max-width:480px;">
    <h3 style="margin-top:0; color:#ca8a04; font-weight:800;">💳 Registrar Voucher POS (Tarjeta)</h3>
    <form action="{{ route('caja.voucher.store') }}" method="POST">
      @csrf
      
      <div style="margin-bottom:12px;">
        <label style="font-weight:600; font-size:0.85rem;">Banco Emisor POS:</label>
        <select name="banco" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1;" required>
          <option value="BAC Credomatic">BAC Credomatic</option>
          <option value="Bancoagrícola">Bancoagrícola</option>
          <option value="Banco Cuscatlán">Banco Cuscatlán</option>
          <option value="Davivienda / Serfinsa">Davivienda / Serfinsa</option>
          <option value="Otro POS">Otro POS</option>
        </select>
      </div>

      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:12px;">
        <div>
          <label style="font-weight:600; font-size:0.85rem;">N° de Lote:</label>
          <input type="text" name="numero_lote" placeholder="Ej: 00124" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1;" required>
        </div>
        <div>
          <label style="font-weight:600; font-size:0.85rem;">N° de Voucher:</label>
          <input type="text" name="numero_voucher" placeholder="Ej: 884912" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1;" required>
        </div>
      </div>

      <div style="display:grid; grid-template-columns: 1fr 1fr; gap:12px; margin-bottom:15px;">
        <div>
          <label style="font-weight:600; font-size:0.85rem;">Monto Cobrado ($):</label>
          <input type="number" step="0.01" name="monto" placeholder="0.00" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1;" required>
        </div>
        <div>
          <label style="font-weight:600; font-size:0.85rem;">Comisión Bancaria (%):</label>
          <input type="number" step="0.01" name="comision" value="2.5" style="width:100%; padding:8px; border-radius:6px; border:1px solid #cbd5e1;">
        </div>
      </div>

      <div style="display:flex; justify-content:flex-end; gap:10px;">
        <button type="button" onclick="closeModal('modalVoucher')" style="background:#e2e8f0; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:600;">Cancelar</button>
        <button type="submit" style="background:#eab308; color:#fff; border:none; padding:8px 16px; border-radius:6px; cursor:pointer; font-weight:700;">Registrar Voucher</button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal(id) {
  document.getElementById(id).style.display = 'flex';
}
function closeModal(id) {
  document.getElementById(id).style.display = 'none';
}
function setCategoria(cat) {
  document.getElementById('categoriaEgresoSelect').value = cat;
}
</script>
@endsection
