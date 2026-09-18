@extends('layouts.main')
@section('title', 'Tabla de Márgenes — DigitalsPos')

@section('content')
<div class="page-header" style="margin-bottom: 20px;">
  <h1 style="margin: 0; font-size: 1.8rem; font-weight: 800; color: #1e293b;">% Tabla de Márgenes y Rentabilidad</h1>
  <p style="color: #64748b; font-size: 0.9rem; margin-top: 4px;">Análisis de rentabilidad por producto, precio de compra vs precio de venta y margen neto de ganancia.</p>
</div>

<div class="card" style="background: #fff; border-radius: 12px; padding: 20px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
  <div style="overflow-x: auto;">
    <table style="width: 100%; border-collapse: collapse; text-align: left; font-size: 0.9rem;">
      <thead>
        <tr style="background: #f8fafc; border-bottom: 2px solid #e2e8f0; color: #475569;">
          <th style="padding: 10px;">SKU</th>
          <th style="padding: 10px;">Producto</th>
          <th style="padding: 10px;">Categoría</th>
          <th style="padding: 10px;">Stock</th>
          <th style="padding: 10px;">Precio Compra ($)</th>
          <th style="padding: 10px;">Precio Venta ($)</th>
          <th style="padding: 10px;">Margen Ganancia ($)</th>
          <th style="padding: 10px;">Margen Utilidad (%)</th>
        </tr>
      </thead>
      <tbody>
        @forelse($productos as $p)
        @php
          $costo = (float) $p->precio_compra;
          $venta = (float) $p->precio;
          $margenDollar = $venta - $costo;
          $margenPorcentaje = $venta > 0 ? round(($margenDollar / $venta) * 100, 1) : 0;
        @endphp
        <tr style="border-bottom: 1px solid #f1f5f9;">
          <td style="padding: 10px; font-weight: 600; color: #64748b;">{{ $p->sku ?? 'N/A' }}</td>
          <td style="padding: 10px; font-weight: 700; color: #0f172a;">{{ $p->nombre }}</td>
          <td style="padding: 10px; color: #475569;">{{ $p->categoria->nombre ?? 'Sin categoría' }}</td>
          <td style="padding: 10px; font-weight: 600;">{{ $p->stock }} u.</td>
          <td style="padding: 10px; color: #dc2626; font-weight: 600;">${{ number_format($costo, 2) }}</td>
          <td style="padding: 10px; color: #2563eb; font-weight: 700;">${{ number_format($venta, 2) }}</td>
          <td style="padding: 10px; font-weight: 800; color: #16a34a;">+${{ number_format($margenDollar, 2) }}</td>
          <td style="padding: 10px;">
            <span style="background: {{ $margenPorcentaje >= 30 ? '#dcfce7' : ($margenPorcentaje >= 15 ? '#fef9c3' : '#fee2e2') }}; color: {{ $margenPorcentaje >= 30 ? '#15803d' : ($margenPorcentaje >= 15 ? '#854d0e' : '#b91c1c') }}; padding: 4px 8px; border-radius: 6px; font-weight: 800; font-size: 0.85rem;">
              {{ $margenPorcentaje }}%
            </span>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="8" style="text-align: center; padding: 30px; color: #94a3b8;">No se encontraron productos en el inventario.</td>
        </tr>
        @endforelse
      </tbody>
    </table>
  </div>
  <div style="margin-top: 15px;">{{ $productos->links() }}</div>
</div>
@endsection
