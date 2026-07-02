@extends('layouts.main')
@section('title','Configuración')
@section('content')

<div class="page-header">
  <h1>⚙️ Configuración del Negocio</h1>
</div>

<div class="card" style="max-width:720px">
  <div class="card-header"><h3>Datos del Negocio</h3></div>
  <div class="card-body">
    <form method="POST" action="{{ route('configuracion.update') }}">
      @csrf
      <div class="form-row">
        <div class="form-group">
          <label>Nombre del Negocio <span class="req">*</span></label>
          <input type="text" name="nombre_negocio" class="form-control" required
                 value="{{ $config['nombre_negocio'] ?? 'VentasPro' }}">
        </div>
        <div class="form-group">
          <label>Email de Contacto</label>
          <input type="email" name="email_negocio" class="form-control"
                 value="{{ $config['email_negocio'] ?? '' }}" placeholder="contacto@negocio.com">
        </div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Teléfono</label>
          <input type="text" name="telefono" class="form-control"
                 value="{{ $config['telefono'] ?? '' }}" placeholder="2222-3333">
        </div>
        <div class="form-group">
          <label>Dirección</label>
          <input type="text" name="direccion" class="form-control"
                 value="{{ $config['direccion'] ?? '' }}" placeholder="Ciudad, País">
        </div>
      </div>
      <hr style="margin:20px 0;border-color:#e2e8f0">
      <h4 style="margin-bottom:16px;color:#374151">Moneda e Impuestos</h4>
      <div class="form-row">
        <div class="form-group">
          <label>Moneda</label>
          <input type="text" name="moneda" class="form-control" maxlength="10"
                 value="{{ $config['moneda'] ?? 'USD' }}" placeholder="USD">
        </div>
        <div class="form-group">
          <label>Símbolo</label>
          <input type="text" name="simbolo_moneda" class="form-control" maxlength="5"
                 value="{{ $config['simbolo_moneda'] ?? '$' }}" placeholder="$">
        </div>
        <div class="form-group">
          <label>IVA (%)</label>
          <input type="number" name="iva" class="form-control" step="0.01" min="0" max="100"
                 value="{{ $config['iva'] ?? '13' }}">
        </div>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">💾 Guardar Configuración</button>
      </div>
    </form>
  </div>
</div>

<div class="card" style="max-width:720px">
  <div class="card-header"><h3>ℹ️ Información del Sistema</h3></div>
  <div class="card-body">
    <div class="info-grid">
      <div class="info-item"><span>Versión</span><strong>1.0.0</strong></div>
      <div class="info-item"><span>Framework</span><strong>Laravel 11</strong></div>
      <div class="info-item"><span>PHP</span><strong>{{ phpversion() }}</strong></div>
      <div class="info-item"><span>Base de datos</span><strong>MySQL</strong></div>
    </div>
  </div>
</div>
@endsection
