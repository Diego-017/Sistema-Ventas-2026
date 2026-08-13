@extends('layouts.main')
@section('title','Proveedores')
@section('content')

<div class="page-header">
  <h1>🏭 Proveedores</h1>
  <button onclick="abrirModal()" class="btn btn-primary">+ Nuevo Proveedor</button>
</div>

<div class="card">
  <div class="card-body p-0">
    <div class="table-toolbar">
      <input type="search" id="searchTable" placeholder="Buscar proveedor..." class="form-control" style="max-width:260px">
    </div>
    <table class="table" id="provTable">
      <thead><tr><th>Nombre</th><th>NIT/DUI</th><th>Teléfono</th><th>Email</th><th>Dirección</th><th>Acciones</th></tr></thead>
      <tbody>
        @forelse($proveedores as $p)
        <tr>
          <td>
            <strong>{{ $p->nombre }}</strong>
            @if($p->aplica_percepcion)<span class="badge badge-info" style="font-size:.6rem">PERCEPCIÓN</span>@endif
            @if($p->aplica_retencion_renta)<span class="badge badge-warning" style="font-size:.6rem">RET. RENTA</span>@endif
          </td>
          <td>{{ $p->nit ?? $p->dui ?? '—' }}</td>
          <td>{{ $p->telefono ?? '—' }}</td>
          <td>{{ $p->email ?? '—' }}</td>
          <td>{{ $p->direccion ?? '—' }}</td>
          <td class="actions">
            <button onclick='editar(@json($p))' class="btn btn-sm btn-secondary">✏️</button>
            <button onclick="eliminar({{ $p->id }},this)" class="btn btn-sm btn-danger">🗑️</button>
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted" style="padding:2rem">Sin proveedores registrados.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div id="provModal" class="modal-overlay" style="display:none" onclick="if(event.target===this)cerrarModal()">
  <div class="modal" style="max-width:640px">
    <div class="modal-header"><h3 id="modalTitle">Nuevo Proveedor</h3><button onclick="cerrarModal()">✕</button></div>
    <div class="modal-body">
      <input type="hidden" id="provId">
      <div class="form-row">
        <div class="form-group"><label>Nombre <span class="req">*</span></label><input id="p_nombre" class="form-control"></div>
        <div class="form-group"><label>Razón Social</label><input id="p_razon_social" class="form-control"></div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Documento</label>
          <select id="p_tipo_documento" class="form-control">
            <option value="nit">NIT</option>
            <option value="dui">DUI</option>
          </select>
        </div>
        <div class="form-group"><label>NIT</label><input id="p_nit" class="form-control" placeholder="0000-000000-000-0"></div>
        <div class="form-group"><label>NRC</label><input id="p_nrc" class="form-control"></div>
        <div class="form-group"><label>DUI</label><input id="p_dui" class="form-control" placeholder="00000000-0"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Contacto</label><input id="p_contacto" class="form-control"></div>
        <div class="form-group"><label>Teléfono</label><input id="p_telefono" class="form-control"></div>
        <div class="form-group"><label>Giro</label><input id="p_giro" class="form-control"></div>
      </div>
      <div class="form-group"><label>Email</label><input id="p_email" type="email" class="form-control"></div>
      <div class="form-group"><label>Dirección</label><input id="p_dir" class="form-control"></div>
      <div class="form-row">
        <div class="form-group"><label>Departamento</label><input id="p_departamento" class="form-control"></div>
        <div class="form-group"><label>Municipio</label><input id="p_municipio" class="form-control"></div>
      </div>
      <div class="form-row" style="flex-wrap:wrap;gap:1rem 2rem">
        <label style="display:flex;align-items:center;gap:6px;margin:0"><input type="checkbox" id="p_percepcion" style="width:auto"> Aplica Percepción (1%) al vendernos</label>
        <label style="display:flex;align-items:center;gap:6px;margin:0"><input type="checkbox" id="p_ret_renta" style="width:auto"> Le retenemos Renta</label>
      </div>
      <div id="provError" class="alert alert-error" style="display:none"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
      <button class="btn btn-primary" onclick="guardar()">💾 Guardar</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name=csrf-token]').content;
const fields = ['nombre','razon_social','tipo_documento','nit','nrc','dui','contacto','telefono','giro','email','dir','departamento','municipio'];

function abrirModal() {
  document.getElementById('provId').value='';
  fields.forEach(f => { const el=document.getElementById('p_'+f); if(el) el.value=''; });
  document.getElementById('p_tipo_documento').value='nit';
  document.getElementById('p_percepcion').checked=false;
  document.getElementById('p_ret_renta').checked=false;
  document.getElementById('modalTitle').textContent='Nuevo Proveedor';
  document.getElementById('provError').style.display='none';
  document.getElementById('provModal').style.display='flex';
}
function editar(p) {
  document.getElementById('provId').value=p.id;
  document.getElementById('p_nombre').value=p.nombre || '';
  document.getElementById('p_razon_social').value=p.razon_social || '';
  document.getElementById('p_tipo_documento').value=p.tipo_documento || 'nit';
  document.getElementById('p_nit').value=p.nit || '';
  document.getElementById('p_nrc').value=p.nrc || '';
  document.getElementById('p_dui').value=p.dui || '';
  document.getElementById('p_contacto').value=p.contacto || '';
  document.getElementById('p_telefono').value=p.telefono || '';
  document.getElementById('p_giro').value=p.giro || '';
  document.getElementById('p_email').value=p.email || '';
  document.getElementById('p_dir').value=p.direccion || '';
  document.getElementById('p_departamento').value=p.departamento || '';
  document.getElementById('p_municipio').value=p.municipio || '';
  document.getElementById('p_percepcion').checked = !!p.aplica_percepcion;
  document.getElementById('p_ret_renta').checked  = !!p.aplica_retencion_renta;
  document.getElementById('modalTitle').textContent='Editar Proveedor';
  document.getElementById('provError').style.display='none';
  document.getElementById('provModal').style.display='flex';
}
function cerrarModal(){ document.getElementById('provModal').style.display='none'; }

async function guardar() {
  const nombre = document.getElementById('p_nombre').value.trim();
  if (!nombre) {
    document.getElementById('provError').textContent='El nombre es obligatorio.';
    document.getElementById('provError').style.display='flex'; return;
  }
  const id = document.getElementById('provId').value;
  const url = id ? `/proveedores/${id}` : '{{ route("proveedores.guardar") }}';
  const body = new URLSearchParams({
    nombre,
    razon_social: document.getElementById('p_razon_social').value,
    tipo_documento: document.getElementById('p_tipo_documento').value,
    nit: document.getElementById('p_nit').value,
    nrc: document.getElementById('p_nrc').value,
    dui: document.getElementById('p_dui').value,
    contacto:document.getElementById('p_contacto').value,
    telefono:document.getElementById('p_telefono').value,
    giro: document.getElementById('p_giro').value,
    email:document.getElementById('p_email').value,
    direccion:document.getElementById('p_dir').value,
    departamento: document.getElementById('p_departamento').value,
    municipio: document.getElementById('p_municipio').value,
    aplica_percepcion: document.getElementById('p_percepcion').checked ? 1 : 0,
    aplica_retencion_renta: document.getElementById('p_ret_renta').checked ? 1 : 0,
    _token:CSRF
  });
  if(id) body.append('_method','PUT');
  const r = await fetch(url,{method:'POST',body,headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}});
  const d = await r.json();
  if(d.ok) location.reload();
  else {
    document.getElementById('provError').textContent = d.error||d.message||'Error.';
    document.getElementById('provError').style.display='flex';
  }
}
async function eliminar(id,btn) {
  if(!confirm('¿Eliminar proveedor?')) return;
  btn.disabled=true;
  const r = await fetch(`/proveedores/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}});
  const d = await r.json();
  if(d.ok) btn.closest('tr').remove();
  else { alert(d.error||'Error.'); btn.disabled=false; }
}
document.getElementById('searchTable').addEventListener('input',function(){
  const q=this.value.toLowerCase();
  document.querySelectorAll('#provTable tbody tr').forEach(r=>{
    r.style.display=r.textContent.toLowerCase().includes(q)?'':'none';
  });
});
</script>
@endpush
@endsection
