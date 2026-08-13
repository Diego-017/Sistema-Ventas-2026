@extends('layouts.main')
@section('title','Reporte Kardex')
@section('content')

<div class="page-header">
  <div>
    <h1>📊 Reporte de Kardex</h1>
    <p class="page-sub">Selecciona un producto y un rango de fechas para ver sus movimientos de inventario</p>
  </div>
  <a href="{{ route('reportes.index') }}" class="btn btn-secondary">← Reportes</a>
</div>

<div class="card" style="max-width:760px">
  <div class="card-body">
    <form method="GET" action="{{ route('reportes.kardex.buscar') }}">
      <div class="form-row">
        <div class="form-group">
          <label>Fecha de Inicio</label>
          <input type="date" name="desde" value="{{ $desde }}" class="form-control">
        </div>
        <div class="form-group">
          <label>Fecha Final</label>
          <input type="date" name="hasta" value="{{ $hasta }}" class="form-control">
        </div>
      </div>
      <div class="form-group">
        <label>Buscar Producto <span class="req">*</span></label>
        <input type="text" name="q" value="{{ request('q') }}" class="form-control" placeholder="Nombre o código del producto..." autofocus>
      </div>
      <button type="submit" class="btn btn-primary">🔍 Buscar Producto</button>
    </form>
  </div>
</div>

@if(request()->filled('q'))
<div class="card" style="max-width:760px;margin-top:16px">
  <div class="card-header"><h3>Resultados</h3></div>
  <div class="card-body p-0">
    @if($productos->isEmpty())
      <div class="empty-state">No se encontraron productos que coincidan con "{{ request('q') }}".</div>
    @else
    <table class="table">
      <thead><tr><th>Producto</th><th>SKU</th><th>Stock</th><th></th></tr></thead>
      <tbody>
        @foreach($productos as $p)
        <tr>
          <td>{{ $p->nombre }}</td>
          <td><code>{{ $p->sku ?? '—' }}</code></td>
          <td>{{ $p->stock }}</td>
          <td style="white-space:nowrap">
            <a href="{{ route('reportes.kardex',['id'=>$p->id,'desde'=>$desde,'hasta'=>$hasta]) }}" class="btn btn-sm btn-primary">📄 Generar Reporte</a>
            <a href="{{ route('reportes.kardex.excel',['id'=>$p->id,'desde'=>$desde,'hasta'=>$hasta]) }}" class="btn btn-sm btn-success">📊 Excel</a>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
    @endif
  </div>
</div>
@endif
@endsection
