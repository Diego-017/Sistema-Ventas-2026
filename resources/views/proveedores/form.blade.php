@extends('layouts.main')
@section('title', $proveedor ? 'Editar Proveedor: ' . $proveedor->nombre : 'Nuevo Proveedor')
@section('content')

<div class="page-header" style="margin-bottom: 20px">
  <div>
    <h1 style="color: #38bdf8; font-size: 1.6rem; font-weight: 800; margin:0">
      {{ $proveedor ? '<i class="fa-solid fa-pen"></i> Editar Proveedor' : '<i class="fa-solid fa-plus"></i> Registrar Nuevo Proveedor' }}
    </h1>
    <p class="page-sub" style="color: #94a3b8; margin-top:4px">
      {{ $proveedor ? 'Actualiza la ficha técnica y comercial del proveedor' : 'Ingresa la información comercial y tributaria del proveedor' }}
    </p>
  </div>
  <div>
    <a href="{{ route('proveedores.index') }}" class="btn btn-light" style="font-weight:700">← Volver al Listado</a>
  </div>
</div>

<div class="card" style="border-radius:10px; max-width:1050px; margin:0 auto 30px">
  <div class="card-header" style="padding:14px 22px; border-bottom:1px solid #282d3c">
    <h3 style="margin:0; font-size:1.1rem; font-weight:800">
      Ficha Técnica del Proveedor {{ $proveedor ? '(#'.$proveedor->id.')' : '' }}
    </h3>
  </div>
  <div class="card-body" style="padding:24px">
    <form action="{{ $proveedor ? route('proveedores.actualizar', $proveedor->id) : route('proveedores.guardar') }}" method="POST">
      @csrf
      @if($proveedor)
        @method('PUT')
      @endif

      {{-- Sección 1: Datos Comerciales --}}
      <h4 style="font-size:0.95rem; font-weight:800; color:#38bdf8; margin:0 0 14px; text-transform:uppercase; letter-spacing:0.5px">
        1. Información General y de Contacto
      </h4>

      <div class="form-row" style="margin-bottom:14px">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Nombre / Razón Social <span class="req" style="color:#ef4444">*</span></label>
          <input type="text" name="nombre" class="form-control" required
                 value="{{ old('nombre', $proveedor->nombre ?? '') }}" placeholder="Ej: DISMA S.A. DE C.V.">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Nombre Comercial</label>
          <input type="text" name="razon_social" class="form-control"
                 value="{{ old('razon_social', $proveedor->razon_social ?? '') }}" placeholder="Ej: Distribuidora Morazán">
        </div>
      </div>

      <div class="form-row" style="margin-bottom:14px">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Persona de Contacto</label>
          <input type="text" name="contacto" class="form-control"
                 value="{{ old('contacto', $proveedor->contacto ?? '') }}" placeholder="Ej: Lic. Roberto Gómez">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Teléfono</label>
          <input type="text" name="telefono" class="form-control"
                 value="{{ old('telefono', $proveedor->telefono ?? '') }}" placeholder="Ej: 2222-0000">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Correo Electrónico</label>
          <input type="email" name="email" class="form-control"
                 value="{{ old('email', $proveedor->email ?? '') }}" placeholder="ventas@proveedor.com">
        </div>
      </div>

      <div class="form-group" style="margin-bottom:20px">
        <label style="font-weight:700">Dirección</label>
        <input type="text" name="direccion" class="form-control"
               value="{{ old('direccion', $proveedor->direccion ?? '') }}" placeholder="Carretera, Calle, Bodega...">
      </div>

      <div class="form-row" style="margin-bottom:24px">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Departamento</label>
          <input type="text" name="departamento" class="form-control"
                 value="{{ old('departamento', $proveedor->departamento ?? 'San Salvador') }}">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Municipio</label>
          <input type="text" name="municipio" class="form-control"
                 value="{{ old('municipio', $proveedor->municipio ?? 'San Salvador Centro') }}">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Giro Comercial</label>
          <input type="text" name="giro" class="form-control"
                 value="{{ old('giro', $proveedor->giro ?? '') }}" placeholder="Ej: Distribución de bebidas">
        </div>
      </div>

      {{-- Sección 2: Datos Tributarios --}}
      <h4 style="font-size:0.95rem; font-weight:800; color:#38bdf8; margin:0 0 14px; text-transform:uppercase; letter-spacing:0.5px">
        2. Información Fiscal y Retenciones
      </h4>

      <div class="form-row" style="margin-bottom:18px">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Tipo de Documento</label>
          <select name="tipo_documento" class="form-control">
            @php $tDoc = old('tipo_documento', $proveedor->tipo_documento ?? 'nit'); @endphp
            <option value="nit" {{ $tDoc === 'nit' ? 'selected' : '' }}>NIT</option>
            <option value="nrc" {{ $tDoc === 'nrc' ? 'selected' : '' }}>NRC</option>
            <option value="dui" {{ $tDoc === 'dui' ? 'selected' : '' }}>DUI</option>
          </select>
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">NIT</label>
          <input type="text" name="nit" class="form-control"
                 value="{{ old('nit', $proveedor->nit ?? '') }}" placeholder="0000-000000-000-0">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">NRC</label>
          <input type="text" name="nrc" class="form-control"
                 value="{{ old('nrc', $proveedor->nrc ?? '') }}" placeholder="000000-0">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">DUI</label>
          <input type="text" name="dui" class="form-control"
                 value="{{ old('dui', $proveedor->dui ?? '') }}" placeholder="00000000-0">
        </div>
      </div>

      <div class="form-row" style="background:rgba(0,0,0,0.04); padding:14px; border-radius:8px; border:1px solid #334155; margin-bottom:24px">
        <label style="display:flex; align-items:center; gap:8px; margin:0; cursor:pointer; font-weight:700">
          <input type="checkbox" name="aplica_percepcion" value="1" {{ old('aplica_percepcion', $proveedor->aplica_percepcion ?? false) ? 'checked' : '' }}>
          Aplica Percepción de IVA (1%)
        </label>
        <label style="display:flex; align-items:center; gap:8px; margin:0; cursor:pointer; font-weight:700">
          <input type="checkbox" name="aplica_retencion_renta" value="1" {{ old('aplica_retencion_renta', $proveedor->aplica_retencion_renta ?? false) ? 'checked' : '' }}>
          Aplica Retención de Renta
        </label>
      </div>

      {{-- Botones de Acción --}}
      <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #282d3c; padding-top:20px">
        <a href="{{ route('proveedores.index') }}" class="btn btn-light" style="padding:10px 20px; font-weight:700">
          <i class="fa-solid fa-xmark"></i> Cancelar
        </a>
        <button type="submit" class="btn btn-primary" style="padding:10px 24px; font-weight:800; font-size:0.95rem">
          <i class="fa-solid fa-floppy-disk"></i> {{ $proveedor ? 'Guardar Cambios' : 'Registrar Proveedor' }}
        </button>
      </div>
    </form>
  </div>
</div>

@endsection
