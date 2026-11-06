@extends('layouts.main')
@section('title', 'Eliminar Proveedor')
@section('content')

<div class="page-header" style="margin-bottom: 20px">
  <div>
    <h1 style="color: #ef4444; font-size: 1.6rem; font-weight: 800; margin:0">⚠️ Confirmar Eliminación de Proveedor</h1>
    <p class="page-sub" style="color: #94a3b8; margin-top:4px">Verifica los datos y compras asociadas antes de proceder</p>
  </div>
  <div>
    <a href="{{ route('proveedores.index') }}" class="btn btn-light" style="font-weight:700">← Cancelar y Volver</a>
  </div>
</div>

<div class="card" style="border-radius:10px; max-width:700px; margin:0 auto 30px; border:1px solid #ef4444">
  <div class="card-header" style="background:rgba(239,68,68,0.12); padding:16px 22px; border-bottom:1px solid rgba(239,68,68,0.25)">
    <h3 style="margin:0; font-size:1.15rem; font-weight:800; color:#ef4444; display:flex; align-items:center; gap:8px">
      🗑️ ¿Estás seguro de que deseas eliminar este proveedor?
    </h3>
  </div>

  <div class="card-body" style="padding:24px">
    <div style="background:rgba(0,0,0,0.04); padding:16px; border-radius:8px; margin-bottom:20px; border:1px solid #334155">
      <div style="font-size:1.3rem; font-weight:800; color:#38bdf8; margin-bottom:6px">
        {{ $proveedor->nombre }}
      </div>
      <div style="font-size:0.9rem; color:#94a3b8; margin-bottom:12px">
        {{ $proveedor->razon_social ? 'Razón Social: ' . $proveedor->razon_social : 'Proveedor Comercial' }}
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:10px; font-size:0.88rem">
        <div><strong>NIT:</strong> {{ $proveedor->nit ?? '—' }}</div>
        <div><strong>NRC:</strong> {{ $proveedor->nrc ?? '—' }}</div>
        <div><strong>Teléfono:</strong> {{ $proveedor->telefono ?? '—' }}</div>
        <div><strong>Email:</strong> {{ $proveedor->email ?? '—' }}</div>
        <div><strong>Contacto:</strong> {{ $proveedor->contacto ?? '—' }}</div>
        <div><strong>Dirección:</strong> {{ $proveedor->direccion ?? '—' }}</div>
      </div>
    </div>

    @if($proveedor->compras_count > 0)
    <div class="alert alert-warning" style="margin-bottom:20px">
      ⚠️ <strong>Atención:</strong> Este proveedor tiene <strong>{{ $proveedor->compras_count }} compras</strong> registradas en el módulo de compras e inventario.
    </div>
    @endif

    <form action="{{ route('proveedores.eliminar', $proveedor->id) }}" method="POST">
      @csrf
      @method('DELETE')

      <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #282d3c; padding-top:20px">
        <a href="{{ route('proveedores.index') }}" class="btn btn-light" style="padding:10px 20px; font-weight:700">
          ✕ Cancelar (No borrar)
        </a>
        <button type="submit" class="btn btn-danger" style="padding:10px 24px; font-weight:800">
          🗑️ Sí, Eliminar Proveedor Definitivamente
        </button>
      </div>
    </form>
  </div>
</div>

@endsection
