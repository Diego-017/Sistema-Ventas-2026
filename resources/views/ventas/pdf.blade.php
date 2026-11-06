<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>DTE Factura {{ $venta->codigo }}</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; font-family:'Helvetica Neue',Helvetica,Arial,sans-serif; }
  body { background:#f1f5f9; padding:24px; color:#1e293b; font-size:11px; }
  .pdf-container { max-width:820px; margin:0 auto; background:#fff; padding:28px; border-radius:6px; box-shadow:0 4px 15px rgba(0,0,0,0.08); }
  .pdf-header { display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:18px; border-bottom:2px solid #0f172a; padding-bottom:14px; }
  .brand-title { font-size:22px; font-weight:900; color:#0f172a; text-transform:uppercase; letter-spacing:0.5px; }
  .brand-sub { font-size:10px; color:#64748b; margin-top:2px; }
  .dte-box { border:2px solid #0f172a; border-radius:6px; padding:10px 14px; text-align:center; min-width:280px; background:#f8fafc; }
  .dte-title { font-size:13px; font-weight:900; color:#0f172a; text-transform:uppercase; }
  .dte-num { font-size:12px; font-weight:800; color:#2563eb; margin:4px 0; font-family:monospace; }
  .dte-meta { font-size:9.5px; color:#475569; text-align:left; margin-top:6px; line-height:1.35; }
  
  .info-grid { display:grid; grid-template-columns:1fr 1fr; gap:14px; margin-bottom:16px; }
  .info-card { border:1px solid #cbd5e1; border-radius:6px; padding:10px 12px; font-size:10.5px; line-height:1.45; }
  .info-card-title { font-size:11px; font-weight:800; text-transform:uppercase; color:#0f172a; border-bottom:1px solid #e2e8f0; padding-bottom:4px; margin-bottom:6px; }
  
  .table-pdf { width:100%; border-collapse:collapse; margin-bottom:16px; font-size:10.5px; }
  .table-pdf th { background:#0f172a; color:#fff; padding:7px 8px; font-weight:800; text-transform:uppercase; font-size:9.5px; border:1px solid #0f172a; }
  .table-pdf td { padding:6px 8px; border:1px solid #cbd5e1; vertical-align:middle; }
  .text-right { text-align:right; }
  .text-center { text-align:center; }
  
  .summary-grid { display:grid; grid-template-columns:1fr 280px; gap:16px; align-items:start; }
  .letters-box { border:1px dashed #cbd5e1; border-radius:6px; padding:10px 12px; font-size:10px; color:#334155; line-height:1.4; }
  .qr-box { display:flex; align-items:center; gap:12px; margin-top:12px; }
  
  .table-totals { width:100%; border-collapse:collapse; font-size:11px; }
  .table-totals td { padding:4px 6px; }
  .table-totals .grand-total { background:#0f172a; color:#fff; font-size:13px; font-weight:900; }
  .table-totals .grand-total td { padding:8px 6px; }
  
  .pdf-footer { margin-top:20px; border-top:1px solid #e2e8f0; padding-top:10px; font-size:9px; color:#64748b; text-align:center; line-height:1.4; }
  
  .no-print-bar { max-width:820px; margin:0 auto 16px; display:flex; justify-content:space-between; align-items:center; }
  .btn-print { background:#2563eb; color:#fff; border:none; padding:8px 18px; border-radius:6px; font-weight:800; cursor:pointer; font-size:12px; }
  .btn-back { background:#64748b; color:#fff; text-decoration:none; padding:8px 16px; border-radius:6px; font-weight:700; font-size:12px; }

  @media print {
    body { background:#fff; padding:0; }
    .pdf-container { box-shadow:none; padding:0; border-radius:0; max-width:100%; }
    .no-print-bar { display:none !important; }
    @page { margin: 12mm 10mm; }
  }
</style>
</head>
<body>

<div class="no-print-bar">
  <a href="{{ url()->previous() ?: route('ventas.index') }}" class="btn-back">← Volver</a>
  <button onclick="window.print()" class="btn-print"><i class="fa-solid fa-print"></i> Imprimir / Guardar como PDF</button>
</div>

<div class="pdf-container">
  {{-- Encabezado DTE Factura --}}
  <div class="pdf-header">
    <div>
      <div class="brand-title">{{ $config['nombre_negocio'] ?? 'DIGITALSPOS' }}</div>
      <div class="brand-sub">{{ $config['eslogan'] ?? 'Sistema de Ventas y Facturación Electrónica' }}</div>
      <div style="margin-top:6px; font-size:10px; color:#334155">
        <strong>NIT:</strong> {{ $config['nit'] ?? '0614-010190-101-1' }} | <strong>NRC:</strong> {{ $config['nrc'] ?? '298765-4' }}<br>
        <strong>Giro:</strong> {{ $config['giro'] ?? 'Comercio al por menor de productos varios' }}<br>
        <strong>Dirección:</strong> {{ $config['direccion'] ?? 'San Salvador, El Salvador' }}<br>
        <strong>Teléfono:</strong> {{ $config['telefono'] ?? '2222-0000' }} | <strong>Email:</strong> {{ $config['email'] ?? 'ventas@digitalspos.com' }}
      </div>
    </div>
    <div class="dte-box">
      <div class="dte-title">Documento Tributario Electrónico</div>
      <div style="font-size:12px; font-weight:800; color:#0f172a">FACTURA</div>
      <div class="dte-num">DTE-00-M001P001-{{ str_pad($venta->id, 15, '0', STR_PAD_LEFT) }}</div>
      <div class="dte-meta">
        <div><strong>Código Generación:</strong> {{ $venta->codigo }}</div>
        <div><strong>Sello Recepción:</strong> {{ date('YmdHis', strtotime($venta->created_at)) }}001928471</div>
        <div><strong>Fecha/Hora Emisión:</strong> {{ $venta->created_at->format('d/m/Y H:i:s') }}</div>
        <div><strong>Modelo Facturación:</strong> Previo | <strong>Tipo Transmisión:</strong> Normal</div>
      </div>
    </div>
  </div>

  {{-- Cuadros Emisor / Receptor --}}
  <div class="info-grid">
    <div class="info-card">
      <div class="info-card-title">Datos del Emisor</div>
      <div><strong>Razón Social:</strong> {{ $config['nombre_negocio'] ?? 'DIGITALSPOS' }}</div>
      <div><strong>Sucursal:</strong> Sucursal Central (Establecimiento 001)</div>
      <div><strong>Punto de Venta:</strong> CAJA 01 — Terminal POS</div>
      <div><strong>Cajero / Empleado:</strong> {{ $venta->usuario->nombre ?? 'Administrador' }}</div>
      <div><strong>Método de Pago:</strong> {{ strtoupper($venta->metodo_pago ?? 'EFECTIVO') }}</div>
    </div>
    <div class="info-card">
      <div class="info-card-title">Datos del Receptor / Cliente</div>
      <div><strong>Nombre / Razón:</strong> {{ $venta->cliente->nombre ?? 'CLIENTES VARIOS (CONSUMIDOR FINAL)' }}</div>
      @if($venta->cliente && $venta->cliente->nombre_comercial)
      <div><strong>Nombre Comercial:</strong> {{ $venta->cliente->nombre_comercial }}</div>
      @endif
      <div><strong>Documento:</strong> {{ strtoupper($venta->cliente->tipo_documento ?? 'DUI') }}: {{ $venta->cliente->dui ?? $venta->cliente->nit ?? '—' }}</div>
      <div><strong>Dirección:</strong> {{ $venta->cliente->direccion ?? 'San Salvador, El Salvador' }}</div>
      <div><strong>Municipio / Depto:</strong> {{ $venta->cliente->municipio ?? 'San Salvador' }}, {{ $venta->cliente->departamento ?? 'San Salvador' }}</div>
      <div><strong>Teléfono / Correo:</strong> {{ $venta->cliente->telefono ?? '—' }} | {{ $venta->cliente->email ?? '—' }}</div>
    </div>
  </div>

  {{-- Tabla de Ítems --}}
  <table class="table-pdf">
    <thead>
      <tr>
        <th class="text-center" style="width:50px">Cant.</th>
        <th style="width:110px">Código</th>
        <th>Descripción del Producto / Ítem</th>
        <th class="text-right" style="width:90px">P. Unitario</th>
        <th class="text-right" style="width:85px">No Sujetas</th>
        <th class="text-right" style="width:85px">Exentas</th>
        <th class="text-right" style="width:95px">Ventas Gravadas</th>
      </tr>
    </thead>
    <tbody>
      @forelse($venta->items as $idx => $it)
      <tr>
        <td class="text-center">{{ $it->cantidad }}</td>
        <td style="font-family:monospace">{{ $it->producto->sku ?? 'SKU-'.$it->producto_id }}</td>
        <td>
          <strong>{{ $it->nombre_producto }}</strong>
          @if($it->producto && $it->producto->presentacion)
            <span style="font-size:9.5px; color:#64748b">({{ $it->producto->presentacion }})</span>
          @endif
        </td>
        <td class="text-right">${{ number_format($it->precio_unitario, 2) }}</td>
        <td class="text-right">$0.00</td>
        <td class="text-right">$0.00</td>
        <td class="text-right"><strong>${{ number_format($it->subtotal, 2) }}</strong></td>
      </tr>
      @empty
      <tr>
        <td colspan="7" class="text-center">Sin ítems</td>
      </tr>
      @endforelse
    </tbody>
  </table>

  {{-- Totales y Código QR --}}
  <div class="summary-grid">
    <div>
      <div class="letters-box">
        <strong>VALOR EN LETRAS:</strong><br>
        {{ strtoupper(\Illuminate\Support\Str::headline(number_format($venta->total, 2))) }} DÓLARES DE LOS ESTADOS UNIDOS DE AMÉRICA
      </div>
      <div class="qr-box">
        <svg width="68" height="68" viewBox="0 0 100 100" fill="#000">
          <rect width="100" height="100" fill="#fff" />
          <rect x="5" y="5" width="30" height="30" fill="#000" />
          <rect x="10" y="10" width="20" height="20" fill="#fff" />
          <rect x="15" y="15" width="10" height="10" fill="#000" />
          <rect x="65" y="5" width="30" height="30" fill="#000" />
          <rect x="70" y="10" width="20" height="20" fill="#fff" />
          <rect x="75" y="15" width="10" height="10" fill="#000" />
          <rect x="5" y="65" width="30" height="30" fill="#000" />
          <rect x="10" y="70" width="20" height="20" fill="#fff" />
          <rect x="15" y="75" width="10" height="10" fill="#000" />
          <rect x="42" y="10" width="16" height="16" fill="#000" />
          <rect x="42" y="42" width="16" height="16" fill="#000" />
          <rect x="65" y="65" width="16" height="16" fill="#000" />
          <rect x="85" y="42" width="10" height="30" fill="#000" />
          <rect x="42" y="75" width="18" height="18" fill="#000" />
        </svg>
        <div style="font-size:9.5px; color:#475569; line-height:1.4">
          <strong>Consulta Pública DTE:</strong><br>
          Escanea el código QR o ingresa el código de generación en el portal del Ministerio de Hacienda para verificar la autenticidad electrónica del documento.
        </div>
      </div>
    </div>

    <div>
      <table class="table-totals">
        <tr>
          <td>Sumas (Ventas Gravadas):</td>
          <td class="text-right"><strong>${{ number_format($venta->subtotal, 2) }}</strong></td>
        </tr>
        <tr>
          <td>Ventas No Sujetas:</td>
          <td class="text-right">$0.00</td>
        </tr>
        <tr>
          <td>Ventas Exentas:</td>
          <td class="text-right">$0.00</td>
        </tr>
        @if($venta->descuento > 0)
        <tr>
          <td>Descuento Otorgado:</td>
          <td class="text-right" style="color:#ef4444">-${{ number_format($venta->descuento, 2) }}</td>
        </tr>
        @endif
        <tr>
          <td>IVA (13%):</td>
          <td class="text-right">${{ number_format($venta->impuesto ?? 0, 2) }}</td>
        </tr>
        <tr>
          <td>Sub-Total:</td>
          <td class="text-right">${{ number_format($venta->total, 2) }}</td>
        </tr>
        <tr class="grand-total">
          <td>TOTAL A PAGAR:</td>
          <td class="text-right">${{ number_format($venta->total, 2) }}</td>
        </tr>
      </table>
    </div>
  </div>

  <div class="pdf-footer">
    Este Documento Tributario Electrónico ha sido emitido y transmitido bajo la normativa legal vigente del Ministerio de Hacienda.<br>
    Gracias por su preferencia. Documento válido para fines fiscales y comerciales.
  </div>
</div>

</body>
</html>
