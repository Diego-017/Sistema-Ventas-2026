<!DOCTYPE html>
<html>
<head>
<meta charset="UTF-8">
<title>Ticket {{ $venta->codigo }}</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; font-family:'Courier New',monospace; }
  body { width: {{ ($impresora->ancho_papel ?? 80) }}mm; padding:8px; font-size:11px; }
  .center { text-align:center; }
  .bold { font-weight:bold; }
  .sep { border-top:1px dashed #000; margin:6px 0; }
  table { width:100%; border-collapse:collapse; font-size:10px; }
  td { padding:2px 0; }
  .right { text-align:right; }
  .total { font-size:13px; font-weight:bold; }
  @media print { @page { margin:0; } }
</style>
</head>
<body onload="window.print()">
  <div class="center bold" style="font-size:14px">{{ $config['nombre_negocio'] ?? 'DigitalsPos' }}</div>
  <div class="center">{{ $config['direccion'] ?? '' }}</div>
  <div class="center">Tel: {{ $config['telefono'] ?? '' }}</div>
  <div class="sep"></div>
  <div>Ticket: {{ $venta->codigo }}</div>
  <div>Fecha: {{ $venta->created_at->format('d/m/Y H:i') }}</div>
  <div>Cajero: {{ $venta->usuario->nombre }}</div>
  @if($venta->cliente)<div>Cliente: {{ $venta->cliente->nombre }}</div>@endif
  <div class="sep"></div>
  <table>
    @foreach($venta->items as $item)
    <tr>
      <td colspan="3">{{ $item->nombre_producto }}</td>
    </tr>
    <tr>
      <td>{{ $item->cantidad }} x ${{ number_format($item->precio_unitario,2) }}</td>
      <td></td>
      <td class="right">${{ number_format($item->subtotal,2) }}</td>
    </tr>
    @endforeach
  </table>
  <div class="sep"></div>
  <table>
    <tr><td>Subtotal:</td><td class="right">${{ number_format($venta->subtotal,2) }}</td></tr>
    @if($venta->descuento > 0)
    <tr><td>Descuento:</td><td class="right">-${{ number_format($venta->descuento,2) }}</td></tr>
    @endif
    <tr class="total"><td>TOTAL:</td><td class="right">${{ number_format($venta->total,2) }}</td></tr>
  </table>
  <div class="sep"></div>
  <div class="center">¡Gracias por su compra!</div>
  <div class="center" style="font-size:9px">{{ $config['eslogan'] ?? 'DigitalsPos' }}</div>
</body>
</html>
