<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Comprobante de Compra {{ $compra->codigo }}</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; font-family:'Helvetica Neue',Helvetica,Arial,sans-serif; }
  body { background:#f1f5f9; padding:24px; color:#1e293b; font-size:11px; }
  .pdf-container { max-width:820px; margin:0 auto; background:#fff; padding:28px; border-radius:6px; box-shadow:0 4px 15px rgba(0,0,0,0.08); }
  .pdf-header { text-align:right; margin-bottom:16px; border-bottom:2px solid #0f172a; padding-bottom:10px; }
  .doc-title { font-size:18px; font-weight:900; color:#0f172a; text-transform:uppercase; letter-spacing:0.5px; }
  .doc-date { font-size:10px; color:#64748b; margin-top:2px; }
  
  .meta-section { margin-bottom:18px; line-height:1.6; font-size:11px; }
  .meta-row { display:flex; margin-bottom:4px; }
  .meta-label { width:170px; font-weight:800; color:#0f172a; text-transform:uppercase; }
  .meta-value { flex:1; color:#334155; }
  
  .table-pdf { width:100%; border-collapse:collapse; margin-bottom:16px; font-size:10.5px; }
  .table-pdf th { background:#f8fafc; color:#0f172a; padding:8px; font-weight:800; text-transform:uppercase; font-size:10px; border:1px solid #cbd5e1; }
  .table-pdf td { padding:7px 8px; border:1px solid #cbd5e1; vertical-align:middle; }
  .text-right { text-align:right; }
  .text-center { text-align:center; }
  
  .total-row { background:#f8fafc; font-weight:800; }
  .grand-total { font-size:13px; font-weight:900; color:#0f172a; }
  
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
  <a href="{{ url()->previous() ?: route('compras.index') }}" class="btn-back">← Volver</a>
  <button onclick="window.print()" class="btn-print"><i class="fa-solid fa-print"></i> Imprimir / Guardar como PDF</button>
</div>

<div class="pdf-container">
  <div class="pdf-header">
    <div class="doc-title">COMPRA {{ $compra->codigo }}</div>
    <div class="doc-date">Fecha de impresión {{ now()->format('d/m/Y H:i') }}</div>
  </div>

  <div class="meta-section">
    <div class="meta-row">
      <span class="meta-label">REGISTRADO POR:</span>
      <span class="meta-value">{{ strtoupper($compra->usuario->nombre ?? 'ADMINISTRADOR') }}</span>
    </div>
    <div class="meta-row">
      <span class="meta-label">CONCEPTO:</span>
      <span class="meta-value">{{ strtoupper($compra->notas ?? 'COMPRA DE MERCADERÍA - ABASTECIMIENTO DE INVENTARIO') }}</span>
    </div>
    <div class="meta-row">
      <span class="meta-label">PROVEEDOR:</span>
      <span class="meta-value">{{ strtoupper($compra->proveedor->nombre ?? 'PROVEEDOR GENERAL') }}</span>
    </div>
    <div class="meta-row">
      <span class="meta-label">FECHA DE REGISTRO:</span>
      <span class="meta-value">{{ $compra->created_at->format('d/m/Y H:i:s') }}</span>
    </div>
  </div>

  <table class="table-pdf">
    <thead>
      <tr>
        <th class="text-center" style="width:55px">Cant.</th>
        <th style="width:110px">Código</th>
        <th>Descripción del Producto</th>
        <th class="text-center" style="width:100px">Presentación</th>
        <th class="text-right" style="width:95px">P. Unitario</th>
        <th class="text-right" style="width:105px">Total</th>
      </tr>
    </thead>
    <tbody>
      @forelse($compra->items as $item)
      <tr>
        <td class="text-center"><strong>{{ $item->cantidad }}</strong></td>
        <td style="font-family:monospace">{{ $item->producto->sku ?? 'SKU-'.$item->producto_id }}</td>
        <td><strong>{{ $item->nombre_producto }}</strong></td>
        <td class="text-center">{{ $item->producto->presentacion ?? 'UNIDAD' }}</td>
        <td class="text-right">${{ number_format($item->precio_unitario, 2) }}</td>
        <td class="text-right"><strong>${{ number_format($item->subtotal, 2) }}</strong></td>
      </tr>
      @empty
      <tr>
        <td colspan="6" class="text-center">Sin productos registrados en la orden.</td>
      </tr>
      @endforelse
      <tr class="total-row">
        <td colspan="5" class="text-right grand-total">Total:</td>
        <td class="text-right grand-total">US${{ number_format($compra->total, 2) }}</td>
      </tr>
    </tbody>
  </table>

  <div style="margin-top:40px; display:grid; grid-template-columns:1fr 1fr; gap:40px; text-align:center; font-size:10px; color:#475569">
    <div>
      <div style="border-top:1px solid #94a3b8; padding-top:6px; margin:0 30px">
        Firma Responsable de Almacén / Compras
      </div>
    </div>
    <div>
      <div style="border-top:1px solid #94a3b8; padding-top:6px; margin:0 30px">
        Firma del Proveedor / Entregado por
      </div>
    </div>
  </div>
</div>

</body>
</html>
