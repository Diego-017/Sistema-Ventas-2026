@extends('layouts.main')
@section('title','Historial de Ventas')
@section('content')
<div class="page-header">
  <h1>📋 Historial de Ventas</h1>
  <a href="{{ route('ventas.nueva') }}" class="btn btn-primary">+ Nueva Venta</a>
</div>
<div class="card">
  <div class="card-body p-0">
    <div class="table-toolbar">
      <input type="search" id="searchTable" placeholder="Buscar código, cliente..." class="form-control" style="max-width:280px">
    </div>
    <table class="table" id="ventasTable">
      <thead>
        <tr><th>Código</th><th>Fecha</th><th>Cliente</th><th>Vendedor</th><th>Total</th><th>Pago</th><th>Estado</th><th></th></tr>
      </thead>
      <tbody>
        @forelse($ventas as $v)
        <tr>
          <td><a href="{{ route('ventas.ver',$v->id) }}"><code>{{ $v->codigo }}</code></a></td>
          <td>{{ $v->created_at->format('d/m/Y H:i') }}</td>
          <td>{{ $v->cliente->nombre ?? 'General' }}</td>
          <td>{{ $v->usuario->nombre }}</td>
          <td><strong>${{ number_format($v->total,2) }}</strong></td>
          <td>
            @php $iconos=['efectivo'=>'💵','tarjeta'=>'💳','transferencia'=>'🏦']; @endphp
            {{ $iconos[$v->metodo_pago]??'' }} {{ $v->metodo_pago }}
          </td>
          <td>
            <span class="badge badge-{{ $v->estado==='completada'?'success':($v->estado==='anulada'?'danger':'warning') }}">
              {{ $v->estado }}
            </span>
          </td>
          <td><a href="{{ route('ventas.ver',$v->id) }}" class="btn btn-sm btn-secondary">👁️ Ver</a></td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted" style="padding:2rem">Sin ventas registradas.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="table-footer">{{ $ventas->links() }}</div>
  </div>
</div>
@push('scripts')
<script>
document.getElementById('searchTable').addEventListener('input',function(){
  const q=this.value.toLowerCase();
  document.querySelectorAll('#ventasTable tbody tr').forEach(r=>{
    r.style.display=r.textContent.toLowerCase().includes(q)?'':'none';
  });
});
</script>
@endpush
@endsection
