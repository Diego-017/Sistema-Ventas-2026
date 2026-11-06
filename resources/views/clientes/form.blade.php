@extends('layouts.main')
@section('title', $cliente ? 'Editar Cliente: ' . $cliente->nombre : 'Nuevo Cliente')
@section('content')

<div class="page-header" style="margin-bottom: 20px">
  <div>
    <h1 style="color: #38bdf8; font-size: 1.6rem; font-weight: 800; margin:0">
      {{ $cliente ? '<i class="fa-solid fa-pen"></i> Editar Cliente Avanzado' : '<i class="fa-solid fa-plus"></i> Registrar Nuevo Cliente' }}
    </h1>
    <p class="page-sub" style="color: #94a3b8; margin-top:4px">
      {{ $cliente ? 'Modifica la ficha técnica y fiscal del cliente seleccionado' : 'Ingresa la información general, comercial y tributaria del cliente' }}
    </p>
  </div>
  <div>
    <a href="{{ route('clientes.index') }}" class="btn btn-light" style="font-weight:700">← Volver al Listado</a>
  </div>
</div>

<div class="card" style="border-radius:10px; max-width:1100px; margin:0 auto 30px">
  <div class="card-header" style="padding:14px 22px; border-bottom:1px solid #282d3c">
    <h3 style="margin:0; font-size:1.1rem; font-weight:800">
      Ficha de Datos del Cliente {{ $cliente ? '(#'.$cliente->id.')' : '' }}
    </h3>
  </div>
  <div class="card-body" style="padding:24px">
    <form action="{{ $cliente ? route('clientes.actualizar', $cliente->id) : route('clientes.guardar') }}" method="POST">
      @csrf
      @if($cliente)
        @method('PUT')
      @endif

      {{-- Sección 1: Información Básica --}}
      <h4 style="font-size:0.95rem; font-weight:800; color:#38bdf8; margin:0 0 14px; text-transform:uppercase; letter-spacing:0.5px">
        1. Información General y de Contacto
      </h4>
      <div class="form-row" style="margin-bottom:14px">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Nombre Completo / Razón Social <span class="req" style="color:#ef4444">*</span></label>
          <input type="text" name="nombre" class="form-control" required
                 value="{{ old('nombre', $cliente->nombre ?? '') }}" placeholder="Ej: Juan Carlos Pérez">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Nombre Comercial</label>
          <input type="text" name="nombre_comercial" class="form-control"
                 value="{{ old('nombre_comercial', $cliente->nombre_comercial ?? '') }}" placeholder="Ej: Distribuidora El Sol">
        </div>
      </div>

      <div class="form-row" style="margin-bottom:14px">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Correo Electrónico</label>
          <input type="email" name="email" class="form-control"
                 value="{{ old('email', $cliente->email ?? '') }}" placeholder="cliente@correo.com">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Teléfono / WhatsApp</label>
          <input type="text" name="telefono" class="form-control"
                 value="{{ old('telefono', $cliente->telefono ?? '') }}" placeholder="Ej: 7123-4567">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Giro / Actividad Económica</label>
          <input type="text" name="giro" class="form-control"
                 value="{{ old('giro', $cliente->giro ?? '') }}" placeholder="Ej: Venta de abarrotes al por mayor">
        </div>
      </div>

      <div class="form-group" style="margin-bottom:20px">
        <label style="font-weight:700">Dirección Completa</label>
        <input type="text" name="direccion" class="form-control"
               value="{{ old('direccion', $cliente->direccion ?? '') }}" placeholder="Calle, Barrio, Avenida, Edificio #...">
      </div>

      <div class="form-row" style="margin-bottom:24px">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Departamento</label>
          <input type="text" name="departamento" class="form-control"
                 value="{{ old('departamento', $cliente->departamento ?? 'San Salvador') }}" placeholder="Ej: San Salvador">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Municipio</label>
          <input type="text" name="municipio" class="form-control"
                 value="{{ old('municipio', $cliente->municipio ?? 'San Salvador Centro') }}" placeholder="Ej: San Salvador">
        </div>
      </div>

      {{-- Sección 2: Información Fiscal / DTE --}}
      <h4 style="font-size:0.95rem; font-weight:800; color:#38bdf8; margin:0 0 14px; text-transform:uppercase; letter-spacing:0.5px">
        2. Datos Fiscales y Documentos Tributarios
      </h4>

      <div class="form-row" style="margin-bottom:14px">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Tipo de Documento Principal</label>
          <select name="tipo_documento" class="form-control">
            @php $tDoc = old('tipo_documento', $cliente->tipo_documento ?? 'dui'); @endphp
            <option value="dui" {{ $tDoc === 'dui' ? 'selected' : '' }}>DUI (Documento Único de Identidad)</option>
            <option value="nit" {{ $tDoc === 'nit' ? 'selected' : '' }}>NIT (Número de Identificación Tributaria)</option>
            <option value="pasaporte" {{ $tDoc === 'pasaporte' ? 'selected' : '' }}>Pasaporte</option>
            <option value="carnet_residente" {{ $tDoc === 'carnet_residente' ? 'selected' : '' }}>Carnet de Residente</option>
            <option value="otro" {{ $tDoc === 'otro' ? 'selected' : '' }}>Otro Documento</option>
          </select>
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Número de DUI</label>
          <input type="text" name="dui" class="form-control"
                 value="{{ old('dui', $cliente->dui ?? '') }}" placeholder="00000000-0">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Número de NIT</label>
          <input type="text" name="nit" class="form-control"
                 value="{{ old('nit', $cliente->nit ?? '') }}" placeholder="0000-000000-000-0">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Número de NRC (Registro de Contribuyente)</label>
          <input type="text" name="nrc" class="form-control"
                 value="{{ old('nrc', $cliente->nrc ?? '') }}" placeholder="000000-0">
        </div>
      </div>

      {{-- Sección 3: Condiciones de Pago y Crédito --}}
      <h4 style="font-size:0.95rem; font-weight:800; color:#38bdf8; margin:20px 0 14px; text-transform:uppercase; letter-spacing:0.5px">
        3. Condiciones Comerciales y Crédito
      </h4>

      <div class="form-row" style="margin-bottom:16px">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Tipo de Cliente</label>
          <select name="tipo" class="form-control">
            @php $tCli = old('tipo', $cliente->tipo ?? 'contado'); @endphp
            <option value="contado" {{ $tCli === 'contado' ? 'selected' : '' }}><i class="fa-solid fa-money-bill-wave"></i> Contado</option>
            <option value="credito" {{ $tCli === 'credito' ? 'selected' : '' }}><i class="fa-solid fa-credit-card"></i> Crédito Autorizado</option>
          </select>
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Límite de Crédito ($)</label>
          <input type="number" step="0.01" name="limite_credito" class="form-control"
                 value="{{ old('limite_credito', $cliente->limite_credito ?? 0) }}" min="0">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Días de Crédito (Plazo)</label>
          <input type="number" name="dias_credito" class="form-control"
                 value="{{ old('dias_credito', $cliente->dias_credito ?? 30) }}" min="0">
        </div>
      </div>

      <div class="form-row" style="background:rgba(0,0,0,0.04); padding:14px; border-radius:8px; border:1px solid #334155; margin-bottom:24px">
        <label style="display:flex; align-items:center; gap:8px; margin:0; cursor:pointer; font-weight:700">
          <input type="checkbox" name="retiene_iva" value="1" {{ old('retiene_iva', $cliente->retiene_iva ?? false) ? 'checked' : '' }}>
          Retiene IVA (1% Gran Contribuyente)
        </label>
        <label style="display:flex; align-items:center; gap:8px; margin:0; cursor:pointer; font-weight:700">
          <input type="checkbox" name="retiene_renta" value="1" {{ old('retiene_renta', $cliente->retiene_renta ?? false) ? 'checked' : '' }}>
          Retiene Renta
        </label>
        <label style="display:flex; align-items:center; gap:8px; margin:0; cursor:pointer; font-weight:700">
          <input type="checkbox" name="aplica_percepcion" value="1" {{ old('aplica_percepcion', $cliente->aplica_percepcion ?? false) ? 'checked' : '' }}>
          Aplica Percepción (1%)
        </label>
        <label style="display:flex; align-items:center; gap:8px; margin:0; cursor:pointer; font-weight:700">
          <input type="checkbox" name="comisiona" value="1" {{ old('comisiona', $cliente->comisiona ?? false) ? 'checked' : '' }}>
          Genera Comisión a Vendedor
        </label>
      </div>

      {{-- Botones de Acción --}}
      <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #282d3c; padding-top:20px">
        <a href="{{ route('clientes.index') }}" class="btn btn-light" style="padding:10px 20px; font-weight:700">
          <i class="fa-solid fa-xmark"></i> Cancelar
        </a>
        <button type="submit" class="btn btn-primary" style="padding:10px 24px; font-weight:800; font-size:0.95rem">
          <i class="fa-solid fa-floppy-disk"></i> {{ $cliente ? 'Guardar Cambios' : 'Registrar Cliente' }}
        </button>
      </div>
    </form>
  </div>
</div>

@endsection
