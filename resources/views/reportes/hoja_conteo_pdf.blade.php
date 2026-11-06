<!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<title>Hoja de Conteo de Inventario - {{ date('d/m/Y') }}</title>
<style>
  * { margin:0; padding:0; box-sizing:border-box; font-family:'Helvetica Neue',Helvetica,Arial,sans-serif; }
  body { background:#f1f5f9; padding:24px; color:#1e293b; font-size:10px; }
  .pdf-container { max-width:920px; margin:0 auto; background:#fff; padding:24px; border-radius:6px; box-shadow:0 4px 15px rgba(0,0,0,0.08); }
  .pdf-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; border-bottom:2px solid #0f172a; padding-bottom:10px; }
  .brand-title { font-size:18px; font-weight:900; color:#0f172a; text-transform:uppercase; letter-spacing:0.5px; }
  .doc-title { font-size:15px; font-weight:900; color:#0f172a; text-transform:uppercase; text-align:right; }
  
  .meta-bar { display:flex; justify-content:space-between; margin-bottom:14px; font-size:10px; color:#475569; background:#f8fafc; padding:8px 12px; border:1px solid #cbd5e1; border-radius:4px; }
  
  .table-pdf { width:100%; border-collapse:collapse; margin-bottom:18px; font-size:9.5px; }
  .table-pdf th { background:#f1f5f9; color:#0f172a; padding:6px 6px; font-weight:800; text-transform:uppercase; font-size:9px; border:1px solid #cbd5e1; }
  .table-pdf td { padding:5px 6px; border:1px solid #cbd5e1; vertical-align:middle; }
  .text-right { text-align:right; }
  .text-center { text-align:center; }
  .blank-box { background:#fff; border:1px dashed #94a3b8; height:20px; border-radius:2px; }
  
  .no-print-bar { max-width:920px; margin:0 auto 14px; display:flex; justify-content:space-between; align-items:center; }
  .btn-print { background:#2563eb; color:#fff; border:none; padding:8px 18px; border-radius:6px; font-weight:800; cursor:pointer; font-size:12px; }
  .btn-back { background:#64748b; color:#fff; text-decoration:none; padding:8px 16px; border-radius:6px; font-weight:700; font-size:12px; }

  @media print {
    body { background:#fff; padding:0; }
    .pdf-container { box-shadow:none; padding:0; border-radius:0; max-width:100%; }
    .no-print-bar { display:none !important; }
    @page { margin: 12mm 10mm; size: portrait; }
  }
</style>
</head>
<body>

<div class="no-print-bar">
  <a href="{{ url()->previous() ?: route('reportes.hoja_conteo') }}" class="btn-back">← Volver</a>
  <button onclick="window.print()" class="btn-print"><i class="fa-solid fa-print"></i> Imprimir / Guardar como PDF</button>
</div>

<div class="pdf-container">
  <div class="pdf-header">
    <div>
      <div class="brand-title">{{ $config['nombre_negocio'] ?? 'DIGITALSPOS' }}</div>
      <div style="font-size:9.5px; color:#64748b">{{ $config['eslogan'] ?? 'Control y Auditoría de Stock' }}</div>
    </div>
    <div class="doc-title">
      <div>REPORTE DE INVENTARIO</div>
      <div style="font-size:11px; font-weight:700; color:#2563eb">HOJA DE CONTEO FÍSICO</div>
    </div>
  </div>

  <div class="meta-bar">
    <div><strong>Sucursal:</strong> Sucursal Central</div>
    <div><strong>Fecha de Emisión:</strong> {{ now()->format('d/m/Y H:i:s') }}</div>
    <div><strong>Total Productos Listados:</strong> {{ $productos->count() }} ítems</div>
    <div><strong>Auditor / Responsable:</strong> {{ session('user.nombre', 'Administrador') }}</div>
  </div>

  <table class="table-pdf">
    <thead>
      <tr>
        <th class="text-center" style="width:30px">Nº</th>
        <th style="width:90px">Código / SKU</th>
        <th>Descripción del Producto</th>
        <th style="width:110px">Categoría</th>
        <th class="text-center" style="width:75px">Presentación</th>
        <th class="text-center" style="width:65px">Stock Sist.</th>
        <th class="text-center" style="width:75px">Conteo Físico</th>
        <th class="text-center" style="width:65px">Diferencia</th>
        <th style="width:120px">Observaciones</th>
      </tr>
    </thead>
    <tbody>
      @forelse($productos as $idx => $p)
      <tr>
        <td class="text-center">{{ $idx + 1 }}</td>
        <td style="font-family:monospace">{{ $p->sku ?? '—' }}</td>
        <td><strong>{{ $p->nombre }}</strong></td>
        <td>{{ $p->categoria->nombre ?? 'General' }}</td>
        <td class="text-center">{{ $p->presentacion ?? 'UNIDAD' }}</td>
        <td class="text-center"><strong>{{ $p->stock }}</strong></td>
        <td class="text-center"><div class="blank-box"></div></td>
        <td class="text-center"><div class="blank-box"></div></td>
        <td><div class="blank-box"></div></td>
      </tr>
      @empty
      <tr>
        <td colspan="9" class="text-center">Sin productos registrados en el inventario.</td>
      </tr>
      @endforelse
    </tbody>
  </table>

  <div style="margin-top:40px; display:grid; grid-template-columns:1fr 1fr; gap:50px; text-align:center; font-size:10px; color:#475569">
    <div>
      <div style="border-top:1px solid #94a3b8; padding-top:6px; margin:0 30px">
        Firma Responsable de Conteo Físico
      </div>
    </div>
    <div>
      <div style="border-top:1px solid #94a3b8; padding-top:6px; margin:0 30px">
        Firma Auditor / Supervisor de Almacén
      </div>
    </div>
  </div>
</div>

</body>
</html>
