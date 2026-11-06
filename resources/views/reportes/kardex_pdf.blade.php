<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Reporte de Kardex - {{ $producto->nombre }}</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; font-family:'Helvetica Neue',Helvetica,Arial,sans-serif; }
  body { background:#f1f5f9; padding:24px; color:#1e293b; font-size:10px; }
  .pdf-container { max-width:960px; margin:0 auto; background:#fff; padding:24px; border-radius:6px; box-shadow:0 4px 15px rgba(0,0,0,0.08); }
  .pdf-header { text-align:center; margin-bottom:14px; border-bottom:2px solid #0f172a; padding-bottom:10px; }
  .brand-title { font-size:18px; font-weight:900; color:#0f172a; text-transform:uppercase; letter-spacing:0.5px; }
  .doc-title { font-size:14px; font-weight:900; color:#2563eb; text-transform:uppercase; margin:3px 0; }
  .doc-period { font-size:10px; color:#475569; font-weight:700; }
  
  .meta-bar { display:flex; justify-content:space-between; margin-bottom:14px; font-size:10px; color:#1e293b; background:#f8fafc; padding:8px 12px; border:1px solid #cbd5e1; border-radius:4px; }
  
  .table-pdf { width:100%; border-collapse:collapse; margin-bottom:18px; font-size:9px; }
  .table-pdf th { background:#f1f5f9; color:#0f172a; padding:5px 4px; font-weight:800; text-transform:uppercase; font-size:8.5px; border:1px solid #cbd5e1; }
  .table-pdf td { padding:5px 4px; border:1px solid #cbd5e1; vertical-align:middle; }
  .text-right { text-align:right; }
  .text-center { text-align:center; }
  
  .no-print-bar { max-width:960px; margin:0 auto 14px; display:flex; justify-content:space-between; align-items:center; }
  .btn-print { background:#2563eb; color:#fff; border:none; padding:8px 18px; border-radius:6px; font-weight:800; cursor:pointer; font-size:12px; }
  .btn-back { background:#64748b; color:#fff; text-decoration:none; padding:8px 16px; border-radius:6px; font-weight:700; font-size:12px; }

  @media print {
    body { background:#fff; padding:0; }
    .pdf-container { box-shadow:none; padding:0; border-radius:0; max-width:100%; }
    .no-print-bar { display:none !important; }
    @page { margin: 10mm; size: landscape; }
  }
</style>
</head>
<body>

<div class="no-print-bar">
  <a href="{{ url()->previous() ?: route('reportes.kardex', $producto->id) }}" class="btn-back">← Volver</a>
  <button onclick="window.print()" class="btn-print">🖨️ Imprimir / Guardar como PDF</button>
</div>

<div class="pdf-container">
  <div class="pdf-header">
    <div class="brand-title">{{ $config['nombre_negocio'] ?? 'DIGITALSPOS' }}</div>
    <div class="doc-title">REPORTE DE KARDEX (VALUACIÓN DE INVENTARIO)</div>
    <div class="doc-period">
      PERÍODO: DEL {{ $desde ? \Carbon\Carbon::parse($desde)->format('d/m/Y') : 'INICIO' }} AL {{ $hasta ? \Carbon\Carbon::parse($hasta)->format('d/m/Y') : now()->format('d/m/Y') }}
    </div>
  </div>

  <div class="meta-bar">
    <div><strong>PRODUCTO:</strong> {{ strtoupper($producto->nombre) }}</div>
    <div><strong>CÓDIGO / SKU:</strong> {{ $producto->sku ?? '—' }}</div>
    <div><strong>CATEGORÍA:</strong> {{ $producto->categoria->nombre ?? 'General' }}</div>
    <div><strong>COSTO UNITARIO REF.:</strong> ${{ number_format($producto->precio_compra, 2) }}</div>
    <div><strong>STOCK ACTUAL:</strong> <strong style="color:#2563eb">{{ $producto->stock }}</strong></div>
  </div>

  <table class="table-pdf">
    <thead>
      <tr>
        <th rowspan="2" class="text-center" style="width:75px">FECHA</th>
        <th rowspan="2" class="text-center" style="width:65px">TIPO DOC</th>
        <th rowspan="2" style="width:85px">Nº DOC</th>
        <th colspan="3" class="text-center" style="background:#e0f2fe">ENTRADAS</th>
        <th colspan="3" class="text-center" style="background:#fef3c7">SALIDAS</th>
        <th colspan="3" class="text-center" style="background:#dcfce7">EXISTENCIA / SALDO</th>
        <th rowspan="2">RESPONSABLE / TERCERO</th>
      </tr>
      <tr>
        <th class="text-center" style="width:45px; background:#e0f2fe">CANT.</th>
        <th class="text-right" style="width:55px; background:#e0f2fe">COSTO</th>
        <th class="text-right" style="width:60px; background:#e0f2fe">TOTAL</th>
        <th class="text-center" style="width:45px; background:#fef3c7">CANT.</th>
        <th class="text-right" style="width:55px; background:#fef3c7">COSTO</th>
        <th class="text-right" style="width:60px; background:#fef3c7">TOTAL</th>
        <th class="text-center" style="width:45px; background:#dcfce7">CANT.</th>
        <th class="text-right" style="width:55px; background:#dcfce7">COSTO</th>
        <th class="text-right" style="width:60px; background:#dcfce7">TOTAL</th>
      </tr>
    </thead>
    <tbody>
      {{-- Saldo Inicial --}}
      <tr style="background:#f8fafc; font-weight:700">
        <td class="text-center">{{ $desde ? \Carbon\Carbon::parse($desde)->format('d/m/Y') : '—' }}</td>
        <td class="text-center">INICIAL</td>
        <td>SALDO-INICIAL</td>
        <td colspan="3" class="text-center">—</td>
        <td colspan="3" class="text-center">—</td>
        <td class="text-center">{{ $saldoInicial }}</td>
        <td class="text-right">${{ number_format($producto->precio_compra, 2) }}</td>
        <td class="text-right">${{ number_format($saldoInicial * $producto->precio_compra, 2) }}</td>
        <td>Inventario previo</td>
      </tr>

      @forelse($movimientos as $m)
      @php
        $esEntrada = in_array($m->tipo, ['Compra', 'Traslado entrada', 'Ajuste positivo']);
        $costo     = $m->precio_unitario > 0 ? $m->precio_unitario : $producto->precio_compra;
        $totalMov  = abs($m->cantidad) * $costo;
        $totalSaldo = $m->saldo * $costo;
      @endphp
      <tr>
        <td class="text-center">{{ \Carbon\Carbon::parse($m->fecha)->format('d/m/Y H:i') }}</td>
        <td class="text-center">
          <span style="font-weight:700; color:{{ $esEntrada ? '#16a34a' : '#dc2626' }}">
            {{ strtoupper($m->tipo) }}
          </span>
        </td>
        <td style="font-family:monospace">{{ $m->referencia ?? '—' }}</td>

        {{-- Entradas --}}
        @if($esEntrada)
        <td class="text-center"><strong>{{ abs($m->cantidad) }}</strong></td>
        <td class="text-right">${{ number_format($costo, 2) }}</td>
        <td class="text-right"><strong>${{ number_format($totalMov, 2) }}</strong></td>
        <td colspan="3" class="text-center" style="color:#94a3b8">—</td>
        @else
        {{-- Salidas --}}
        <td colspan="3" class="text-center" style="color:#94a3b8">—</td>
        <td class="text-center"><strong>{{ abs($m->cantidad) }}</strong></td>
        <td class="text-right">${{ number_format($costo, 2) }}</td>
        <td class="text-right"><strong>${{ number_format($totalMov, 2) }}</strong></td>
        @endif

        {{-- Saldo --}}
        <td class="text-center" style="font-weight:800; color:#2563eb">{{ $m->saldo }}</td>
        <td class="text-right">${{ number_format($costo, 2) }}</td>
        <td class="text-right" style="font-weight:700">${{ number_format($totalSaldo, 2) }}</td>
        <td>{{ $m->usuario ?? 'Sistema' }}</td>
      </tr>
      @empty
      <tr>
        <td colspan="13" class="text-center" style="padding:15px">Sin movimientos en el período seleccionado.</td>
      </tr>
      @endforelse
    </tbody>
  </table>

  <div style="margin-top:40px; display:grid; grid-template-columns:1fr 1fr 1fr; gap:30px; text-align:center; font-size:10px; color:#475569">
    <div>
      <div style="border-top:1px solid #94a3b8; padding-top:6px; margin:0 20px">
        Elaborado por: {{ session('user.nombre', 'Administrador') }}
      </div>
    </div>
    <div>
      <div style="border-top:1px solid #94a3b8; padding-top:6px; margin:0 20px">
        Revisado por: Auditor de Inventario
      </div>
    </div>
    <div>
      <div style="border-top:1px solid #94a3b8; padding-top:6px; margin:0 20px">
        Autorizado por: Gerencia General
      </div>
    </div>
  </div>
</div>

</body>
</html>
