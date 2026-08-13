@extends('layouts.main')
@section('title','Compras')
@section('content')
<div class="page-header">
  <h1>🛒 Compras / Entradas de Inventario</h1>
  <a href="{{ route('compras.crear') }}" class="btn btn-primary">+ Nueva Compra</a>
</div>
<div class="card">
  <div class="card-body p-0">
    <div class="table-toolbar">
      <input type="search" id="searchTable" placeholder="Buscar..." class="form-control" style="max-width:260px">
    </div>
    <table class="table" id="comprasTable">
      <thead><tr><th>Código</th><th>Fecha</th><th>Proveedor</th><th>Registró</th><th>Total</th><th>Estado</th><th></th></tr></thead>
      <tbody>
        @forelse($compras as $c)
        <tr>
          <td><code>{{ $c->codigo }}</code></td>
          <td>{{ $c->created_at->format('d/m/Y H:i') }}</td>
          <td>{{ $c->proveedor->nombre ?? 'Sin proveedor' }}</td>
          <td>{{ $c->usuario->nombre }}</td>
          <td><strong>${{ number_format($c->total,2) }}</strong></td>
          <td><span class="badge badge-{{ $c->estado==='completada'?'success':($c->estado==='anulada'?'danger':'warning') }}">{{ $c->estado }}</span></td>
          <td>
            <a href="{{ route('compras.ver',$c->id) }}" class="btn btn-sm btn-secondary">👁️</a>
            @if($c->estado==='completada')
            <button onclick="anularCompra({{ $c->id }})" class="btn btn-sm btn-danger">❌</button>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted" style="padding:2rem">Sin compras registradas.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="table-footer">{{ $compras->links() }}</div>
  </div>
</div>
@push('scripts')
<script>
document.getElementById('searchTable').addEventListener('input', function() {
  const q = this.value.toLowerCase();
  document.querySelectorAll('#comprasTable tbody tr').forEach(r => {
    r.style.display = r.textContent.toLowerCase().includes(q) ? '':'none';
  });
});
function anularCompra(id) {
  if (!confirm('¿Anular esta compra? Se revertirá el stock.')) return;
  fetch(`/compras/${id}/anular`, {
    method:'PATCH',
    headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'}
  }).then(r=>r.json()).then(d=>{ if(d.ok) location.reload(); });
}
</script>
@endpush
@endsection
