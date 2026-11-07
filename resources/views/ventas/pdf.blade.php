<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>DTE Factura {{ $venta->codigo }}</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; font-family:Arial, Helvetica, sans-serif; }
  body { background:#f1f5f9; padding:20px; color:#000; font-size:9.5px; }
  .dte-sheet { max-width:850px; margin:0 auto; background:#fff; padding:18px 24px; border:1px solid #94a3b8; box-shadow:0 4px 15px rgba(0,0,0,0.1); }
  
  .page-top-num { text-align:right; font-size:9px; color:#475569; margin-bottom:4px; }
  
  .header-grid { display:grid; grid-template-columns:1.1fr 1fr; gap:14px; margin-bottom:10px; align-items:start; }
  .emisor-info { font-size:9.5px; line-height:1.35; }
  .emisor-title { font-size:12px; font-weight:bold; color:#000; margin-bottom:3px; text-transform:uppercase; }
  
  .dte-official-box { border:1.5px solid #000; border-radius:3px; overflow:hidden; font-size:8.5px; }
  .dte-box-header { background:#fff; border-bottom:1px solid #000; text-align:center; padding:4px; }
  .dte-box-header h2 { font-size:11px; font-weight:bold; margin:0; }
  .dte-box-header h3 { font-size:11px; font-weight:bold; margin:0; text-transform:uppercase; }
  .dte-box-body { padding:4px 8px; display:grid; grid-template-columns:1fr auto; gap:6px; line-height:1.3; }
  .dte-qrs { display:flex; gap:6px; align-items:center; }
  .dte-qr-item { text-align:center; font-size:7px; font-weight:bold; }
  .dte-qr-item img { width:58px; height:58px; display:block; margin:0 auto 2px; }
  
  .dte-table { width:100%; border-collapse:collapse; margin-bottom:6px; font-size:8.5px; }
  .dte-table th, .dte-table td { border:1px solid #000; padding:2px 4px; vertical-align:middle; }
  .dte-table th { background:#fff; font-weight:bold; text-align:center; }
  .section-title { font-weight:bold; text-align:center; font-size:8.5px; letter-spacing:0.5px; background:#fff; }
  
  .items-table { width:100%; border-collapse:collapse; margin-bottom:6px; font-size:8.5px; }
  .items-table th { border:1px solid #000; padding:3px 2px; text-align:center; font-size:8px; font-weight:bold; }
  .items-table td { border:1px solid #000; padding:3px 4px; font-size:8.5px; }
  .text-right { text-align:right; }
  .text-center { text-align:center; }
  
  .bottom-grid { display:grid; grid-template-columns:1.2fr 1fr; border:1px solid #000; font-size:8.5px; }
  .bottom-left { border-right:1px solid #000; padding:6px; display:flex; flex-direction:column; justify-content:space-between; }
  .monto-letras { font-weight:bold; text-transform:uppercase; margin-bottom:8px; font-size:9px; }
  .extension-table { width:100%; border-collapse:collapse; margin-bottom:10px; font-size:8px; }
  .extension-table td { border:1px solid #000; padding:2px 4px; }
  .condicion-op { font-size:14px; font-weight:900; text-align:center; margin-top:8px; }
  .condicion-sub { text-align:center; font-size:8px; color:#475569; }
  
  .bottom-right-table { width:100%; border-collapse:collapse; font-size:8.5px; }
  .bottom-right-table td { border-bottom:1px solid #000; border-left:1px solid #000; padding:2px 4px; }
  .bottom-right-table tr:last-child td { border-bottom:none; font-weight:bold; }
  
  .obs-box { border:1px solid #000; margin-top:6px; padding:4px 6px; font-size:8px; }
  
  .no-print-bar { max-width:850px; margin:0 auto 12px; display:flex; justify-content:space-between; align-items:center; }
  .btn-print { background:#2563eb; color:#fff; border:none; padding:8px 18px; border-radius:6px; font-weight:800; cursor:pointer; font-size:12px; }
  .btn-back { background:#64748b; color:#fff; text-decoration:none; padding:8px 16px; border-radius:6px; font-weight:700; font-size:12px; }

  @media print {
    body { background:#fff; padding:0; }
    .dte-sheet { border:none; box-shadow:none; padding:0; max-width:100%; }
    .no-print-bar { display:none !important; }
    @page { margin: 8mm 6mm; size: A4 portrait; }
  }
</style>
</head>
<body>

<div class="no-print-bar">
  <a href="{{ url()->previous() ?: route('ventas.index') }}" class="btn-back">← Volver</a>
  <button onclick="window.print()" class="btn-print"><i class="fa-solid fa-print"></i> Imprimir / Guardar DTE Factura</button>
</div>

<div class="dte-sheet">
  <div class="page-top-num">Página 1 de 1</div>

  {{-- 1. Encabezado Emisor + Caja DTE --}}
  <div class="header-grid">
    <div class="emisor-info">
      <div class="emisor-title">{{ $config['nombre_negocio'] ?? 'DIGITALSPOS ENTERPRISE S.A. DE C.V.' }}</div>
      <div><strong>Actividad Económica:</strong> {{ $config['giro'] ?? '85301 - Enseñanza superior y servicios informáticos' }}</div>
      <div><strong>Dirección:</strong> {{ $config['direccion'] ?? 'KM. 113 Carretera El Litoral, Usulután, El Salvador, C.A.' }}</div>
      <div><strong>NIT:</strong> {{ $config['nit'] ?? '0614-110682-101-6' }} &nbsp;|&nbsp; <strong>NRC:</strong> {{ $config['nrc'] ?? '757470' }}</div>
      <div><strong>Teléfono:</strong> {{ $config['telefono'] ?? '2645-6500' }} &nbsp;|&nbsp; <strong>Correo:</strong> {{ $config['email'] ?? 'consultas@digitalspos.com' }}</div>
      <div><strong>Tipo Establecimiento:</strong> Sucursal / Agencia (Central)</div>
    </div>

    <div class="dte-official-box">
      <div class="dte-box-header">
        <h2>DOCUMENTO TRIBUTARIO ELECTRÓNICO</h2>
        <h3>Factura</h3>
      </div>
      <div class="dte-box-body">
        <div>
          <div><strong>Código generación:</strong> <span style="font-family:monospace">{{ strtoupper($venta->codigo_generacion ?? md5($venta->codigo)) }}</span></div>
          <div><strong>Sello de recepción:</strong> <span style="font-family:monospace">{{ strtoupper(substr(hash('sha256', $venta->codigo), 0, 40)) }}</span></div>
          <div><strong>Número de control:</strong> DTE-01-S002P001-{{ str_pad($venta->id, 15, '0', STR_PAD_LEFT) }}</div>
          <div><strong>Modelo de Facturación:</strong> Previo &nbsp;|&nbsp; <strong>Versión:</strong> 1</div>
          <div><strong>Fecha y Hora Emisión:</strong> {{ $venta->created_at->format('Y-m-d, H:i:s') }}</div>
          <div><strong>Tipo Transmisión:</strong> Transmisión normal</div>
        </div>
        <div class="dte-qrs">
          <div class="dte-qr-item">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode('https://admin.factura.gob.sv/consultaPublica?dte=' . $venta->codigo) }}" alt="QR Publica">
            Consulta Pública
          </div>
          <div class="dte-qr-item">
            <img src="https://api.qrserver.com/v1/create-qr-code/?size=90x90&data={{ urlencode('https://digitalspos.com/portal/dte/' . $venta->id) }}" alt="QR Portal">
            Portal Cliente
          </div>
        </div>
      </div>
    </div>
  </div>

  {{-- 2. Receptor --}}
  <table class="dte-table">
    <thead>
      <tr>
        <th colspan="3" class="section-title">RECEPTOR</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td style="width:60%"><strong>Nombre:</strong> {{ $venta->cliente->nombre ?? 'FARID ALBERTO FUENTES APARICIO' }}</td>
        <td style="width:20%"><strong>DUI:</strong> {{ $venta->cliente->dui ?? '06864884-6' }}</td>
        <td style="width:20%"><strong>NRC:</strong> {{ $venta->cliente->nrc ?? '—' }}</td>
      </tr>
      <tr>
        <td colspan="2"><strong>Actividad:</strong> {{ $venta->cliente->giro ?? 'Particular / Consumidor Final' }}</td>
        <td><strong>Teléfono:</strong> {{ $venta->cliente->telefono ?? '2662-4765' }}</td>
      </tr>
      <tr>
        <td colspan="2"><strong>Dirección:</strong> {{ $venta->cliente->direccion ?? 'COLONIA PUERTA DEL SOL, FINAL DEL SEGUNDO PASAJE CASA #87' }}</td>
        <td><strong>Correo:</strong> {{ $venta->cliente->email ?? 'cliente@gmail.com' }}</td>
      </tr>
    </tbody>
  </table>

  {{-- 3. Venta a Cuenta de Terceros --}}
  <table class="dte-table">
    <thead>
      <tr><th colspan="2" class="section-title">VENTA A CUENTA DE TERCEROS</th></tr>
      <tr>
        <th style="width:30%">NIT</th>
        <th>Nombre, denominación o razón social:</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </tbody>
  </table>

  {{-- 4. Documentos Relacionados --}}
  <table class="dte-table">
    <thead>
      <tr><th colspan="3" class="section-title">DOCUMENTOS RELACIONADOS</th></tr>
      <tr>
        <th>Tipo de Documento</th>
        <th>No. de Documento</th>
        <th>Fecha de Documento</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </tbody>
  </table>

  {{-- 5. Otros Documentos Asociados --}}
  <table class="dte-table">
    <thead>
      <tr><th colspan="3" class="section-title">OTROS DOCUMENTOS ASOCIADOS</th></tr>
      <tr>
        <th>Documento asociado</th>
        <th>Identificación</th>
        <th>Descripción</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
        <td>&nbsp;</td>
      </tr>
    </tbody>
  </table>

  {{-- 6. Tabla de Items / Productos (Exacto a la Imagen 2) --}}
  <table class="items-table">
    <thead>
      <tr>
        <th style="width:24px">No.</th>
        <th style="width:40px">Cant.</th>
        <th style="width:45px">Unidad</th>
        <th>Descripción</th>
        <th style="width:65px">Precio Unitario</th>
        <th style="width:55px">Descuento por item</th>
        <th style="width:65px">Otros montos no afectos</th>
        <th style="width:55px">Ventas no Sujetas</th>
        <th style="width:55px">Ventas Exentas</th>
        <th style="width:65px">Ventas Gravadas</th>
      </tr>
    </thead>
    <tbody>
      @php
        $num = 1;
        $totalGravado = 0;
        $totalExento = 0;
      @endphp
      @foreach($venta->items as $item)
        @php
          $subItem = (float)$item->subtotal;
          $descItem = (float)($item->descuento ?? 0);
          $esExento = !empty($item->exento);
          if ($esExento) {
            $totalExento += $subItem;
          } else {
            $totalGravado += $subItem;
          }
        @endphp
        <tr>
          <td class="text-center">{{ $num++ }}</td>
          <td class="text-center">{{ number_format($item->cantidad, 2) }}</td>
          <td class="text-center">Unidad</td>
          <td>{{ $item->nombre_producto }}</td>
          <td class="text-right">${{ number_format($item->precio_unitario, 2) }}</td>
          <td class="text-right">${{ number_format($descItem, 2) }}</td>
          <td class="text-right">$0.00</td>
          <td class="text-right">$0.00</td>
          <td class="text-right">{{ $esExento ? '$'.number_format($subItem, 2) : '$0.00' }}</td>
          <td class="text-right">{{ !$esExento ? '$'.number_format($subItem, 2) : '$0.00' }}</td>
        </tr>
      @endforeach

      {{-- Filas vacías para completar la estructura estética de la página como en la imagen real --}}
      @for($f = count($venta->items); $f < 8; $f++)
        <tr>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
          <td>&nbsp;</td>
        </tr>
      @endfor
    </tbody>
  </table>

  {{-- 7. Bloque de Totales y Resumen Inferior (Exacto a la Imagen 2) --}}
  <div class="bottom-grid">
    <div class="bottom-left">
      <div>
        <div class="monto-letras">
          @php
            $entero = floor($venta->total);
            $centavos = round(($venta->total - $entero) * 100);
            if (class_exists('\NumberFormatter')) {
              $fmt = new \NumberFormatter("es", \NumberFormatter::SPELLOUT);
              $letras = strtoupper($fmt->format($entero)) . ' ' . str_pad($centavos, 2, '0', STR_PAD_LEFT) . '/100 DÓLARES';
            } else {
              $unidades = ['', 'UN', 'DOS', 'TRES', 'CUATRO', 'CINCO', 'SEIS', 'SIETE', 'OCHO', 'NUEVE'];
              $decenas = ['', 'DIEZ', 'VEINTE', 'TREINTA', 'CUARENTA', 'CINCUENTA', 'SESENTA', 'SETENTA', 'OCHENTA', 'NOVENTA'];
              $especiales = [11=>'ONCE', 12=>'DOCE', 13=>'TRECE', 14=>'QUINCE', 16=>'DIECISÉIS', 17=>'DIECISIETE', 18=>'DIECIOCHO', 19=>'DIECINUEVE', 20=>'VEINTE', 21=>'VEINTIUNO', 22=>'VEINTIDÓS', 23=>'VEINTITRÉS', 24=>'VEINTICUATRO', 25=>'VEINTICINCO'];
              $numStr = '';
              if ($entero == 0) $numStr = 'CERO';
              elseif (isset($especiales[$entero])) $numStr = $especiales[$entero];
              elseif ($entero < 10) $numStr = $unidades[$entero];
              elseif ($entero < 100) {
                $d = floor($entero / 10);
                $u = $entero % 10;
                $numStr = $decenas[$d] . ($u > 0 ? ' Y ' . $unidades[$u] : '');
              } else {
                $numStr = (string)$entero;
              }
              $letras = $numStr . ' ' . str_pad($centavos, 2, '0', STR_PAD_LEFT) . '/100 DÓLARES';
            }
          @endphp
          {{ $letras }}
        </div>

        <table class="extension-table">
          <thead>
            <tr><th colspan="2" style="text-align:center; font-weight:bold">EXTENSIÓN</th></tr>
          </thead>
          <tbody>
            <tr>
              <td style="width:30%"><strong>Nombre Entrega:</strong></td>
              <td>{{ $venta->usuario->nombre ?? 'Administrador' }}</td>
            </tr>
            <tr>
              <td><strong>No. Documento:</strong></td>
              <td>0614-110682-101-6</td>
            </tr>
            <tr>
              <td><strong>Nombre Recibe:</strong></td>
              <td>{{ $venta->cliente->nombre ?? 'Clientes Varios' }}</td>
            </tr>
            <tr>
              <td><strong>No. Documento:</strong></td>
              <td>{{ $venta->cliente->dui ?? '00000000-0' }}</td>
            </tr>
          </tbody>
        </table>
      </div>

      <div>
        <div class="condicion-op">CONTADO</div>
        <div class="condicion-sub">Condición de Operación: Efectivo</div>
      </div>
    </div>

    <div class="bottom-right">
      <table class="bottom-right-table">
        <tbody>
          <tr>
            <td style="font-weight:bold">SUMAS</td>
            <td class="text-right" style="width:65px">$0.00</td>
            <td class="text-right" style="width:65px">${{ number_format($totalExento, 2) }}</td>
            <td class="text-right" style="width:65px">${{ number_format($totalGravado, 2) }}</td>
          </tr>
          <tr>
            <td colspan="3">Suma Total de Ventas:</td>
            <td class="text-right"><strong>${{ number_format($venta->total, 2) }}</strong></td>
          </tr>
          <tr>
            <td colspan="3">Monto global Desc., Rebajas y otros a ventas no sujetas:</td>
            <td class="text-right">$0.00</td>
          </tr>
          <tr>
            <td colspan="3">Monto global Desc., Rebajas y otros a ventas exentas:</td>
            <td class="text-right">$0.00</td>
          </tr>
          <tr>
            <td colspan="3">Monto global Desc., Rebajas y otros a ventas gravadas:</td>
            <td class="text-right">${{ number_format($venta->descuento, 2) }}</td>
          </tr>
          <tr>
            <td colspan="3">Sub-Total:</td>
            <td class="text-right"><strong>${{ number_format($venta->subtotal, 2) }}</strong></td>
          </tr>
          <tr>
            <td colspan="3">IVA (13% integrado en precio):</td>
            <td class="text-right">${{ number_format($venta->impuesto, 2) }}</td>
          </tr>
          <tr>
            <td colspan="3">IVA Retenido (1%):</td>
            <td class="text-right">${{ number_format($venta->retencion, 2) }}</td>
          </tr>
          <tr>
            <td colspan="3">Retención Renta:</td>
            <td class="text-right">$0.00</td>
          </tr>
          <tr>
            <td colspan="3">Monto Total de la Operación:</td>
            <td class="text-right"><strong>${{ number_format($venta->total, 2) }}</strong></td>
          </tr>
          <tr>
            <td colspan="3">Total Otros Montos No Afectos:</td>
            <td class="text-right">$0.00</td>
          </tr>
          <tr style="background:#f8fafc">
            <td colspan="3" style="font-size:10px; font-weight:900">Total a Pagar:</td>
            <td class="text-right" style="font-size:11px; font-weight:900; color:#000">${{ number_format($venta->total, 2) }}</td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>

  {{-- 8. Observaciones y Pie de Firma --}}
  <div class="obs-box">
    <strong>- OBSERVACIONES -</strong><br>
    Cajero: <strong>{{ $venta->usuario->nombre ?? 'gamador' }}</strong> &nbsp;|&nbsp;
    Sucursal: <strong>{{ $venta->caja->sucursal ?? 'Sucursal Central' }}</strong> &nbsp;|&nbsp;
    Turno: <strong>{{ $venta->caja->turno ?? 'Turno 1' }}</strong> &nbsp;|&nbsp;
    Ciclo: Ciclo I - 2026 &nbsp;|&nbsp;
    Comprobante emitido según disposiciones del Ministerio de Hacienda de El Salvador.
  </div>
</div>

</body>
</html>