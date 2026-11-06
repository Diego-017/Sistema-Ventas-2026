<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Comprobante de Traslado No {{ $traslado->id }}</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; font-family:'Helvetica Neue',Helvetica,Arial,sans-serif; }
  body { background:#f1f5f9; padding:24px; color:#1e293b; font-size:11px; }
  .pdf-container { max-width:820px; margin:0 auto; background:#fff; padding:28px; border-radius:6px; box-shadow:0 4px 15px rgba(0,0,0,0.08); }
  .pdf-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:18px; border-bottom:2px solid #0f172a; padding-bottom:12px; }
  .brand-title { font-size:20px; font-weight:900; color:#0f172a; text-transform:uppercase; letter-spacing:0.5px; }
  .doc-info { text-align:right; }
  .doc-title { font-size:16px; font-weight:900; color:#0f172a; text-transform:uppercase; }
  .doc-date { font-size:10px; color:#64748b; margin-top:2px; }
  
  .meta-grid { display:grid; grid-template-columns:1fr 1fr; gap:12px; margin-bottom:20px; font-size:11px; line-height:1.6; }
  .meta-card { background:#f8fafc; border:1px solid #cbd5e1; border-radius:6px; padding:10px 14px; }
  
  .table-pdf { width:100%; border-collapse:collapse; margin-bottom:18px; font-size:10.5px; }
  .table-pdf th { background:#f8fafc; color:#0f172a; padding:8px; font-weight:800; text-transform:uppercase; font-size:10px; border:1px solid #cbd5e1; }
  .table-pdf td { padding:7px 8px; border:1px solid #cbd5e1; vertical-align:middle; }
  .text-right { text-align:right; }
  .text-center { text-align:center; }
  
  .total-row { background:#f8fafc; font-weight:800; font-size:12px; }
  
  .no-print-bar { max-width:820px; margin:0 auto 16px; display:flex; justify-content:space-between; align-items:center; }
  .btn-print { background:#2563eb; color:#fff; border:none; padding:8px 18px; border-radius:6px; font-weight:800; cursor:pointer; font-size:12px; }
  .btn-back { background:#64748b; color:#fff; text-decoration:none; padding:8px 16px; border-radius:6px; font-weight:700; font-size:12px; }

  @media print {
    body { background:#fff; padding:0; }
    .pdf-container { box-shadow:none; padding:0; border-radius:0; max-width:100%; }
    .no-print-bar { display:none !important; }
    @page { margin: 15mm 12mm; }
  }
</style>
</head>
<body>

<div class="no-print-bar">
  <a href="{{ url()->previous() ?: route('inventario.traslados') }}" class="btn-back">← Volver</a>
  <button onclick="window.print()" class="btn-print">🖨️ Imprimir / Guardar como PDF</button>
</div>

<div class="pdf-container">
  <div class="pdf-header">
    <div>
      <div class="brand-title">{{ $config['nombre_negocio'] ?? 'DIGITALSPOS' }}</div>
      <div style="font-size:10px; color:#64748b">{{ $config['eslogan'] ?? 'Control de Inventario y Mercadería' }}</div>
    </div>
    <div class="doc-info">
      <div class="doc-title">TRASLADO No {{ $traslado->id }}</div>
      <div class="doc-date">Fecha de impresión {{ now()->format('d/m/Y H:i') }}</div>
    </div>
  </div>

  <div class="meta-grid">
    <div class="meta-card">
      <div><strong>REGISTRADO POR:</strong> {{ strtoupper($traslado->usuario->nombre ?? 'USUARIO') }}</div>
      <div><strong>CONCEPTO:</strong> {{ strtoupper($traslado->concepto ?? 'TRASLADO INTERNO') }}</div>
      <div><strong>TIPO OPERACIÓN:</strong> <span style="text-transform:uppercase; font-weight:700">{{ $traslado->tipo }}</span></div>
    </div>
    <div class="meta-card">
      <div><strong>SUCURSAL ORIGEN:</strong> Sucursal Central</div>
      <div><strong>SUCURSAL DESTINO:</strong> Bodega Principal</div>
      <div><strong>FECHA EMISIÓN:</strong> {{ $traslado->created_at->format('d/m/Y H:i:s') }}</div>
    </div>
  </div>

  <table class="table-pdf">
    <thead>
      <tr>
        <th>Descripción del Producto</th>
        <th class="text-center" style="width:110px">Presentación</th>
        <th class="text-right" style="width:95px">P. Unitario</th>
        <th class="text-center" style="width:80px">Cant.</th>
        <th class="text-right" style="width:110px">Total</th>
      </tr>
    </thead>
    <tbody>
      @php $totalGral = 0; @endphp
      @forelse($traslado->items as $it)
      @php
        $precioUnit = $it->producto->precio_venta ?? 0;
        $subt = abs($it->cantidad_ajuste) * $precioUnit;
        $totalGral += $subt;
      @endphp
      <tr>
        <td>
          <strong>{{ $it->producto->nombre ?? 'Producto #'.$it->producto_id }}</strong>
          @if($it->producto && $it->producto->sku)
            <div style="font-size:9.5px; color:#64748b">SKU: {{ $it->producto->sku }}</div>
          @endif
        </td>
        <td class="text-center">{{ $it->producto->presentacion ?? 'UNIDAD' }}</td>
        <td class="text-right">${{ number_format($precioUnit, 2) }}</td>
        <td class="text-center"><strong>{{ abs($it->cantidad_ajuste) }}</strong></td>
        <td class="text-right"><strong>${{ number_format($subt, 2) }}</strong></td>
      </tr>
      @empty
      <tr>
        <td colspan="5" class="text-center">Sin ítems registrados en el traslado.</td>
      </tr>
      @endforelse
      <tr class="total-row">
        <td colspan="4" class="text-right" style="font-weight:900; font-size:13px">Total:</td>
        <td class="text-right" style="font-weight:900; font-size:13px; color:#0f172a">US${{ number_format($totalGral, 2) }}</td>
      </tr>
    </tbody>
  </table>

  <div style="margin-top:50px; display:grid; grid-template-columns:1fr 1fr; gap:50px; text-align:center; font-size:10px; color:#475569">
    <div>
      <div style="border-top:1px solid #94a3b8; padding-top:6px; margin:0 30px">
        Firma Responsable Sucursal Origen
      </div>
    </div>
    <div>
      <div style="border-top:1px solid #94a3b8; padding-top:6px; margin:0 30px">
        Firma Recibido Sucursal Destino
      </div>
    </div>
  </div>
</div>

</body>
</html>
