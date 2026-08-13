@extends('layouts.main')
@section('title','Hoja de Conteo')
@section('content')
<div class="page-header">
  <div>
    <h1>📝 Hoja de Conteo de Inventario</h1>
    <p class="page-sub">{{ now()->format('d/m/Y H:i') }} — Generada por {{ session('user.nombre') }}</p>
  </div>
  <button onclick="window.print()" class="btn btn-secondary">🖨️ Imprimir</button>
</div>

<div class="card">
  <div class="card-body p-0">
    <table class="table" id="conteoTable">
      <thead>
        <tr>
          <th>#</th>
          <th>Código</th>
          <th>Producto</th>
          <th>Categoría</th>
          <th>Subcategoría</th>
          <th>Unidad</th>
          <th>Stock Sistema</th>
          <th style="background:#fef3c7;min-width:100px">Conteo Real</th>
          <th style="background:#fef3c7;min-width:80px">Diferencia</th>
          <th>Observaciones</th>
        </tr>
      </thead>
      <tbody>
        @forelse($productos as $i => $p)
        <tr>
          <td class="text-muted">{{ $i+1 }}</td>
          <td><code>{{ $p->sku ?? '—' }}</code></td>
          <td><strong>{{ $p->nombre }}</strong></td>
          <td>{{ $p->categoria->nombre ?? '—' }}</td>
          <td>{{ $p->subcategoria->nombre ?? '—' }}</td>
          <td>{{ $p->unidad ?? 'unidad' }}</td>
          <td><span class="badge badge-{{ $p->stock <= $p->stock_minimo ? 'danger':'success' }}">{{ $p->stock }}</span></td>
          <td style="background:#fffbeb"><input type="number" class="form-control form-control-sm conteo-real" data-sistema="{{ $p->stock }}" placeholder="0" style="min-width:80px"></td>
          <td style="background:#fffbeb"><span class="diferencia text-muted">—</span></td>
          <td><input type="text" class="form-control form-control-sm" placeholder="Observaciones..."></td>
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
// Calcular diferencia en tiempo real
document.querySelectorAll('.conteo-real').forEach(input => {
  input.addEventListener('input', function(){
    const sistema = parseInt(this.dataset.sistema)||0;
    const real    = parseInt(this.value)||0;
    const diff    = real - sistema;
    const span    = this.closest('tr').querySelector('.diferencia');
    span.textContent = isNaN(parseInt(this.value)) ? '—' : (diff >= 0 ? '+'+diff : diff);
    span.className   = 'diferencia ' + (diff > 0 ? 'text-success' : diff < 0 ? 'text-danger' : 'text-muted');
  });
});
</script>
@endpush
@endsection
