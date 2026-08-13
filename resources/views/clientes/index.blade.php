@extends('layouts.main')
@section('title','Clientes')
@section('content')
<div class="page-header">
  <h1>👥 Administrar Clientes</h1>
  <button onclick="abrirModal()" class="btn btn-primary">+ Nuevo Cliente</button>
</div>
<div class="card">
  <div class="card-body p-0">
    <div class="table-toolbar">
      <input type="search" id="searchTable" placeholder="Buscar cliente..." class="form-control" style="max-width:260px">
    </div>
    <table class="table" id="clientTable">
      <thead><tr><th>Nombre</th><th>NIT/DUI</th><th>Teléfono</th><th>Tipo</th><th>Email</th><th></th></tr></thead>
      <tbody>
        @forelse($clientes as $c)
        <tr>
          <td>
            <a href="{{ route('clientes.show',$c->id) }}"><strong>{{ $c->nombre }}</strong></a>
            @if($c->retiene_iva)<span class="badge badge-warning" style="font-size:.6rem">RET. IVA</span>@endif
            @if($c->aplica_percepcion)<span class="badge badge-info" style="font-size:.6rem">PERCEPCIÓN</span>@endif
          </td>
          <td>{{ $c->nit ?? $c->dui ?? '—' }}</td>
          <td>{{ $c->telefono ?? '—' }}</td>
          <td>
            <span class="badge badge-{{ $c->tipo==='credito'?'warning':'success' }}">
              {{ $c->tipo === 'credito' ? '💳 Crédito' : '💵 Contado' }}
            </span>
          </td>
          <td>{{ $c->email ?? '—' }}</td>
          <td class="actions">
            <a href="{{ route('clientes.show',$c->id) }}" class="btn btn-sm btn-light">👁️</a>
            <button onclick='editarCliente(@json($c))' class="btn btn-sm btn-light">✏️</button>
            @if(session('user.rol')==='admin')
            <button onclick="eliminar({{ $c->id }},this)" class="btn btn-sm btn-danger">🗑️</button>
            @endif
          </td>
        </tr>
        @empty
        <tr><td colspan="6" class="text-center text-muted" style="padding:2rem">Sin clientes.</td></tr>
        @endforelse
      </tbody>
    </table>
    <div class="table-footer">{{ $clientes->links() }}</div>
  </div>
</div>

<div id="clientModal" class="modal-overlay" style="display:none" onclick="if(event.target===this)cerrarModal()">
  <div class="modal" style="max-width:640px">
    <div class="modal-header"><h3 id="modalTitle">Nuevo Cliente</h3><button onclick="cerrarModal()">✕</button></div>
    <div class="modal-body">
      <input type="hidden" id="clienteId">
      <div class="form-row">
        <div class="form-group"><label>Nombre <span class="req">*</span></label><input id="c_nombre" class="form-control"></div>
        <div class="form-group"><label>Nombre Comercial</label><input id="c_nombre_comercial" class="form-control"></div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Documento</label>
          <select id="c_tipo_documento" class="form-control">
            <option value="dui">DUI</option>
            <option value="nit">NIT</option>
            <option value="pasaporte">Pasaporte</option>
            <option value="carnet_residente">Carnet Residente</option>
            <option value="otro">Otro</option>
          </select>
        </div>
        <div class="form-group"><label>DUI</label><input id="c_dui" class="form-control" placeholder="00000000-0"></div>
        <div class="form-group"><label>NIT</label><input id="c_nit" class="form-control" placeholder="0000-000000-000-0"></div>
        <div class="form-group"><label>NRC</label><input id="c_nrc" class="form-control"></div>
      </div>
      <div class="form-row">
        <div class="form-group"><label>Teléfono</label><input id="c_tel" class="form-control"></div>
        <div class="form-group"><label>Email</label><input id="c_email" type="email" class="form-control"></div>
        <div class="form-group"><label>Giro / Actividad</label><input id="c_giro" class="form-control"></div>
      </div>
      <div class="form-group"><label>Dirección</label><input id="c_dir" class="form-control"></div>
      <div class="form-row">
        <div class="form-group"><label>Departamento</label><input id="c_departamento" class="form-control"></div>
        <div class="form-group"><label>Municipio</label><input id="c_municipio" class="form-control"></div>
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Tipo de Cliente</label>
          <select id="c_tipo" class="form-control" onchange="toggleCredito()">
            <option value="contado">💵 Contado</option>
            <option value="credito">💳 Crédito</option>
          </select>
        </div>
        <div class="form-group" id="limiteGroup" style="display:none">
          <label>Límite de Crédito ($)</label>
          <input id="c_limite" type="number" step="0.01" min="0" class="form-control" value="0">
        </div>
        <div class="form-group" id="diasGroup" style="display:none">
          <label>Días de Crédito</label>
          <input id="c_dias" type="number" min="1" class="form-control" value="30">
        </div>
      </div>
      <div class="form-row" style="flex-wrap:wrap;gap:1rem 2rem">
        <label style="display:flex;align-items:center;gap:6px;margin:0"><input type="checkbox" id="c_retiene_iva" style="width:auto"> Retiene IVA (1%)</label>
        <label style="display:flex;align-items:center;gap:6px;margin:0"><input type="checkbox" id="c_retiene_renta" style="width:auto"> Retiene Renta</label>
        <label style="display:flex;align-items:center;gap:6px;margin:0"><input type="checkbox" id="c_percepcion" style="width:auto"> Aplica Percepción (1%)</label>
        <label style="display:flex;align-items:center;gap:6px;margin:0"><input type="checkbox" id="c_comisiona" style="width:auto"> Comisiona a vendedor</label>
      </div>
      <div id="cliError" class="alert alert-error" style="display:none"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
      <button class="btn btn-primary" onclick="guardarCliente()">💾 Guardar</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name=csrf-token]').content;

document.getElementById('searchTable').addEventListener('input', function(){
  const q=this.value.toLowerCase();
  document.querySelectorAll('#clientTable tbody tr').forEach(r=>{r.style.display=r.textContent.toLowerCase().includes(q)?'':'none';});
});

function toggleCredito(){
  const esCredito = document.getElementById('c_tipo').value === 'credito';
  document.getElementById('limiteGroup').style.display = esCredito ? 'block' : 'none';
  document.getElementById('diasGroup').style.display   = esCredito ? 'block' : 'none';
}

const CLI_FIELDS = ['nombre','nombre_comercial','tipo_documento','dui','nit','nrc','tel','email','giro','dir','departamento','municipio'];

function limpiarModal(){
  document.getElementById('clienteId').value='';
  CLI_FIELDS.forEach(f=>{ const el=document.getElementById('c_'+f); if(el) el.value=''; });
  document.getElementById('c_tipo_documento').value='dui';
  document.getElementById('c_tipo').value='contado';
  document.getElementById('c_limite').value='0';
  document.getElementById('c_dias').value='30';
  ['c_retiene_iva','c_retiene_renta','c_percepcion','c_comisiona'].forEach(id=>document.getElementById(id).checked=false);
  toggleCredito();
}

function abrirModal(){
  limpiarModal();
  document.getElementById('modalTitle').textContent='Nuevo Cliente';
  document.getElementById('cliError').style.display='none';
  document.getElementById('clientModal').style.display='flex';
}

function editarCliente(c){
  limpiarModal();
  document.getElementById('clienteId').value = c.id;
  document.getElementById('c_nombre').value = c.nombre || '';
  document.getElementById('c_nombre_comercial').value = c.nombre_comercial || '';
  document.getElementById('c_tipo_documento').value = c.tipo_documento || 'dui';
  document.getElementById('c_dui').value = c.dui || '';
  document.getElementById('c_nit').value = c.nit || '';
  document.getElementById('c_nrc').value = c.nrc || '';
  document.getElementById('c_tel').value = c.telefono || '';
  document.getElementById('c_email').value = c.email || '';
  document.getElementById('c_giro').value = c.giro || '';
  document.getElementById('c_dir').value = c.direccion || '';
  document.getElementById('c_departamento').value = c.departamento || '';
  document.getElementById('c_municipio').value = c.municipio || '';
  document.getElementById('c_tipo').value = c.tipo || 'contado';
  document.getElementById('c_limite').value = c.limite_credito || 0;
  document.getElementById('c_dias').value = c.dias_credito || 30;
  document.getElementById('c_retiene_iva').checked   = !!c.retiene_iva;
  document.getElementById('c_retiene_renta').checked = !!c.retiene_renta;
  document.getElementById('c_percepcion').checked    = !!c.aplica_percepcion;
  document.getElementById('c_comisiona').checked     = !!c.comisiona;
  toggleCredito();
  document.getElementById('modalTitle').textContent='Editar Cliente';
  document.getElementById('cliError').style.display='none';
  document.getElementById('clientModal').style.display='flex';
}
function cerrarModal(){ document.getElementById('clientModal').style.display='none'; }

async function guardarCliente(){
  const nombre=document.getElementById('c_nombre').value.trim();
  if(!nombre){document.getElementById('cliError').textContent='El nombre es obligatorio.';document.getElementById('cliError').style.display='flex';return;}
  const id=document.getElementById('clienteId').value;
  const body=new URLSearchParams({
    nombre,
    nombre_comercial: document.getElementById('c_nombre_comercial').value,
    tipo_documento: document.getElementById('c_tipo_documento').value,
    dui: document.getElementById('c_dui').value,
    nit: document.getElementById('c_nit').value,
    nrc: document.getElementById('c_nrc').value,
    telefono:document.getElementById('c_tel').value,
    email:document.getElementById('c_email').value,
    giro: document.getElementById('c_giro').value,
    direccion:document.getElementById('c_dir').value,
    departamento: document.getElementById('c_departamento').value,
    municipio: document.getElementById('c_municipio').value,
    tipo:document.getElementById('c_tipo').value,
    limite_credito:document.getElementById('c_limite').value || 0,
    dias_credito: document.getElementById('c_dias').value || 30,
    retiene_iva: document.getElementById('c_retiene_iva').checked ? 1 : 0,
    retiene_renta: document.getElementById('c_retiene_renta').checked ? 1 : 0,
    aplica_percepcion: document.getElementById('c_percepcion').checked ? 1 : 0,
    comisiona: document.getElementById('c_comisiona').checked ? 1 : 0,
    _token:CSRF
  });
  const url=id?`/clientes/${id}`:'{{ route("clientes.guardar") }}';
  if(id) body.append('_method','PUT');
  const r=await fetch(url,{method:'POST',body,headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}});
  const d=await r.json();
  if(d.ok) location.reload();
  else{document.getElementById('cliError').textContent=d.message||'Error.';document.getElementById('cliError').style.display='flex';}
}
async function eliminar(id,btn){
  if(!confirm('¿Eliminar cliente?')) return;
  btn.disabled=true;
  const r=await fetch(`/clientes/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}});
  const d=await r.json();
  if(d.ok) btn.closest('tr').remove();
  else{alert('Error.');btn.disabled=false;}
}
</script>
@endpush
@endsection
