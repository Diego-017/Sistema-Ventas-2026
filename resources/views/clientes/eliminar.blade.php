@extends('layouts.main')
@section('title', 'Eliminar Cliente')
@section('content')

<div class="page-header" style="margin-bottom: 20px">
  <div>
    <h1 style="color: #ef4444; font-size: 1.6rem; font-weight: 800; margin:0">⚠️ Confirmar Eliminación de Cliente</h1>
    <p class="page-sub" style="color: #94a3b8; margin-top:4px">Verifica los datos e impacto antes de proceder con el borrado definitivo</p>
  </div>
  <div>
    <a href="{{ route('clientes.index') }}" class="btn btn-light" style="font-weight:700">← Cancelar y Volver</a>
  </div>
</div>

<div class="card" style="border-radius:10px; max-width:700px; margin:0 auto 30px; border:1px solid #ef4444">
  <div class="card-header" style="background:rgba(239,68,68,0.12); padding:16px 22px; border-bottom:1px solid rgba(239,68,68,0.25)">
    <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:#ef4444; display:flex; align-items:center; gap:8px">
      🗑️ ¿Estás seguro de que deseas eliminar este cliente?
    </h3>
  </div>

  <div class="card-body" style="padding:24px">
    <div style="background:rgba(0,0,0,0.04); padding:16px; border-radius:8px; margin-bottom:20px; border:1px solid #334155">
      <div style="font-size:1.3rem; font-weight:800; color:#38bdf8; margin-bottom:6px">
        {{ $cliente->nombre }}
      </div>
      <div style="font-size:0.9rem; color:#94a3b8; margin-bottom:12px">
        {{ $cliente->nombre_comercial ? 'Comercial: ' . $cliente->nombre_comercial : 'Cliente General' }}
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:0.88rem">
        <div><strong>Documento:</strong> {{ strtoupper($cliente->tipo_documento ?? 'DUI') }}: {{ $cliente->dui ?? $cliente->nit ?? '—' }}</div>
        <div><strong>Teléfono:</strong> {{ $cliente->telefono ?? '—' }}</div>
        <div><strong>Email:</strong> {{ $cliente->email ?? '—' }}</div>
        <div><strong>Tipo:</strong> {{ ucfirst($cliente->tipo ?? 'contado') }}</div>
        <div><strong>Dirección:</strong> {{ $cliente->direccion ?? '—' }}</div>
        <div><strong>Municipio:</strong> {{ $cliente->municipio ?? '—' }}</div>
      </div>
    </div>

    @if($cliente->ventas_count > 0 || $cliente->creditos_count > 0)
    <div class="alert alert-warning" style="margin-bottom:20px">
      ⚠️ <strong>Atención:</strong> Este cliente tiene <strong>{{ $cliente->ventas_count }} ventas</strong> y <strong>{{ $cliente->creditos_count }} registros de crédito</strong> asociados en el historial del sistema.
    </div>
    @endif

    <form action="{{ route('clientes.eliminar', $cliente->id) }}" method="POST">
      @csrf
      @method('DELETE')

      <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #282d3c; padding-top:20px">
        <a href="{{ route('clientes.index') }}" class="btn btn-light" style="padding:10px 20px; font-weight:700">
          ✕ Cancelar (No borrar)
        </a>
        <button type="submit" class="btn btn-danger" style="padding:10px 24px; font-weight:800">
          🗑️ Sí, Eliminar Cliente Definitivamente
        </button>
      </div>
    </form>
  </div>
</div>

@endsection
