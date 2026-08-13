@extends('layouts.main')
@section('title','Corte de Caja')
@section('content')
<div class="page-header">
  <h1>📋 Corte de Caja — {{ $caja->nombre }}</h1>
  <div>
    <button onclick="window.print()" class="btn btn-light">🖨️ Imprimir</button>
    <a href="{{ route('caja.historial') }}" class="btn btn-light">← Historial</a>
  </div>
</div>

<div class="card receipt">
  <div class="receipt-header">
    <div class="receipt-logo">📋</div>
    <h2>Corte de Caja</h2>
    <p>{{ $caja->abierta_at->format('d/m/Y') }}</p>
  </div>
  <div class="receipt-meta">
    <div class="meta-row"><span>Abierta por:</span><span>{{ $caja->usuario->nombre }}</span></div>
    <div class="meta-row"><span>Apertura:</span><span>{{ $caja->abierta_at->format('H:i') }}</span></div>
    <div class="meta-row"><span>Cierre:</span><span>{{ $caja->cerrada_at?->format('H:i') ?? 'N/A' }}</span></div>
  </div>

  @if($corte)
  <table class="table">
    <tbody>
      <tr><td>Monto Apertura</td><td class="text-right">${{ number_format($caja->monto_apertura,2) }}</td></tr>
      <tr><td>Ventas Efectivo</td><td class="text-right text-success">+${{ number_format($corte->ventas_efectivo,2) }}</td></tr>
      <tr><td>Ventas Tarjeta</td><td class="text-right">${{ number_format($corte->ventas_tarjeta,2) }}</td></tr>
      <tr><td>Ventas Transferencia</td><td class="text-right">${{ number_format($corte->ventas_transferencia,2) }}</td></tr>
      <tr><td>Ventas Crédito</td><td class="text-right">${{ number_format($corte->ventas_credito,2) }}</td></tr>
      <tr><td>Gastos/Retiros</td><td class="text-right text-danger">-${{ number_format($caja->total_gastos,2) }}</td></tr>
    </tbody>
  </table>
  <div class="receipt-footer">
    <div class="total-row"><span>Efectivo Esperado:</span><span>${{ number_format($corte->efectivo_esperado,2) }}</span></div>
    <div class="total-row"><span>Efectivo Contado:</span><span>${{ number_format($corte->efectivo_contado,2) }}</span></div>
    <div class="total-row total-final">
      <span>Diferencia:</span>
      <span class="{{ $corte->diferencia >= 0 ? 'text-success':'text-danger' }}">
        {{ $corte->diferencia >= 0 ? '+':'' }}${{ number_format($corte->diferencia,2) }}
      </span>
    </div>
  </div>
  @if($corte->notas)
  <div style="padding:16px"><strong>Notas:</strong> {{ $corte->notas }}</div>
  @endif
  @else
  <div class="empty-state">Esta caja aún no tiene un corte registrado (sigue abierta).</div>
  @endif
</div>
@endsection
