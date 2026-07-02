@extends('layouts.main')
@section('title','Detalle de Cotización')
@section('content')
<div class="page-header">
  <h1>📄 {{ $cotizacion->codigo }}</h1>
  <div style="display:flex;gap:8px">
    <button onclick="window.print()" class="btn btn-secondary">🖨️ Imprimir</button>
    <a href="{{ route('cotizaciones.index') }}" class="btn btn-secondary">← Volver</a>
    @if($cotizacion->estado==='vigente')
    <form method="POST" action="{{ route('cotizaciones.convertir',$cotizacion->id) }}"
          onsubmit="return confirm('¿Convertir a venta?')">
      @csrf
      <button type="submit" class="btn btn-success">🛍️ Convertir a Venta</button>
    </form>
    @endif
  </div>
</div>

<div class="card receipt">
  <div class="receipt-header">
    <div class="receipt-logo">📝</div>
    <h2>Cotización</h2>
    <p class="receipt-code">{{ $cotizacion->codigo }}</p>
    <p>{{ $cotizacion->created_at->format('d/m/Y H:i') }}</p>
    <span class="badge badge-{{ $cotizacion->estado==='vigente'?'success':($cotizacion->estado==='convertida'?'primary':'danger') }}">
      {{ strtoupper($cotizacion->estado) }}
    </span>
  </div>
  <div class="receipt-meta">
    @if($cotizacion->cliente)
    <div class="meta-row"><span>Cliente:</span><span>{{ $cotizacion->cliente->nombre }}</span></div>
    @endif
    <div class="meta-row"><span>Generado por:</span><span>{{ $cotizacion->usuario->nombre }}</span></div>
    @if($cotizacion->valida_hasta)
    <div class="meta-row"><span>Válida hasta:</span><span>{{ $cotizacion->valida_hasta->format('d/m/Y') }}</span></div>
    @endif
    @if($cotizacion->notas)
    <div class="meta-row"><span>Notas:</span><span>{{ $cotizacion->notas }}</span></div>
    @endif
  </div>
  <table class="table">
    <thead><tr><th>Producto</th><th style="text-align:center">Cant.</th><th style="text-align:right">Precio</th><th style="text-align:right">Subtotal</th></tr></thead>
    <tbody>
      @foreach($cotizacion->items as $item)
      <tr>
        <td>{{ $item->nombre_producto }}</td>
        <td style="text-align:center">{{ $item->cantidad }}</td>
        <td style="text-align:right">${{ number_format($item->precio_unitario,2) }}</td>
        <td style="text-align:right">${{ number_format($item->subtotal,2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <div class="receipt-footer">
    <div class="total-row"><span>Subtotal:</span><span>${{ number_format($cotizacion->subtotal,2) }}</span></div>
    @if($cotizacion->descuento > 0)
    <div class="total-row" style="color:#dc2626"><span>Descuento:</span><span>-${{ number_format($cotizacion->descuento,2) }}</span></div>
    @endif
    <div class="total-row total-final"><span>TOTAL:</span><span>${{ number_format($cotizacion->total,2) }}</span></div>
  </div>
</div>
@endsection
