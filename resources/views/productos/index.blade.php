@extends('layouts.main')
@section('title','Productos')
@section('content')
<div class="page-header">
  <h1>📦 Productos</h1>
  @if(session('user.rol')==='admin')
  <a href="{{ route('productos.crear') }}" class="btn btn-primary">+ Nuevo</a>
  @endif
</div>
<div class="card">
  <div class="card-body p-0">
    <div class="table-toolbar">
      <input type="search" id="searchTable" placeholder="Buscar..." class="form-control" style="max-width:260px">
    </div>
    <table class="table" id="prodTable">
      <thead><tr><th>SKU</th><th>Nombre</th><th>Categoría</th><th>P. Compra</th><th>P. Venta</th><th>Stock</th><th>Acciones</th></tr></thead>
      <tbody>
        @forelse($productos as $p)
        <tr>
          <td>{{ $p->sku ?? '—' }}</td>
          <td>
            @if($p->imagen)
              <img src="{{ asset('storage/'.$p->imagen) }}" class="thumb" alt="">
            @endif
            {{ $p->nombre }}
          </td>
          <td>{{ $p->categoria->nombre ?? '—' }}</td>
          <td>${{ number_format($p->precio_compra,2) }}</td>
          <td>${{ number_format($p->precio_venta,2) }}</td>
          <td>
            <span class="badge {{ $p->stock<=$p->stock_minimo?'badge-danger':'badge-success' }}">
              {{ $p->stock }}
            </span>
          </td>
          <td class="actions">
            @if(session('user.rol')==='admin')
            <a href="{{ route('productos.editar',$p->id) }}" class="btn btn-sm btn-secondary">✏️</a>
            <button onclick="delProd({{ $p->id }},this)" class="btn btn-sm btn-danger">🗑️</button>
            @else
            <a href="{{ route('productos.editar',$p->id) }}" class="btn btn-sm btn-secondary">👁️</a>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted" style="padding:2rem">Sin productos.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="table-footer">{{ $productos->links() }}</div>
  </div>
</div>
@push('scripts')
<script>
document.getElementById('searchTable').addEventListener('input', function(){
  const q=this.value.toLowerCase();
  document.querySelectorAll('#prodTable tbody tr').forEach(r=>{r.style.display=r.textContent.toLowerCase().includes(q)?'':'none';});
});
async function delProd(id,btn){
  if(!confirm('¿Eliminar producto?')) return;
  btn.disabled=true;
  const r=await fetch(`/productos/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content,'Accept':'application/json'}});
  const d=await r.json();
  if(d.ok) btn.closest('tr').remove();
  else{alert('Error.');btn.disabled=false;}
}
</script>
@endpush
@endsection
