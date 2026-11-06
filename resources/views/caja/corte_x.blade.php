<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Ticket Corte X — {{ $caja->nombre }}</title>
<style>
  * { box-sizing: border-box; margin: 0; padding: 0; font-family: 'Courier New', Courier, monospace; }
  body { width: 80mm; margin: 0 auto; padding: 12px 6px; font-size: 12px; color: #000; background: #fff; }
  .text-center { text-align: center; }
  .text-right { text-align: right; }
  .bold { font-weight: bold; }
  .divider { border-bottom: 1px dashed #000; margin: 6px 0; }
  .divider-double { border-bottom: 2px solid #000; margin: 6px 0; }
  .row { display: flex; justify-content: space-between; margin-bottom: 3px; font-size: 11.5px; }
  .highlight { font-size: 13px; font-weight: 800; }
  .btn-print {
    display: block; width: 100%; padding: 8px; margin-bottom: 12px;
    background: #0284c7; color: #fff; text-align: center;
    text-decoration: none; font-weight: bold; border-radius: 4px;
  }
  @media print {
    .btn-print { display: none; }
    body { width: 100%; padding: 0; }
  }
</style>
</head>
<body>

<a href="#" onclick="window.print()" class="btn-print">🖨️ IMPRIMIR CORTE X</a>

<div class="text-center">
  <div class="bold" style="font-size:15px">{{ \App\Models\Configuracion::get('nombre_negocio', 'SUPER EMMANUEL') }}</div>
  <div>{{ \App\Models\Configuracion::get('direccion', 'EL SALVADOR') }}</div>
  <div>NRC: {{ \App\Models\Configuracion::get('nrc', '000000-0') }} | NIT: {{ \App\Models\Configuracion::get('nit', '0000-000000-000-0') }}</div>
  <div class="divider"></div>
  <div class="bold" style="font-size:14px">REPORTE PARCIAL DE CORTE X</div>
  <div style="font-size:10px; margin-top:2px">LECTURA INTERMEDIA DE TURNO</div>
</div>

<div class="divider"></div>

<div class="row"><span>FECHA/HORA:</span><span>{{ now()->format('d/m/Y h:i A') }}</span></div>
<div class="row"><span>CAJA:</span><span class="bold">{{ $caja->nombre }}</span></div>
<div class="row"><span>CAJERO:</span><span>{{ $caja->usuario->nombre ?? 'N/A' }}</span></div>
<div class="row"><span>APERTURA:</span><span>{{ $caja->abierta_at->format('d/m/Y H:i') }}</span></div>

<div class="divider"></div>

<div class="text-center bold" style="margin:4px 0">DESGLOSE DE VENTAS POR MEDIO</div>
<div class="row"><span>VENTAS EFECTIVO:</span><span class="bold">${{ number_format($efectivoVentas, 2) }}</span></div>
<div class="row"><span>VENTAS TARJETA:</span><span class="bold">${{ number_format($tarjetaVentas, 2) }}</span></div>
<div class="row"><span>VENTAS TRANSFERENCIA:</span><span class="bold">${{ number_format($transfVentas, 2) }}</span></div>
<div class="row"><span>VENTAS CRÉDITO:</span><span class="bold">${{ number_format($creditoVentas, 2) }}</span></div>

<div class="divider"></div>

<div class="row"><span>FONDO APERTURA:</span><span>${{ number_format($caja->monto_apertura, 2) }}</span></div>
<div class="row"><span>(+) INGRESOS CAJA CHICA:</span><span>+${{ number_format($ingresosExtra, 2) }}</span></div>
<div class="row"><span>(-) EGRESOS / GASTOS:</span><span>-${{ number_format($egresosGastos, 2) }}</span></div>

<div class="divider-double"></div>

<div class="row highlight">
  <span>TOTAL EN GAVETA:</span>
  <span>${{ number_format($efectivoEsperado, 2) }}</span>
</div>

<div class="row" style="margin-top:6px">
  <span>TOTAL VENDIDO TURNO:</span>
  <span class="bold">${{ number_format($caja->total_ventas, 2) }}</span>
</div>

<div class="divider"></div>

<div class="text-center" style="font-size:10px; color:#333; margin-top:10px">
  *** LECTURA INFORMATIVA ***<br>
  ESTE CORTE NO CIERRA LA CAJA NI EL DÍA FISCAL
</div>

<script>
  window.onload = function() {
    // Si se desea auto imprimir: window.print();
  }
</script>
</body>
</html>
