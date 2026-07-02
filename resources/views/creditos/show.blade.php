@extends('layouts.main')
@section('title','Detalle de Crédito')
@section('content')
<div class="page-header">
  <h1>💳 Detalle de Crédito</h1>
  <div>
    <button onclick="window.print()" class="btn btn-secondary">🖨️ Imprimir</button>
    <a href="{{ route('creditos.index') }}" class="btn btn-secondary">← Volver</a>
  </div>
</div>

<div class="grid-2">
  <div>
    <div class="card">
      <div class="card-header"><h3>Información del Crédito</h3></div>
      <div class="card-body">
        <div class="info-grid">
          <div class="info-item"><span>Cliente</span><strong>{{ $credito->cliente->nombre }}</strong></div>
          <div class="info-item"><span>Estado</span>
            <span class="badge badge-{{ $credito->estado==='pagado'?'success':($credito->estado==='vencido'?'danger':'warning') }}">
              {{ strtoupper($credito->estado) }}
            </span>
          </div>
          <div class="info-item"><span>Monto Total</span><strong>${{ number_format($credito->monto_total,2) }}</strong></div>
          <div class="info-item"><span>Pagado</span><strong class="text-success">${{ number_format($credito->monto_pagado,2) }}</strong></div>
          <div class="info-item"><span>Saldo Pendiente</span>
            <strong class="{{ $credito->saldo > 0 ? 'text-danger':'' }}">${{ number_format($credito->saldo,2) }}</strong>
          </div>
          <div class="info-item"><span>Vence</span>
            <strong>{{ $credito->fecha_vencimiento ? $credito->fecha_vencimiento->format('d/m/Y') : 'Sin fecha' }}</strong>
          </div>
          <div class="info-item"><span>Registrado por</span><strong>{{ $credito->usuario->nombre }}</strong></div>
          <div class="info-item"><span>Fecha</span><strong>{{ $credito->created_at->format('d/m/Y H:i') }}</strong></div>
        </div>
        @if($credito->notas)
        <p class="text-muted small" style="margin-top:12px">{{ $credito->notas }}</p>
        @endif
      </div>
    </div>

    {{-- Historial de pagos --}}
    <div class="card">
      <div class="card-header"><h3>Historial de Pagos</h3></div>
      <div class="card-body p-0">
        @if($credito->pagos->isEmpty())
          <div class="empty-state">Sin pagos registrados.</div>
        @else
        <table class="table">
          <thead><tr><th>Fecha</th><th>Monto</th><th>Método</th><th>Registró</th></tr></thead>
          <tbody>
            @foreach($credito->pagos as $p)
            <tr>
              <td>{{ $p->created_at->format('d/m/Y H:i') }}</td>
              <td class="text-success"><strong>${{ number_format($p->monto,2) }}</strong></td>
              <td>{{ ucfirst($p->metodo) }}</td>
              <td>{{ $p->usuario->nombre }}</td>
            </tr>
            @endforeach
          </tbody>
        </table>
        @endif
      </div>
    </div>
  </div>

  {{-- Registrar pago --}}
  @if($credito->estado !== 'pagado')
  <div>
    <div class="card">
      <div class="card-header"><h3>💵 Registrar Pago</h3></div>
      <div class="card-body">
        <div class="caja-resumen-cierre" style="margin-bottom:20px">
          <div class="resumen-row"><span>Monto Total:</span><span>${{ number_format($credito->monto_total,2) }}</span></div>
          <div class="resumen-row"><span>Ya Pagado:</span><span class="text-success">${{ number_format($credito->monto_pagado,2) }}</span></div>
          <div class="resumen-row resumen-total"><span>Saldo Actual:</span><span>${{ number_format($credito->saldo,2) }}</span></div>
        </div>
        <form method="POST" action="{{ route('creditos.pago',$credito->id) }}">
          @csrf
          <div class="form-group">
            <label>Monto a Pagar ($) <span class="req">*</span></label>
            <input type="number" name="monto" step="0.01" min="0.01" max="{{ $credito->saldo }}"
                   class="form-control" placeholder="0.00" required>
          </div>
          <div class="form-group">
            <label>Método de Pago</label>
            <select name="metodo" class="form-control">
              <option value="efectivo">💵 Efectivo</option>
              <option value="tarjeta">💳 Tarjeta</option>
              <option value="transferencia">🏦 Transferencia</option>
            </select>
          </div>
          <div class="form-group">
            <label>Notas</label>
            <input type="text" name="notas" class="form-control" placeholder="Observaciones...">
          </div>
          <button type="submit" class="btn btn-success btn-block btn-lg">✅ Registrar Pago</button>
        </form>
      </div>
    </div>
  </div>
  @endif
</div>
@endsection
