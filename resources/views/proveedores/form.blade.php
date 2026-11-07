@extends('layouts.main')
@section('title', $proveedor ? 'Editar Proveedor: ' . $proveedor->nombre : 'Nuevo Proveedor')
@section('content')

<div class="page-header" style="margin-bottom: 20px">
  <div>
    <h1 style="color: #38bdf8; font-size: 1.6rem; font-weight: 800; margin:0">
      {!! $proveedor ? '<i class="fa-solid fa-pen"></i> Editar Proveedor' : '<i class="fa-solid fa-plus"></i> Registrar Nuevo Proveedor' !!}
    </h1>
    <p class="page-sub" style="color: #94a3b8; margin-top:4px">
      {{ $proveedor ? 'Actualización de ficha técnica, contactos y términos comerciales' : 'Registro de nuevo proveedor con información fiscal y comercial completa' }}
    </p>
  </div>
  <div>
    <a href="{{ route('proveedores.index') }}" class="btn btn-secondary" style="font-weight:700">
      <i class="fa-solid fa-arrow-left"></i> Volver a Proveedores
    </a>
  </div>
</div>

<div class="card" style="border-radius:10px; max-width:960px; margin:0 auto 30px">
  <div class="card-header" style="padding:14px 22px; border-bottom:1px solid #282d3c">
    <h3 style="margin:0; font-size:1.1rem; font-weight:800; display:flex; align-items:center; gap:8px">
      <i class="fa-solid fa-truck" style="color:#38bdf8"></i> Ficha Integral de Proveedor {{ $proveedor ? '(#'.$proveedor->id.')' : '' }}
    </h3>
  </div>
  <div class="card-body" style="padding:26px">
    <form action="{{ $proveedor ? route('proveedores.actualizar', $proveedor->id) : route('proveedores.guardar') }}" method="POST">
      @csrf
      @if($proveedor)
        @method('PUT')
      @endif

      <h4 style="color:#38bdf8; font-size:0.95rem; font-weight:800; margin-bottom:14px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:6px">
        1. Identificación y Razón Social
      </h4>

      <div class="form-row">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Nombre de la Empresa <span class="req" style="color:#ef4444">*</span></label>
          <input type="text" name="nombre" class="form-control" required
                 value="{{ old('nombre', $proveedor->nombre ?? '') }}" placeholder="Ej: Distribuidora Central S.A. de C.V.">
          @error('nombre')<span class="text-danger" style="font-size:0.8rem">{{ $message }}</span>@enderror
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Razón Social / Nombre Comercial</label>
          <input type="text" name="razon_social" class="form-control"
                 value="{{ old('razon_social', $proveedor->razon_social ?? '') }}" placeholder="Ej: DisCentral El Salvador">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">NIT</label>
          <input type="text" name="nit" class="form-control"
                 value="{{ old('nit', $proveedor->nit ?? '') }}" placeholder="0000-000000-000-0">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">NRC (Registro de Contribuyente)</label>
          <input type="text" name="nrc" class="form-control"
                 value="{{ old('nrc', $proveedor->nrc ?? '') }}" placeholder="Ej: 123456-7">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Giro o Actividad Económica</label>
          <input type="text" name="giro" class="form-control"
                 value="{{ old('giro', $proveedor->giro ?? '') }}" placeholder="Ej: Venta al por mayor de tecnología">
        </div>
      </div>

      <h4 style="color:#38bdf8; font-size:0.95rem; font-weight:800; margin:22px 0 14px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:6px">
        2. Contacto Directo y Representante
      </h4>

      <div class="form-row">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Persona de Contacto</label>
          <input type="text" name="contacto" class="form-control"
                 value="{{ old('contacto', $proveedor->contacto ?? '') }}" placeholder="Ej: Lic. Roberto Gómez">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Cargo del Contacto</label>
          <input type="text" name="cargo_contacto" class="form-control"
                 value="{{ old('cargo_contacto', $proveedor->cargo_contacto ?? '') }}" placeholder="Ej: Ejecutivo de Ventas / Gerente">
        </div>
      </div>

      <div class="form-row">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Teléfono Principal</label>
          <input type="text" name="telefono" class="form-control"
                 value="{{ old('telefono', $proveedor->telefono ?? '') }}" placeholder="Ej: 2222-3333">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Teléfono Secundario / WhatsApp</label>
          <input type="text" name="telefono_secundario" class="form-control"
                 value="{{ old('telefono_secundario', $proveedor->telefono_secundario ?? '') }}" placeholder="Ej: 7777-8888">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Correo Electrónico</label>
          <input type="email" name="email" class="form-control"
                 value="{{ old('email', $proveedor->email ?? '') }}" placeholder="ventas@proveedor.com">
        </div>
      </div>

      <h4 style="color:#38bdf8; font-size:0.95rem; font-weight:800; margin:22px 0 14px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:6px">
        3. Ubicación y Dirección Fiscal
      </h4>

      <div class="form-row">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Departamento</label>
          <input type="text" name="departamento" class="form-control"
                 value="{{ old('departamento', $proveedor->departamento ?? 'San Salvador') }}">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Municipio</label>
          <input type="text" name="municipio" class="form-control"
                 value="{{ old('municipio', $proveedor->municipio ?? 'San Salvador') }}">
        </div>
        <div class="form-group" style="flex:2">
          <label style="font-weight:700">Dirección Exacta / Bodega</label>
          <input type="text" name="direccion" class="form-control"
                 value="{{ old('direccion', $proveedor->direccion ?? '') }}" placeholder="Calle, número de local, colonia...">
        </div>
      </div>

      <h4 style="color:#38bdf8; font-size:0.95rem; font-weight:800; margin:22px 0 14px; border-bottom:1px solid rgba(255,255,255,0.1); padding-bottom:6px">
        4. Condiciones Comerciales y Datos Bancarios
      </h4>

      <div class="form-row">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Plazo de Crédito (Días)</label>
          <input type="number" name="dias_credito" class="form-control" min="0"
                 value="{{ old('dias_credito', $proveedor->dias_credito ?? '30') }}" placeholder="30">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Límite de Crédito Autorizado ($)</label>
          <input type="number" step="0.01" min="0" name="limite_credito" class="form-control"
                 value="{{ old('limite_credito', $proveedor->limite_credito ?? '0.00') }}" placeholder="0.00">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Banco para Pagos</label>
          <input type="text" name="banco_nombre" class="form-control"
                 value="{{ old('banco_nombre', $proveedor->banco_nombre ?? '') }}" placeholder="Ej: Banco Agrícola, BAC...">
        </div>
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Número de Cuenta Bancaria</label>
          <input type="text" name="numero_cuenta" class="form-control"
                 value="{{ old('numero_cuenta', $proveedor->numero_cuenta ?? '') }}" placeholder="Ej: 001-234567-8">
        </div>
      </div>

      <div class="form-row" style="margin-top:14px">
        <div class="form-group" style="flex:1">
          <label style="font-weight:700">Sitio Web / Catálogo en Línea</label>
          <input type="text" name="sitio_web" class="form-control"
                 value="{{ old('sitio_web', $proveedor->sitio_web ?? '') }}" placeholder="https://www.proveedor.com">
        </div>
      </div>

      <div class="form-row" style="margin:14px 0 18px">
        <div class="form-group" style="flex:1">
          <label style="display:flex; align-items:center; gap:8px; cursor:pointer">
            <input type="checkbox" name="aplica_percepcion" value="1" {{ old('aplica_percepcion', $proveedor->aplica_percepcion ?? 0) ? 'checked' : '' }}>
            <span>Aplica Percepción (Gran Contribuyente 1%)</span>
          </label>
        </div>
        <div class="form-group" style="flex:1">
          <label style="display:flex; align-items:center; gap:8px; cursor:pointer">
            <input type="checkbox" name="aplica_retencion_renta" value="1" {{ old('aplica_retencion_renta', $proveedor->aplica_retencion_renta ?? 0) ? 'checked' : '' }}>
            <span>Aplica Retención de Renta</span>
          </label>
        </div>
        <div class="form-group" style="flex:1">
          <label style="display:flex; align-items:center; gap:8px; cursor:pointer">
            <input type="checkbox" name="activo" value="1" {{ old('activo', $proveedor->activo ?? 1) ? 'checked' : '' }}>
            <span style="font-weight:700">Proveedor Activo</span>
          </label>
        </div>
      </div>

      <div class="form-group" style="margin-bottom:24px">
        <label style="font-weight:700">Notas / Observaciones Generales</label>
        <textarea name="notas" class="form-control" rows="2" placeholder="Observaciones de entregas, fletes, descuentos por pronto pago...">{{ old('notas', $proveedor->notas ?? '') }}</textarea>
      </div>

      <div style="display:flex; justify-content:space-between; align-items:center; border-top:1px solid #282d3c; padding-top:20px">
        <a href="{{ route('proveedores.index') }}" class="btn btn-secondary" style="padding:10px 20px; font-weight:700">
          <i class="fa-solid fa-xmark"></i> Cancelar
        </a>
        <button type="submit" class="btn btn-primary" style="padding:10px 26px; font-weight:800; font-size:0.95rem">
          <i class="fa-solid fa-floppy-disk"></i> {{ $proveedor ? 'Guardar Cambios' : 'Registrar Proveedor' }}
        </button>
      </div>
    </form>
  </div>
</div>
@endsection