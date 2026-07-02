@extends('layouts.main')
@section('title','Reporte Kardex')
@section('content')
<div class="page-header">
  <div>
    <h1>📊 Reporte Kardex</h1>
    <p class="page-sub">Movimientos de inventario: <strong>{{ $producto->nombre }}</strong></p>
  </div>
  <div>
    <button onclick="window.print()" class="btn btn-secondary">🖨️ Imprimir</button>
    <a href="{{ route('reportes.index') }}" class="btn btn-secondary">← Reportes</a>
  </div>
</div>

<div class="card" style="max-width:600px;margin-bottom:20px">
  <div class="card-body">
    <div class="info-grid">
      <div class="info-item"><span>Código</span><strong>{{ $producto->sku ?? '—' }}</strong></div>
      <div class="info-item"><span>Categoría</span><strong>{{ $producto->categoria->nombre ?? '—' }}</strong></div>
      <div class="info-item"><span>P. Compra</span><strong>${{ number_format($producto->precio_compra,2) }}</strong></div>
      <div class="info-item"><span>P. Venta</span><strong>${{ number_format($producto->precio_venta,2) }}</strong></div>
      <div class="info-item"><span>Stock Actual</span>
        <span class="badge badge-{{ $producto->stock<=$producto->stock_minimo?'danger':'success' }}">{{ $producto->stock }}</span>
      </div>
      <div class="info-item"><span>Stock Mínimo</span><strong>{{ $producto->stock_minimo }}</strong></div>
    </div>
  </div>
</div>

<div class="card">
  <div class="card-header"><h3>Movimientos de Inventario</h3></div>
  <div class="card-body p-0">
    <table class="table">
      <thead>
        <tr><th>Fecha</th><th>Tipo</th><th>Referencia</th><th>Cantidad</th><th>Precio Unit.</th><th>Usuario</th></tr>
      </thead>
      <tbody>
        @forelse($movimientos as $m)
        @php
          $esEntrada = str_contains(strtolower($m->tipo),'compra') || str_contains(strtolower($m->tipo),'entrada');
          $esSalida  = str_contains(strtolower($m->tipo),'venta')  || str_contains(strtolower($m->tipo),'salida');
        @endphp
        <tr>
          <td>{{ \Carbon\Carbon::parse($m->fecha)->format('d/m/Y H:i') }}</td>
          <td>
            <span class="badge badge-{{ $esEntrada?'success':($esSalida?'danger':'warning') }}">
              {{ $m->tipo }}
            </span>
          </td>
          <td><code>{{ $m->referencia ?? '—' }}</code></td>
          <td>
            <span class="{{ $esEntrada?'text-success':($esSalida?'text-danger':'text-warning') }}">
              {{ $esEntrada?'+':($esSalida?'-':'±') }}{{ abs($m->cantidad) }}
            </span>
          </td>
          <td>${{ number_format($m->precio_unitario,2) }}</td>
          <td>{{ $m->usuario }}</td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted" style="padding:2rem">Sin movimientos registrados.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
