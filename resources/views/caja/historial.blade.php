@extends('layouts.main')
@section('title','Historial de Caja')
@section('content')
<div class="page-header">
  <h1>📋 Administrar Corte — Historial de Cajas</h1>
  <a href="{{ route('caja.index') }}" class="btn btn-light">← Caja Actual</a>
</div>
<div class="card">
  <div class="card-body p-0">
    <table class="table">
      <thead>
        <tr><th>Fecha</th><th>Caja</th><th>Apertura</th><th>Ventas</th><th>Gastos</th><th>Cierre</th><th>Diferencia</th><th>Estado</th><th>Usuario</th><th></th></tr>
      </thead>
      <tbody>
        @forelse($cajas as $c)
        @php
          $corte = $c->cortes->last();
        @endphp
        <tr>
          <td>{{ $c->abierta_at->format('d/m/Y') }}</td>
          <td>{{ $c->nombre }}</td>
          <td>${{ number_format($c->monto_apertura,2) }}</td>
          <td class="text-success">+${{ number_format($c->total_ventas,2) }}</td>
          <td class="text-danger">-${{ number_format($c->total_gastos,2) }}</td>
          <td>{{ $c->monto_cierre !== null ? '$'.number_format($c->monto_cierre,2) : '—' }}</td>
          <td>
            @if($corte)
              <span class="{{ $corte->diferencia >= 0 ? 'text-success':'text-danger' }}">
                {{ $corte->diferencia >= 0 ? '+':'' }}${{ number_format($corte->diferencia,2) }}
              </span>
            @else — @endif
          </td>
          <td><span class="badge badge-{{ $c->estado==='cerrada'?'success':'warning' }}">{{ $c->estado }}</span></td>
          <td>{{ $c->usuario->nombre }}</td>
          <td><a href="{{ route('caja.corte',$c->id) }}" class="btn btn-sm btn-light">👁️</a></td>
        </tr>
        @empty
        <tr><td colspan="10" class="text-center text-muted" style="padding:2rem">Sin registros.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="table-footer">{{ $cajas->links() }}</div>
  </div>
</div>
@endsection
