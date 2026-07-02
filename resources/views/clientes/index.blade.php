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
      <thead><tr><th>Nombre</th><th>NIT</th><th>Teléfono</th><th>Tipo</th><th>Email</th><th></th></tr></thead>
      <tbody>
        @forelse($clientes as $c)
        <tr>
          <td><a href="{{ route('clientes.show',$c->id) }}"><strong>{{ $c->nombre }}</strong></a></td>
          <td>{{ $c->nit ?? '—' }}</td>
          <td>{{ $c->telefono ?? '—' }}</td>
          <td>
            <span class="badge badge-{{ $c->tipo==='credito'?'warning':'success' }}">
              {{ $c->tipo === 'credito' ? '💳 Crédito' : '💵 Contado' }}
            </span>
          </td>
          <td>{{ $c->email ?? '—' }}</td>
          <td class="actions">
            <a href="{{ route('clientes.show',$c->id) }}" class="btn btn-sm btn-light">👁️</a>
            <button onclick="editarCliente({{ $c->id }},'{{ addslashes($c->nombre) }}','{{ addslashes($c->nit??'') }}','{{ addslashes($c->telefono??'') }}','{{ addslashes($c->email??'') }}','{{ addslashes($c->direccion??'') }}','{{ $c->tipo }}',{{ $c->limite_credito }})"
                    class="btn btn-sm btn-light">✏️</button>
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
  <div class="modal">
    <div class="modal-header"><h3 id="modalTitle">Nuevo Cliente</h3><button onclick="cerrarModal()">✕</button></div>
    <div class="modal-body">
      <input type="hidden" id="clienteId">
      <div class="form-group"><label>Nombre <span class="req">*</span></label><input id="c_nombre" class="form-control"></div>
      <div class="form-row">
        <div class="form-group"><label>NIT</label><input id="c_nit" class="form-control"></div>
        <div class="form-group"><label>Teléfono</label><input id="c_tel" class="form-control"></div>
      </div>
      <div class="form-group"><label>Email</label><input id="c_email" type="email" class="form-control"></div>
      <div class="form-group"><label>Dirección</label><input id="c_dir" class="form-control"></div>
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
  document.getElementById('limiteGroup').style.display =
    document.getElementById('c_tipo').value === 'credito' ? 'block' : 'none';
}

function abrirModal(){
  document.getElementById('clienteId').value='';
  ['nombre','nit','tel','email','dir'].forEach(f=>document.getElementById('c_'+f).value='');
  document.getElementById('c_tipo').value='contado';
  document.getElementById('c_limite').value='0';
  document.getElementById('limiteGroup').style.display='none';
  document.getElementById('modalTitle').textContent='Nuevo Cliente';
  document.getElementById('cliError').style.display='none';
  document.getElementById('clientModal').style.display='flex';
}
function editarCliente(id,nombre,nit,tel,email,dir,tipo,limite){
  document.getElementById('clienteId').value=id;
  document.getElementById('c_nombre').value=nombre;
  document.getElementById('c_nit').value=nit;
  document.getElementById('c_tel').value=tel;
  document.getElementById('c_email').value=email;
  document.getElementById('c_dir').value=dir;
  document.getElementById('c_tipo').value=tipo;
  document.getElementById('c_limite').value=limite;
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
    nombre, nit:document.getElementById('c_nit').value,
    telefono:document.getElementById('c_tel').value,
    email:document.getElementById('c_email').value,
    direccion:document.getElementById('c_dir').value,
    tipo:document.getElementById('c_tipo').value,
    limite_credito:document.getElementById('c_limite').value || 0,
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
