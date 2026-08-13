@extends('layouts.main')
@section('title','Movimientos de Caja')
@section('content')

<div class="page-header">
  <div>
    <h1>🧮 Movimientos de Caja</h1>
    <p class="page-sub">Historial de ingresos y salidas registrados en todas las cajas</p>
  </div>
  <div style="display:flex;gap:.5rem">
    <a href="{{ route('caja.index') }}" class="btn btn-light">💰 Ir a Caja</a>
    @if($caja)
    <button type="button" class="btn btn-success" onclick="abrirModalMovimiento('ingreso')">📥 Ingreso a caja</button>
    <button type="button" class="btn btn-danger" onclick="abrirModalMovimiento('egreso')">📤 Salida de caja</button>
    @endif
  </div>
</div>

@if(!$caja)
<div class="alert alert-warning" style="margin-bottom:1rem">
  ⚠️ No hay ninguna caja abierta en este momento. Puedes consultar el historial, pero para registrar
  nuevos movimientos primero debes <a href="{{ route('caja.index') }}">abrir una caja</a>.
</div>
@endif

<div class="card">
  <div class="card-body p-0">
    <form method="GET" action="{{ route('caja.movimientos') }}" class="table-toolbar" style="flex-wrap:wrap;gap:.6rem">
      <input type="search" name="buscar" value="{{ request('buscar') }}" placeholder="Buscar por concepto..." class="form-control" style="max-width:240px">
      <select name="caja_id" class="form-control" style="max-width:200px">
        <option value="">Todas las cajas</option>
        @foreach($cajas as $c)
          <option value="{{ $c->id }}" {{ (string)request('caja_id')===(string)$c->id?'selected':'' }}>{{ $c->nombre }}</option>
        @endforeach
      </select>
      <input type="date" name="desde" value="{{ request('desde') }}" class="form-control" style="max-width:160px">
      <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control" style="max-width:160px">
      <button type="submit" class="btn btn-primary">🔍 Filtrar</button>
      @if(request()->hasAny(['buscar','caja_id','desde','hasta']))
        <a href="{{ route('caja.movimientos') }}" class="btn btn-light">Limpiar</a>
      @endif
    </form>

    <table class="table">
      <thead>
        <tr>
          <th>ID</th><th>Concepto</th><th>Caja</th><th>Fecha</th>
          <th>Monto</th><th>Empleado</th><th>Tipo</th><th></th>
        </tr>
      </thead>
      <tbody>
        @forelse($movimientos as $m)
        <tr>
          <td class="text-muted">#{{ $m->id }}</td>
          <td>{{ $m->concepto }}</td>
          <td>{{ $m->caja->nombre ?? '—' }}</td>
          <td>{{ $m->created_at->format('d-m-Y h:i A') }}</td>
          <td class="{{ $m->tipo==='ingreso' ? 'text-success' : 'text-danger' }}">
            <strong>{{ $m->tipo==='ingreso' ? '+' : '-' }}${{ number_format($m->monto,2) }}</strong>
          </td>
          <td>{{ $m->usuario->nombre ?? '—' }}</td>
          <td>
            <span class="badge badge-{{ $m->tipo==='ingreso' ? 'success' : 'danger' }}">
              {{ $m->tipo==='ingreso' ? 'Entrada' : 'Salida' }}
            </span>
          </td>
          <td>
            <form method="POST" action="{{ route('caja.movimientos.eliminar',$m->id) }}"
                  onsubmit="return confirm('¿Eliminar este movimiento? Esta acción no se puede deshacer.')">
              @csrf @method('DELETE')
              <button type="submit" class="btn btn-sm btn-light" title="Eliminar">🗑️</button>
            </form>
          </td>
        </tr>
        @empty
        <tr><td colspan="8" class="text-center text-muted" style="padding:2rem">Sin movimientos registrados.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="table-footer">{{ $movimientos->links() }}</div>
  </div>
</div>

{{-- Modal registrar movimiento --}}
<div class="modal-overlay" id="modalMovimiento" style="display:none">
  <div class="modal" style="max-width:420px">
    <div class="modal-header">
      <h3 id="modalMovimientoTitulo">Registrar Movimiento</h3>
      <button type="button" onclick="cerrarModalMovimiento()">&times;</button>
    </div>
    <form method="POST" action="{{ route('caja.gasto') }}">
      @csrf
      <input type="hidden" name="tipo" id="modalMovimientoTipo" value="egreso">
      <div class="modal-body">
        <div class="form-group">
          <label>Concepto <span class="req">*</span></label>
          <input type="text" name="concepto" class="form-control" required placeholder="Ej: Pago de servicios, venta de bolsas...">
        </div>
        <div class="form-group">
          <label>Monto ($) <span class="req">*</span></label>
          <input type="number" name="monto" step="0.01" min="0.01" class="form-control" required placeholder="0.00">
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-light" onclick="cerrarModalMovimiento()">Cancelar</button>
        <button type="submit" class="btn btn-primary">Guardar</button>
      </div>
    </form>
  </div>
</div>

@push('scripts')
<script>
function abrirModalMovimiento(tipo) {
  document.getElementById('modalMovimientoTipo').value = tipo;
  document.getElementById('modalMovimientoTitulo').textContent =
    tipo === 'ingreso' ? '📥 Registrar Ingreso a Caja' : '📤 Registrar Salida de Caja';
  document.getElementById('modalMovimiento').style.display = 'flex';
}
function cerrarModalMovimiento() {
  document.getElementById('modalMovimiento').style.display = 'none';
}
</script>
@endpush
@endsection
