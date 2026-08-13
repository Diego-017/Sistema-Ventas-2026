@extends('layouts.main')
@section('title','Detalle de Compra')
@section('content')
<div class="page-header">
  <h1>🛒 {{ $compra->codigo }}</h1>
  <div>
    <button onclick="window.print()" class="btn btn-secondary">🖨️ Imprimir</button>
    <a href="{{ route('compras.index') }}" class="btn btn-secondary">← Volver</a>
  </div>
</div>
<div class="card receipt">
  <div class="receipt-header">
    <h2>Orden de Compra</h2>
    <p class="receipt-code">{{ $compra->codigo }}</p>
    <p>{{ $compra->created_at->format('d/m/Y H:i') }}</p>
    @if($compra->proveedor)<p>Proveedor: {{ $compra->proveedor->nombre }}</p>@endif
    <p>Registrado por: {{ $compra->usuario->nombre }}</p>
    <span class="badge badge-{{ $compra->estado==='completada'?'success':'danger' }}">{{ strtoupper($compra->estado) }}</span>
  </div>
  <table class="table">
    <thead><tr><th>Producto</th><th>Cant.</th><th>Precio Unit.</th><th>Subtotal</th></tr></thead>
    <tbody>
      @foreach($compra->items as $item)
      <tr>
        <td>{{ $item->nombre_producto }}</td>
        <td>{{ $item->cantidad }}</td>
        <td>${{ number_format($item->precio_unitario,2) }}</td>
        <td>${{ number_format($item->subtotal,2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <div class="receipt-footer">
    <div class="total-row total-final"><span>TOTAL:</span><span>${{ number_format($compra->total,2) }}</span></div>
  </div>
</div>
@endsection
