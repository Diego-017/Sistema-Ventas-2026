@extends('layouts.main')
@section('title','Detalle de Cliente')
@section('content')
<div class="page-header">
  <h1>👤 {{ $cliente->nombre }}</h1>
  <a href="{{ route('clientes.index') }}" class="btn btn-light">← Volver</a>
</div>

<div class="grid-2">
  <div>
    <div class="card">
      <div class="card-header"><h3>Información del Cliente</h3></div>
      <div class="card-body">
        <div class="info-grid">
          <div class="info-item"><span>NIT</span><strong>{{ $cliente->nit ?? '—' }}</strong></div>
          <div class="info-item"><span>Tipo</span>
            <span class="badge badge-{{ $cliente->tipo==='credito'?'warning':'success' }}">
              {{ $cliente->tipo === 'credito' ? '💳 Crédito' : '💵 Contado' }}
            </span>
          </div>
          <div class="info-item"><span>Teléfono</span><strong>{{ $cliente->telefono ?? '—' }}</strong></div>
          <div class="info-item"><span>Email</span><strong>{{ $cliente->email ?? '—' }}</strong></div>
          @if($cliente->tipo === 'credito')
          <div class="info-item"><span>Límite Crédito</span><strong>${{ number_format($cliente->limite_credito,2) }}</strong></div>
          <div class="info-item"><span>Saldo Actual</span><strong class="text-danger">${{ number_format($cliente->saldo_credito,2) }}</strong></div>
          @endif
        </div>
        @if($cliente->direccion)
        <p class="text-muted small" style="margin-top:12px">📍 {{ $cliente->direccion }}</p>
        @endif
      </div>
    </div>

    <div class="card">
      <div class="card-header"><h3>Historial de Compras</h3></div>
      <div class="card-body p-0">
        <table class="table">
          <thead><tr><th>Código</th><th>Fecha</th><th>Total</th><th>Pago</th></tr></thead>
          <tbody>
            @forelse($ventas as $v)
            <tr>
              <td><a href="{{ route('ventas.ver',$v->id) }}"><code>{{ $v->codigo }}</code></a></td>
              <td>{{ $v->created_at->format('d/m/Y') }}</td>
              <td><strong>${{ number_format($v->total,2) }}</strong></td>
              <td>{{ ucfirst($v->metodo_pago) }}</td>
            </tr>
            @empty
            <tr><td colspan="4" class="text-center text-muted" style="padding:1.5rem">Sin compras registradas.</td></tr>
            @endforelse
          </tbody>
        </table>
        <div class="table-footer">{{ $ventas->links() }}</div>
      </div>
    </div>
  </div>

  <div>
    <div class="stat-card stat-primary" style="margin-bottom:20px">
      <div class="stat-icon-wrap">💰</div>
      <div class="stat-info">
        <div class="stat-value">${{ number_format($totalComprado,2) }}</div>
        <div class="stat-label">Total Comprado Histórico</div>
      </div>
    </div>

    @if($creditos->isNotEmpty())
    <div class="card">
      <div class="card-header"><h3>💳 Créditos</h3></div>
      <div class="card-body p-0">
        <table class="table">
          <thead><tr><th>Saldo</th><th>Estado</th><th></th></tr></thead>
          <tbody>
            @foreach($creditos as $c)
            <tr>
              <td>${{ number_format($c->saldo,2) }}</td>
              <td><span class="badge badge-{{ $c->estado==='pagado'?'success':($c->estado==='vencido'?'danger':'warning') }}">{{ $c->estado }}</span></td>
              <td><a href="{{ route('creditos.show',$c->id) }}" class="btn btn-sm btn-light">Ver</a></td>
            </tr>
            @endforeach
          </tbody>
        </table>
      </div>
    </div>
    @endif
  </div>
</div>
@endsection
