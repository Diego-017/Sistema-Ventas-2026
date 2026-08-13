@extends('layouts.main')
@section('title','Ventas por Vendedor')
@section('content')
<div class="page-header">
  <h1>🏅 Ventas por Vendedor — {{ now()->translatedFormat('F Y') }}</h1>
  <a href="{{ route('ventas.index') }}" class="btn btn-light">← Volver</a>
</div>
<div class="card">
  <div class="card-body p-0">
    <table class="table">
      <thead><tr><th>#</th><th>Vendedor</th><th>Ventas</th><th>Monto Total</th><th>Promedio</th></tr></thead>
      <tbody>
        @forelse($datos as $i => $d)
        <tr>
          <td><span class="badge badge-{{ $i===0?'success':'primary' }}">{{ $i+1 }}</span></td>
          <td><strong>{{ $d->nombre }}</strong></td>
          <td>{{ $d->total_ventas }}</td>
          <td><strong>${{ number_format($d->monto_total,2) }}</strong></td>
          <td>${{ number_format($d->monto_total / max(1,$d->total_ventas),2) }}</td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center text-muted" style="padding:2rem">Sin datos este mes.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>
@endsection
