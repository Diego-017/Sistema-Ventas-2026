@extends('layouts.main')
@section('title','Reporte Kardex')
@section('content')
<div class="page-header">
  <div>
    <h1>📊 Reporte Kardex</h1>
    <p class="page-sub">Movimientos de inventario: <strong>{{ $producto->nombre }}</strong></p>
  </div>
  <div>
    <a href="{{ route('reportes.kardex.excel',['id'=>$producto->id,'desde'=>$desde,'hasta'=>$hasta]) }}" class="btn btn-success">📊 Generar Reporte Excel</a>
    <button onclick="window.print()" class="btn btn-secondary">🖨️ Imprimir</button>
    <a href="{{ route('reportes.kardex.buscar') }}" class="btn btn-secondary">← Buscar otro producto</a>
  </div>
</div>

<div class="card" style="max-width:500px;margin-bottom:20px">
  <div class="card-body">
    <form method="GET" class="form-row" style="align-items:flex-end">
      <div class="form-group">
        <label>Desde</label>
        <input type="date" name="desde" value="{{ $desde }}" class="form-control">
      </div>
      <div class="form-group">
        <label>Hasta</label>
        <input type="date" name="hasta" value="{{ $hasta }}" class="form-control">
      </div>
      <div class="form-group" style="flex:0">
        <button type="submit" class="btn btn-primary">Filtrar</button>
      </div>
    </form>
    @if($desde)<p class="help-text">Saldo inicial al {{ \Carbon\Carbon::parse($desde)->format('d/m/Y') }}: <strong>{{ $saldoInicial }}</strong> unidades.</p>@endif
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
          $esCompra  = str_contains(strtolower($m->tipo),'compra');
          $esVenta   = str_contains(strtolower($m->tipo),'venta');
          $esEntrada = $m->delta >= 0;
        @endphp
        <tr>
          <td>{{ \Carbon\Carbon::parse($m->fecha)->format('d/m/Y H:i') }}</td>
          <td>
            <span class="badge badge-{{ $esCompra?'success':($esVenta?'info':($esEntrada?'success':'danger')) }}">
              {{ $m->tipo }}
            </span>
          </td>
          <td><code>{{ $m->referencia ?? '—' }}</code></td>
          <td>
            <span class="{{ $esEntrada?'text-success':'text-danger' }}">
              {{ $esEntrada?'+':'-' }}{{ abs($m->cantidad) }}
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
