@extends('layouts.main')
@section('title','Consultar Stock')
@section('content')
<div class="page-header">
  <div>
    <h1>📊 Consultar Stock</h1>
    <p class="page-sub">Inventario actual de todos los productos</p>
  </div>
  <div style="display:flex;gap:8px">
    <a href="{{ route('compras.crear') }}" class="btn btn-primary">+ Compra</a>
    <a href="{{ route('inventario.traslados') }}" class="btn btn-secondary">Ajuste</a>
  </div>
</div>

<div class="stats-grid" style="grid-template-columns:repeat(4,1fr)">
  <div class="stat-card stat-blue">
    <div class="stat-icon">📦</div>
    <div class="stat-value">{{ $productos->count() }}</div>
    <div class="stat-label">Total Productos</div>
  </div>
  <div class="stat-card stat-green">
    <div class="stat-icon">✅</div>
    <div class="stat-value">{{ $productos->where('stock','>',$productos->pluck('stock_minimo'))->count() }}</div>
    <div class="stat-label">Stock OK</div>
  </div>
  <div class="stat-card stat-red">
    <div class="stat-icon">⚠️</div>
    <div class="stat-value">{{ $productos->filter(fn($p) => $p->stock <= $p->stock_minimo)->count() }}</div>
    <div class="stat-label">Stock Bajo</div>
  </div>
  <div class="stat-card stat-purple">
    <div class="stat-icon">💰</div>
    <div class="stat-value">${{ number_format($productos->sum(fn($p) => $p->stock * $p->precio_compra),2) }}</div>
    <div class="stat-label">Valor Inventario</div>
  </div>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-toolbar" style="display:flex;gap:12px;align-items:center">
      <input type="search" id="searchTable" placeholder="Buscar producto, SKU..." class="form-control" style="max-width:280px">
      <select id="filtroStock" class="form-control" style="max-width:160px">
        <option value="">Todos</option>
        <option value="bajo">Stock Bajo</option>
        <option value="ok">Stock OK</option>
        <option value="cero">Sin Stock</option>
      </select>
    </div>
    <table class="table" id="stockTable">
      <thead>
        <tr><th>Código</th><th>Producto</th><th>Categoría</th><th>Unidad</th><th>Stock</th><th>Mínimo</th><th>P. Compra</th><th>P. Venta</th><th>Margen</th><th>Valor Total</th></tr>
      </thead>
      <tbody>
        @forelse($productos as $p)
        <tr data-stock="{{ $p->stock }}" data-minimo="{{ $p->stock_minimo }}">
          <td><code>{{ $p->sku ?? '—' }}</code></td>
          <td>
            @if($p->imagen)
              <img src="{{ asset('storage/'.$p->imagen) }}" class="thumb" alt="">
            @endif
            {{ $p->nombre }}
          </td>
          <td>{{ $p->categoria->nombre ?? '—' }}</td>
          <td>{{ $p->unidad ?? 'unidad' }}</td>
          <td>
            <span class="badge {{ $p->stock == 0 ? 'badge-danger' : ($p->stock <= $p->stock_minimo ? 'badge-warning' : 'badge-success') }}">
              {{ $p->stock }}
            </span>
          </td>
          <td>{{ $p->stock_minimo }}</td>
          <td>${{ number_format($p->precio_compra,2) }}</td>
          <td>${{ number_format($p->precio_venta,2) }}</td>
          <td>
            <span class="{{ ($p->precio_venta - $p->precio_compra) > 0 ? 'text-success' : 'text-danger' }}">
              ${{ number_format($p->precio_venta - $p->precio_compra, 2) }}
            </span>
          </td>
          <td>${{ number_format($p->stock * $p->precio_compra, 2) }}</td>
        </tr>
        @empty
        <tr><td colspan="10" class="text-center text-muted" style="padding:2rem">Sin productos.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@push('scripts')
<script>
document.getElementById('searchTable').addEventListener('input', function(){
  filtrar();
});
document.getElementById('filtroStock').addEventListener('change', function(){
  filtrar();
});
function filtrar(){
  const q = document.getElementById('searchTable').value.toLowerCase();
  const f = document.getElementById('filtroStock').value;
  document.querySelectorAll('#stockTable tbody tr').forEach(r => {
    const stock   = parseInt(r.dataset.stock);
    const minimo  = parseInt(r.dataset.minimo);
    const textOk  = r.textContent.toLowerCase().includes(q);
    let stockOk   = true;
    if(f === 'bajo')  stockOk = stock <= minimo && stock > 0;
    if(f === 'ok')    stockOk = stock > minimo;
    if(f === 'cero')  stockOk = stock === 0;
    r.style.display = (textOk && stockOk) ? '' : 'none';
  });
}
</script>
@endpush
@endsection
