@extends('layouts.main')
@section('title','Stock por Lote')
@section('content')
<div class="page-header">
  <h1>🔢 Consulta de Stock por Lote</h1>
  <a href="{{ route('inventario.stock') }}" class="btn btn-secondary">← Consultar Stock</a>
</div>
<div class="card">
  <div class="card-body p-0">
    <div class="table-toolbar">
      <input type="search" id="searchTable" placeholder="Buscar lote o producto..." class="form-control" style="max-width:280px">
    </div>
    <table class="table" id="loteTable">
      <thead>
        <tr><th>Lote #</th><th>Producto</th><th>Categoría</th><th>Cant. Inicial</th><th>Cant. Actual</th><th>Costo Unit.</th><th>Valor Total</th><th>Vencimiento</th><th>Ingresado</th></tr>
      </thead>
      <tbody>
        @forelse($lotes as $l)
        @php
          $vence = $l->fecha_vencimiento;
          $diasRestantes = $vence ? now()->diffInDays($vence, false) : null;
          $rowClass = '';
          if($diasRestantes !== null){
            if($diasRestantes < 0) $rowClass = 'row-danger';
            elseif($diasRestantes <= 30) $rowClass = 'row-warning';
          }
        @endphp
        <tr class="{{ $rowClass }}">
          <td><code>{{ $l->numero_lote ?? 'Sin lote' }}</code></td>
          <td>{{ $l->producto->nombre }}</td>
          <td>{{ $l->producto->categoria->nombre ?? '—' }}</td>
          <td>{{ $l->cantidad_inicial }}</td>
          <td>
            <span class="badge badge-{{ $l->cantidad_actual > 0 ? 'success' : 'danger' }}">
              {{ $l->cantidad_actual }}
            </span>
          </td>
          <td>${{ number_format($l->costo_unitario,2) }}</td>
          <td>${{ number_format($l->cantidad_actual * $l->costo_unitario,2) }}</td>
          <td>
            @if($vence)
              {{ $vence->format('d/m/Y') }}
              @if($diasRestantes !== null)
                <br><small class="{{ $diasRestantes < 0 ? 'text-danger' : ($diasRestantes <= 30 ? 'text-warning' : 'text-muted') }}">
                  {{ $diasRestantes < 0 ? 'Vencido hace '.abs($diasRestantes).'d' : 'Vence en '.$diasRestantes.'d' }}
                </small>
              @endif
            @else
              <span class="text-muted">Sin vencimiento</span>
            @endif
          </td>
          <td>{{ $l->created_at->format('d/m/Y') }}</td>
        </tr>
        @empty
        <tr><td colspan="9" class="text-center text-muted" style="padding:2rem">Sin lotes registrados.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="table-footer">{{ $lotes->links() }}</div>
  </div>
</div>
@push('scripts')
<script>
document.getElementById('searchTable').addEventListener('input', function(){
  const q=this.value.toLowerCase();
  document.querySelectorAll('#loteTable tbody tr').forEach(r=>{r.style.display=r.textContent.toLowerCase().includes(q)?'':'none';});
});
</script>
@endpush
@endsection
