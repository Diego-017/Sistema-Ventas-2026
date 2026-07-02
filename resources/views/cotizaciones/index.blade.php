@extends('layouts.main')
@section('title','Cotizaciones')
@section('content')
<div class="page-header">
  <h1>📄 Administrar Cotizaciones</h1>
  <a href="{{ route('cotizaciones.nueva') }}" class="btn btn-primary">+ Nueva Cotización</a>
</div>
<div class="card">
  <div class="card-body p-0">
    <div class="table-toolbar">
      <input type="search" id="searchTable" placeholder="Buscar..." class="form-control" style="max-width:260px">
    </div>
    <table class="table" id="cotTable">
      <thead>
        <tr><th>Código</th><th>Fecha</th><th>Cliente</th><th>Total</th><th>Válida hasta</th><th>Estado</th><th></th></tr>
      </thead>
      <tbody>
        @forelse($cotizaciones as $c)
        <tr>
          <td><code>{{ $c->codigo }}</code></td>
          <td>{{ $c->created_at->format('d/m/Y H:i') }}</td>
          <td>{{ $c->cliente->nombre ?? 'Cliente general' }}</td>
          <td><strong>${{ number_format($c->total,2) }}</strong></td>
          <td>
            @if($c->valida_hasta)
              {{ $c->valida_hasta->format('d/m/Y') }}
              @if($c->estado==='vigente' && $c->valida_hasta->isPast())
                <span class="badge badge-danger">Vencida hoy</span>
              @endif
            @else —
            @endif
          </td>
          <td>
            <span class="badge badge-{{ $c->estado==='vigente'?'success':($c->estado==='convertida'?'primary':($c->estado==='vencida'?'danger':'warning')) }}">
              {{ $c->estado }}
            </span>
          </td>
          <td class="actions">
            <a href="{{ route('cotizaciones.show',$c->id) }}" class="btn btn-sm btn-secondary">👁️</a>
            @if($c->estado==='vigente')
            <form method="POST" action="{{ route('cotizaciones.convertir',$c->id) }}" style="display:inline"
                  onsubmit="return confirm('¿Convertir esta cotización a venta?')">
              @csrf
              <button type="submit" class="btn btn-sm btn-success">🛍️ Vender</button>
            </form>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted" style="padding:2rem">Sin cotizaciones.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="table-footer">{{ $cotizaciones->links() }}</div>
  </div>
</div>
@push('scripts')
<script>
document.getElementById('searchTable').addEventListener('input',function(){
  const q=this.value.toLowerCase();
  document.querySelectorAll('#cotTable tbody tr').forEach(r=>{r.style.display=r.textContent.toLowerCase().includes(q)?'':'none';});
});
</script>
@endpush
@endsection
