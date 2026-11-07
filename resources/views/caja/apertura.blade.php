@extends('layouts.main')
@section('title', 'Apertura de Caja y Turno')

@section('content')
<div class="corte-admin-container" style="max-width:760px; margin:0 auto; padding:20px 15px">
  {{-- Header con Icono y Breadcrumb --}}
  <div class="corte-page-header" style="margin-bottom:24px">
    <div class="corte-title-wrap">
      <span class="corte-main-icon" style="color:#10b981; font-size:1.8rem"><i class="fa-solid fa-lock-open"></i></span>
      <div>
        <h1 class="corte-main-title" style="font-size:1.6rem; font-weight:800; margin:0">Apertura de Caja y Turno</h1>
        <p style="color:#94a3b8; font-size:0.88rem; margin-top:3px">Habilite la caja registradora especificando sucursal, turno y fondo inicial</p>
      </div>
    </div>
    <div class="corte-breadcrumb" style="font-size:0.85rem">
      <a href="{{ route('dashboard') }}" style="color:#38bdf8">Inicio</a>
      <span style="color:#64748b">/</span>
      <a href="{{ route('caja.administrar_corte') }}" style="color:#38bdf8">Corte caja</a>
      <span style="color:#64748b">/</span>
      <span class="active" style="color:#94a3b8">Apertura</span>
    </div>
  </div>

  @if(session('error'))
    <div class="alert alert-danger" style="background:#ef444422; border:1px solid #ef4444; color:#fca5a5; padding:12px 16px; border-radius:8px; margin-bottom:20px">
      <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
    </div>
  @endif

  {{-- Card Principal del Formulario --}}
  <div class="card" style="border-radius:12px; border:1px solid #2c3244; background:#1a1e29; box-shadow:0 8px 24px rgba(0,0,0,0.25)">
    <div class="card-header" style="background:#202534; padding:16px 22px; border-bottom:1px solid #2c3244">
      <h3 style="margin:0; font-size:1.05rem; font-weight:700; color:#38bdf8; display:flex; align-items:center; gap:8px">
        <i class="fa-solid fa-cash-register"></i> Ficha de Registro de Apertura
      </h3>
    </div>

    <form action="{{ route('caja.abrir') }}" method="POST" style="padding:24px 22px">
      @csrf

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:18px">
        {{-- Sucursal --}}
        <div>
          <label style="display:block; font-size:0.85rem; font-weight:700; color:#e2e8f0; margin-bottom:6px">
            Sucursal / Establecimiento <span style="color:#ef4444">*</span>
          </label>
          <select name="sucursal" class="form-control" style="width:100%; height:42px; border-radius:6px; font-weight:600; background:#12141c; color:#ffffff; border-color:#2c3244" required>
            @foreach($sucursales as $s)
              <option value="{{ $s }}" {{ $sucursalSeleccionada === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
          <small style="color:#64748b; font-size:0.75rem; display:block; margin-top:4px">Seleccione la sede operativa de esta apertura.</small>
        </div>

        {{-- Caja --}}
        <div>
          <label style="display:block; font-size:0.85rem; font-weight:700; color:#e2e8f0; margin-bottom:6px">
            Caja Registradora <span style="color:#ef4444">*</span>
          </label>
          <select name="caja_id" class="form-control" style="width:100%; height:42px; border-radius:6px; font-weight:600; background:#12141c; color:#ffffff; border-color:#2c3244" required>
            @foreach($cajas as $c)
              <option value="{{ $c->id }}" {{ $cajaSeleccionadaId == $c->id ? 'selected' : '' }}>
                {{ $c->nombre }} ({{ ucfirst($c->estado) }})
              </option>
            @endforeach
          </select>
          <small style="color:#64748b; font-size:0.75rem; display:block; margin-top:4px">Dispositivo o gaveta física a inicializar.</small>
        </div>
      </div>

      <div style="display:grid; grid-template-columns:1fr 1fr; gap:18px; margin-bottom:18px">
        {{-- Cajero Responsable --}}
        <div>
          <label style="display:block; font-size:0.85rem; font-weight:700; color:#e2e8f0; margin-bottom:6px">
            Cajero / Responsable
          </label>
          <div style="position:relative">
            <input type="text" class="form-control" value="{{ session('user.nombre', $usuarioActual->nombre ?? 'Usuario') }}" readonly
                   style="width:100%; height:42px; border-radius:6px; background:#151822; color:#38bdf8; font-weight:700; border-color:#2c3244; padding-left:36px">
            <span style="position:absolute; left:12px; top:12px; color:#64748b"><i class="fa-solid fa-user-check"></i></span>
          </div>
          <small style="color:#64748b; font-size:0.75rem; display:block; margin-top:4px">Usuario que apertura y responde por el corte.</small>
        </div>

        {{-- Turno --}}
        <div>
          <label style="display:block; font-size:0.85rem; font-weight:700; color:#e2e8f0; margin-bottom:6px">
            Turno Asignado <span style="color:#ef4444">*</span>
          </label>
          <select name="turno" class="form-control" style="width:100%; height:42px; border-radius:6px; font-weight:600; background:#12141c; color:#ffffff; border-color:#2c3244" required>
            <option value="1" selected>Turno 1</option>
            <option value="2">Turno 2</option>
            <option value="3">Turno 3</option>
            <option value="Mañana">Turno Mañana</option>
            <option value="Tarde">Turno Tarde</option>
            <option value="Completo">Turno Completo</option>
          </select>
          <small style="color:#64748b; font-size:0.75rem; display:block; margin-top:4px">Turno de trabajo correspondiente.</small>
        </div>
      </div>

      {{-- Monto de Apertura --}}
      <div style="margin-bottom:18px">
        <label style="display:block; font-size:0.85rem; font-weight:700; color:#e2e8f0; margin-bottom:6px">
          Monto de Apertura ($) <span style="color:#ef4444">*</span>
        </label>
        <div style="position:relative">
          <span style="position:absolute; left:14px; top:11px; font-size:1.1rem; font-weight:800; color:#10b981">$</span>
          <input type="number" step="0.01" min="0" name="monto_apertura" value="0.00" class="form-control" required autofocus
                 style="width:100%; height:44px; border-radius:6px; background:#12141c; color:#10b981; font-size:1.15rem; font-weight:800; border-color:#2c3244; padding-left:32px">
        </div>
        <small style="color:#94a3b8; font-size:0.78rem; display:block; margin-top:4px">
          <i class="fa-solid fa-circle-info" style="color:#38bdf8"></i> Fondo inicial en efectivo que queda en la gaveta física para cambio.
        </small>
      </div>

      {{-- Observaciones --}}
      <div style="margin-bottom:24px">
        <label style="display:block; font-size:0.85rem; font-weight:700; color:#e2e8f0; margin-bottom:6px">
          Observaciones / Notas de Apertura
        </label>
        <textarea name="notas_apertura" class="form-control" rows="2" placeholder="Ej. Gaveta revisada, billetes de baja denominación provistos..."
                  style="width:100%; border-radius:6px; background:#12141c; color:#ffffff; border-color:#2c3244; resize:none"></textarea>
      </div>

      {{-- Botones de Acción --}}
      <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #2c3244; padding-top:18px">
        <a href="{{ route('caja.administrar_corte') }}" class="btn btn-secondary" style="padding:10px 20px; font-weight:700">
          <i class="fa-solid fa-arrow-left"></i> Cancelar y Volver
        </a>
        <button type="submit" class="btn btn-success" style="padding:10px 24px; font-weight:800; font-size:0.95rem; background:#10b981; border:none; display:flex; align-items:center; gap:8px">
          <i class="fa-solid fa-circle-check"></i> Confirmar Apertura de Caja
        </button>
      </div>
    </form>
  </div>
</div>
@endsection
