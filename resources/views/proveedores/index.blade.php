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
      <thead><tr><th>Nombre</th><th>Contacto</th><th>Teléfono</th><th>Email</th><th>Dirección</th><th>Acciones</th></tr></thead>
      <tbody>
        @forelse($proveedores as $p)
        <tr>
          <td><strong>{{ $p->nombre }}</strong></td>
          <td>{{ $p->contacto ?? '—' }}</td>
          <td>{{ $p->telefono ?? '—' }}</td>
          <td>{{ $p->email ?? '—' }}</td>
          <td>{{ $p->direccion ?? '—' }}</td>
          <td class="actions">
            <button onclick="editar({{ $p->id }},'{{ addslashes($p->nombre) }}','{{ addslashes($p->contacto??'') }}','{{ addslashes($p->telefono??'') }}','{{ addslashes($p->email??'') }}','{{ addslashes($p->direccion??'') }}')"
                    class="btn btn-sm btn-secondary">✏️</button>
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
  <div class="modal">
    <div class="modal-header"><h3 id="modalTitle">Nuevo Proveedor</h3><button onclick="cerrarModal()">✕</button></div>
    <div class="modal-body">
      <input type="hidden" id="provId">
      <div class="form-group"><label>Nombre <span class="req">*</span></label><input id="p_nombre" class="form-control"></div>
      <div class="form-row">
        <div class="form-group"><label>Contacto</label><input id="p_contacto" class="form-control"></div>
        <div class="form-group"><label>Teléfono</label><input id="p_telefono" class="form-control"></div>
      </div>
      <div class="form-group"><label>Email</label><input id="p_email" type="email" class="form-control"></div>
      <div class="form-group"><label>Dirección</label><input id="p_dir" class="form-control"></div>
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
const fields = ['nombre','contacto','telefono','email','dir'];

function abrirModal() {
  document.getElementById('provId').value='';
  fields.forEach(f => document.getElementById('p_'+f).value='');
  document.getElementById('modalTitle').textContent='Nuevo Proveedor';
  document.getElementById('provError').style.display='none';
  document.getElementById('provModal').style.display='flex';
}
function editar(id,nombre,contacto,telefono,email,dir) {
  document.getElementById('provId').value=id;
  document.getElementById('p_nombre').value=nombre;
  document.getElementById('p_contacto').value=contacto;
  document.getElementById('p_telefono').value=telefono;
  document.getElementById('p_email').value=email;
  document.getElementById('p_dir').value=dir;
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
    nombre, contacto:document.getElementById('p_contacto').value,
    telefono:document.getElementById('p_telefono').value,
    email:document.getElementById('p_email').value,
    direccion:document.getElementById('p_dir').value, _token:CSRF
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
