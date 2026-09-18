@extends('layouts.main')
@section('title', 'Cuadraturas y Arqueos — DigitalsPos')

@section('content')
<div class="page-header" style="margin-bottom: 20px;">
  <h1 style="margin: 0; font-size: 1.8rem; font-weight: 800; color: #1e293b;">⚖️ Cuadraturas y Arqueos de Caja</h1>
  <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">Conciliación entre ventas del sistema, dinero en efectivo contado y vouchers POS bancarios.</p>
</div>

<div style="display: grid; grid-template-columns: 1fr 2fr; gap: 25px;">
  <!-- FORMULARIO DE CUADRATURA -->
  <div class="card" style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); height: fit-content;">
    <h3 style="margin-top: 0; font-size: 1.1rem; color: #0f172a; font-weight: 700; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">Procesar Cuadratura de Turno</h3>

    <form action="{{ route('caja.cuadraturas.store') }}" method="POST">
      @csrf

      <div style="background: #f8fafc; padding: 12px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid #2563eb;">
        <div style="font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Efectivo en Sistema (Hoy)</div>
        <div style="font-size: 1.3rem; font-weight: 800; color: #1e293b;">${{ number_format($efectivoSistema, 2) }}</div>
      </div>

      <div style="margin-bottom: 15px;">
        <label style="font-weight: 600; font-size: 0.85rem; color: #334155;">Efectivo Real Físico en Caja ($):</label>
        <input type="number" step="0.01" name="efectivo_real" value="{{ $efectivoSistema }}" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 1rem; font-weight: 700; color: #0f172a;" required>
      </div>

      <div style="background: #f8fafc; padding: 12px; border-radius: 8px; margin-bottom: 15px; border-left: 4px solid #eab308;">
        <div style="font-size: 0.8rem; color: #64748b; font-weight: 700; text-transform: uppercase;">Tarjetas en Sistema (Hoy)</div>
        <div style="font-size: 1.3rem; font-weight: 800; color: #1e293b;">${{ number_format($tarjetaSistema, 2) }}</div>
        <div style="font-size: 0.75rem; color: #854d0e; margin-top: 2px;">Vouchers Registrados: ${{ number_format($vouchersRegistrados, 2) }}</div>
      </div>

      <div style="margin-bottom: 15px;">
        <label style="font-weight: 600; font-size: 0.85rem; color: #334155;">Suma Total de Vouchers Físicos POS ($):</label>
        <input type="number" step="0.01" name="tarjeta_real_vouchers" value="{{ $vouchersRegistrados }}" style="width: 100%; padding: 10px; border-radius: 6px; border: 1px solid #cbd5e1; font-size: 1rem; font-weight: 700; color: #0f172a;" required>
      </div>

      <div style="margin-bottom: 15px;">
        <label style="font-weight: 600; font-size: 0.85rem; color: #334155;">Observaciones / Justificación de Descuadre:</label>
        <textarea name="observaciones" rows="3" placeholder="Ej: Faltante de $0.50 por diferencia en cambio" style="width: 100%; padding: 8px; border-radius: 6px; border: 1px solid #cbd5e1;"></textarea>
      </div>

      <button type="submit" style="width: 100%; background: #2563eb; color: #fff; border: none; padding: 12px; border-radius: 8px; font-weight: 700; cursor: pointer;">
        ✓ Guardar Cuadratura de Caja
      </button>
    </form>
  </div>

  <!-- HISTORIAL DE CUADRATURAS -->
  <div class="card" style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
    <h3 style="margin-top: 0; font-size: 1.1rem; color: #0f172a; font-weight: 700; border-bottom: 2px solid #f1f5f9; padding-bottom: 10px;">Historial de Cuadraturas Registradas</h3>

    <div style="overflow-x: auto;">
      <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
        <thead>
          <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569;">
            <th style="padding: 8px;">Fecha</th>
            <th style="padding: 8px;">Usuario</th>
            <th style="padding: 8px;">Ef. Sistema</th>
            <th style="padding: 8px;">Ef. Real</th>
            <th style="padding: 8px;">Tarj. Sistema</th>
            <th style="padding: 8px;">Vouchers</th>
            <th style="padding: 8px;">Diferencia</th>
            <th style="padding: 8px;">Estado</th>
          </tr>
        </thead>
        <tbody>
          @forelse($cuadraturas as $c)
          <tr style="border-bottom: 1px solid #f1f5f9;">
            <td style="padding: 8px; font-weight: 600;">{{ $c->created_at->format('d/m/Y H:i') }}</td>
            <td style="padding: 8px;">{{ $c->usuario->nombre ?? 'N/A' }}</td>
            <td style="padding: 8px;">${{ number_format($c->efectivo_sistema, 2) }}</td>
            <td style="padding: 8px; font-weight: 700;">${{ number_format($c->efectivo_real, 2) }}</td>
            <td style="padding: 8px;">${{ number_format($c->tarjeta_sistema, 2) }}</td>
            <td style="padding: 8px; font-weight: 700;">${{ number_format($c->tarjeta_real_vouchers, 2) }}</td>
            <td style="padding: 8px; font-weight: 800; color: {{ $c->diferencia == 0 ? '#16a34a' : ($c->diferencia > 0 ? '#2563eb' : '#dc2626') }};">
              {{ $c->diferencia > 0 ? '+' : '' }}${{ number_format($c->diferencia, 2) }}
            </td>
            <td style="padding: 8px;">
              @if($c->estado_cuadratura === 'cuadrada')
                <span style="background: #dcfce7; color: #166534; padding: 2px 6px; border-radius: 4px; font-weight: 700; font-size: 0.75rem;">✓ Cuadrada</span>
              @else
                <span style="background: #fee2e2; color: #b91c1c; padding: 2px 6px; border-radius: 4px; font-weight: 700; font-size: 0.75rem;">⚠ Descuadre</span>
              @endif
            </td>
          </tr>
          @empty
          <tr>
            <td colspan="8" style="text-align: center; padding: 25px; color: #94a3b8;">No se han registrado cuadraturas de caja aún.</td>
          </tr>
          @endforelse
        </tbody>
      </table>
    </div>
    <div style="margin-top: 15px;">{{ $cuadraturas->links() }}</div>
  </div>
</div>
@endsection
