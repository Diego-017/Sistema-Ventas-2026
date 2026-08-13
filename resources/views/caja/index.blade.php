@extends('layouts.main')
@section('title','Caja')
@section('content')

<div class="page-header">
  <div>
    <h1>💰 Gestión de Caja</h1>
    <p class="page-sub">Control de apertura, cierre y movimientos del día</p>
  </div>
  <a href="{{ route('caja.historial') }}" class="btn btn-light">📋 Historial de Cortes</a>
</div>

@if(!$caja)
<div class="card">
  <div class="card-body text-center" style="padding:3rem">
    <div style="font-size:4rem;margin-bottom:1rem">🔐</div>
    <h2 style="margin-bottom:.5rem">Caja Cerrada</h2>
    <p class="text-muted" style="margin-bottom:2rem">Abre la caja para comenzar a registrar ventas del día.</p>
    <form method="POST" action="{{ route('caja.abrir') }}" style="max-width:400px;margin:0 auto">
      @csrf
      <div class="form-group">
        <label>Nombre de Caja</label>
        <input type="text" name="nombre" class="form-control" value="Caja Principal">
      </div>
      <div class="form-group">
        <label>Monto de Apertura ($) <span class="req">*</span></label>
        <input type="number" name="monto_apertura" step="0.01" min="0" class="form-control" placeholder="0.00" required>
      </div>
      <div class="form-group">
        <label>Notas (opcional)</label>
        <input type="text" name="notas_apertura" class="form-control" placeholder="Observaciones...">
      </div>
      <button type="submit" class="btn btn-primary btn-block btn-lg">🔓 Abrir Caja</button>
    </form>
  </div>
</div>

@else
<div class="caja-grid">
  <div>
    <div class="stats-grid" style="grid-template-columns:1fr 1fr">
      <div class="stat-card stat-success">
        <div class="stat-icon-wrap">💵</div>
        <div class="stat-info"><div class="stat-value">${{ number_format($caja->monto_apertura,2) }}</div><div class="stat-label">Apertura</div></div>
      </div>
      <div class="stat-card stat-primary">
        <div class="stat-icon-wrap">🧾</div>
        <div class="stat-info"><div class="stat-value">${{ number_format($caja->total_ventas,2) }}</div><div class="stat-label">Ventas del Día</div></div>
      </div>
      <div class="stat-card stat-danger">
        <div class="stat-icon-wrap">📤</div>
        <div class="stat-info"><div class="stat-value">${{ number_format($caja->total_gastos,2) }}</div><div class="stat-label">Gastos / Retiros</div></div>
      </div>
      <div class="stat-card stat-purple">
        <div class="stat-icon-wrap">💰</div>
        <div class="stat-info"><div class="stat-value">${{ number_format($caja->balance_estimado,2) }}</div><div class="stat-label">Balance Estimado</div></div>
      </div>
    </div>

    <div class="card" id="movimientos">
      <div class="card-header"><h3>➕ Registrar Movimiento</h3></div>
      <div class="card-body">
        <form method="POST" action="{{ route('caja.gasto') }}">
          @csrf
          <div class="form-row">
            <div class="form-group" style="flex:2">
              <label>Concepto <span class="req">*</span></label>
              <input type="text" name="concepto" class="form-control" required placeholder="Ej: Pago de servicios">
            </div>
            <div class="form-group">
              <label>Monto ($) <span class="req">*</span></label>
              <input type="number" name="monto" step="0.01" min="0.01" class="form-control" required placeholder="0.00">
            </div>
            <div class="form-group">
              <label>Tipo</label>
              <select name="tipo" class="form-control">
                <option value="egreso">💸 Egreso</option>
                <option value="retiro">📤 Retiro</option>
                <option value="ingreso">📥 Ingreso Extra</option>
              </select>
            </div>
          </div>
          <button type="submit" class="btn btn-primary">Registrar</button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3>📋 Movimientos de Hoy</h3></div>
      <div class="card-body p-0">
        @if($gastos->isEmpty())
          <div class="empty-state">Sin movimientos registrados.</div>
        @else
        <table class="table">
          <thead><tr><th>Hora</th><th>Concepto</th><th>Tipo</th><th>Monto</th></tr></thead>
          <tbody>
            @foreach($gastos as $g)
            <tr>
              <td class="text-muted">{{ $g->created_at->format('H:i') }}</td>
              <td>{{ $g->concepto }}</td>
              <td><span class="badge badge-{{ $g->tipo==='ingreso'?'success':'danger' }}">{{ $g->tipo }}</span></td>
              <td class="{{ $g->tipo==='ingreso'?'text-success':'text-danger' }}">
                {{ $g->tipo==='ingreso'?'+':'-' }}${{ number_format($g->monto,2) }}
              </td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @endif
      </div>
    </div>
  </div>

  <div>
    <div class="card">
      <div class="card-header"><h3>🔒 Cerrar Caja</h3></div>
      <div class="card-body">
        <div class="caja-resumen">
          <div class="resumen-row"><span>Apertura:</span><span>${{ number_format($caja->monto_apertura,2) }}</span></div>
          <div class="resumen-row"><span>Ventas:</span><span class="text-success">+${{ number_format($caja->total_ventas,2) }}</span></div>
          <div class="resumen-row"><span>Gastos/Retiros:</span><span class="text-danger">-${{ number_format($caja->total_gastos,2) }}</span></div>
          <div class="resumen-row"><span>Efectivo Esperado:</span><span>${{ number_format($caja->balance_estimado,2) }}</span></div>
        </div>
        <form method="POST" action="{{ route('caja.cerrar') }}" class="mt-3" id="cierreForm">
          @csrf
          <p class="help-text mb-2">Conteo de billetes y monedas (opcional, ayuda a calcular el total):</p>
          <div class="denominaciones-grid">
            @foreach([100,50,20,10,5,1,0.25,0.10,0.05] as $d)
            <div class="denom-item">
              <label>${{ $d }}</label>
              <input type="number" min="0" class="form-control form-control-sm denom-input" data-valor="{{ $d }}" placeholder="0">
            </div>
            @endforeach
          </div>
          <input type="hidden" name="denominaciones" id="denomInput">

          <div class="form-group">
            <label>Efectivo Contado Total ($) <span class="req">*</span></label>
            <input type="number" name="monto_cierre" id="montoCierre" step="0.01" min="0" class="form-control" placeholder="0.00" required>
            <p class="help-text">Se calcula automáticamente si llenas el conteo, o ingrésalo manualmente.</p>
          </div>
          <div class="form-group">
            <label>Notas de Cierre</label>
            <textarea name="notas_cierre" class="form-control" rows="2" placeholder="Observaciones..."></textarea>
          </div>
          <button type="submit" class="btn btn-danger btn-block"
                  onclick="return confirm('¿Cerrar la caja del día?')">🔒 Cerrar Caja</button>
        </form>
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3>ℹ️ Información</h3></div>
      <div class="card-body">
        <p><strong>Caja:</strong> {{ $caja->nombre }}</p>
        <p><strong>Abierta por:</strong> {{ $caja->usuario->nombre }}</p>
        <p><strong>Hora apertura:</strong> {{ $caja->abierta_at->format('d/m/Y H:i') }}</p>
        @if($caja->notas_apertura)<p><strong>Notas:</strong> {{ $caja->notas_apertura }}</p>@endif
      </div>
    </div>
  </div>
</div>
@endif

@push('scripts')
<script>
document.querySelectorAll('.denom-input').forEach(inp => {
  inp.addEventListener('input', calcularTotal);
});
function calcularTotal(){
  let total = 0;
  const denoms = {};
  document.querySelectorAll('.denom-input').forEach(inp => {
    const cant = parseInt(inp.value)||0;
    const valor = parseFloat(inp.dataset.valor);
    total += cant * valor;
    if(cant > 0) denoms[valor] = cant;
  });
  document.getElementById('montoCierre').value = total.toFixed(2);
  document.getElementById('denomInput').value = JSON.stringify(denoms);
}
</script>
@endpush
@endsection
