@extends('layouts.main')
@section('title', 'Control de Vencimientos — DigitalsPos')

@section('content')
<div class="page-header" style="margin-bottom: 20px;">
  <h1 style="margin: 0; font-size: 1.8rem; font-weight: 800; color: #1e293b;">⏳ Control de Vencimientos de Productos</h1>
  <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">Supervisión de fecha de caducidad por lotes para evitar pérdidas por caducidad en tienda comercial.</p>
</div>

{{-- KPI CARDS --}}
<div style="display: grid; grid-template-columns: repeat(3, 1fr); gap: 20px; margin-bottom: 25px;">
  <div class="card" style="background: #fee2e2; border-left: 5px solid #dc2626; border-radius: 10px; padding: 15px;">
    <div style="font-size: 0.8rem; font-weight: 700; color: #991b1b; text-transform: uppercase;">Lotes Vencidos (Acción Urgente)</div>
    <div style="font-size: 1.8rem; font-weight: 900; color: #7f1d1d;">{{ $lotesVencidos->count() }}</div>
  </div>

  <div class="card" style="background: #fef9c3; border-left: 5px solid #ca8a04; border-radius: 10px; padding: 15px;">
    <div style="font-size: 0.8rem; font-weight: 700; color: #854d0e; text-transform: uppercase;">Por Vencer (Próximos 30 Días)</div>
    <div style="font-size: 1.8rem; font-weight: 900; color: #713f12;">{{ $lotesProximos->count() }}</div>
  </div>

  <div class="card" style="background: #dcfce7; border-left: 5px solid #16a34a; border-radius: 10px; padding: 15px;">
    <div style="font-size: 0.8rem; font-weight: 700; color: #166534; text-transform: uppercase;">Lotes Vigentes (>30 Días)</div>
    <div style="font-size: 1.8rem; font-weight: 900; color: #14532d;">{{ $lotesVigentes->count() }}</div>
  </div>
</div>

{{-- TABLA DE LOTES VENCIDOS --}}
@if($lotesVencidos->count() > 0)
<div class="card" style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px; border: 1px solid #fca5a5;">
  <h3 style="margin-top: 0; font-size: 1.1rem; color: #dc2626; font-weight: 800; display: flex; align-items: center; gap: 8px;">
    🚨 Lotes Vencidos (Retirar de Anaquel)
  </h3>
  <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
    <thead>
      <tr style="background: #fee2e2; color: #991b1b;">
        <th style="padding: 8px;">Código Lote</th>
        <th style="padding: 8px;">Producto</th>
        <th style="padding: 8px;">Categoría</th>
        <th style="padding: 8px;">Stock en Lote</th>
        <th style="padding: 8px;">Fecha Vencimiento</th>
        <th style="padding: 8px;">Estado</th>
      </tr>
    </thead>
    <tbody>
      @foreach($lotesVencidos as $l)
      <tr style="border-bottom: 1px solid #fca5a5;">
        <td style="padding: 8px; font-weight: 700;">{{ $l->codigo_lote }}</td>
        <td style="padding: 8px; font-weight: 700;">{{ $l->producto->nombre ?? 'N/A' }}</td>
        <td style="padding: 8px;">{{ $l->producto->categoria->nombre ?? '-' }}</td>
        <td style="padding: 8px; font-weight: 800; color: #b91c1c;">{{ $l->cantidad_actual }} u.</td>
        <td style="padding: 8px; font-weight: 800; color: #dc2626;">{{ \Carbon\Carbon::parse($l->fecha_vencimiento)->format('d/m/Y') }}</td>
        <td style="padding: 8px;"><span style="background: #dc2626; color: #fff; padding: 2px 6px; border-radius: 4px; font-weight: 700; font-size: 0.75rem;">VENCIDO</span></td>
      </tr>
      @endforeach
    </tbody>
  </table>
</div>
@endif

{{-- TABLA DE LOTES POR VENCER (PRÓXIMOS 30 DÍAS) --}}
<div class="card" style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1); margin-bottom: 25px;">
  <h3 style="margin-top: 0; font-size: 1.1rem; color: #ca8a04; font-weight: 800; display: flex; align-items: center; gap: 8px;">
    ⚠️ Lotes Próximos a Vencer (Promocionar / Dar Salida)
  </h3>
  <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.85rem;">
    <thead>
      <tr style="background: #fef9c3; color: #854d0e;">
        <th style="padding: 8px;">Código Lote</th>
        <th style="padding: 8px;">Producto</th>
        <th style="padding: 8px;">Categoría</th>
        <th style="padding: 8px;">Stock en Lote</th>
        <th style="padding: 8px;">Fecha Vencimiento</th>
        <th style="padding: 8px;">Días Restantes</th>
      </tr>
    </thead>
    <tbody>
      @forelse($lotesProximos as $l)
      @php $dias = now()->diffInDays(\Carbon\Carbon::parse($l->fecha_vencimiento), false); @endphp
      <tr style="border-bottom: 1px solid #fef08a;">
        <td style="padding: 8px; font-weight: 700;">{{ $l->codigo_lote }}</td>
        <td style="padding: 8px; font-weight: 700;">{{ $l->producto->nombre ?? 'N/A' }}</td>
        <td style="padding: 8px;">{{ $l->producto->categoria->nombre ?? '-' }}</td>
        <td style="padding: 8px; font-weight: 800;">{{ $l->cantidad_actual }} u.</td>
        <td style="padding: 8px; font-weight: 700; color: #ca8a04;">{{ \Carbon\Carbon::parse($l->fecha_vencimiento)->format('d/m/Y') }}</td>
        <td style="padding: 8px;"><span style="background: #ca8a04; color: #fff; padding: 2px 6px; border-radius: 4px; font-weight: 700; font-size: 0.75rem;">{{ $dias }} días restantes</span></td>
      </tr>
      @empty
      <tr>
        <td colspan="6" style="text-align: center; padding: 20px; color: #94a3b8;">No hay lotes con vencimiento en los próximos 30 días.</td>
      </tr>
      @endforelse
    </tbody>
  </table>
</div>
@endsection
