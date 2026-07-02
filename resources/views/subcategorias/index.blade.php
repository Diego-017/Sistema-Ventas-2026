@extends('layouts.main')
@section('title','Subcategorías')
@section('content')
<div class="page-header">
  <h1>🔖 Subcategorías</h1>
  <button onclick="abrirModal()" class="btn btn-primary">+ Nueva Subcategoría</button>
</div>
<div class="card">
  <div class="card-body p-0">
    <table class="table" id="subTable">
      <thead>
        <tr><th>Nombre</th><th>Categoría</th><th>Descripción</th><th>Productos</th><th>Acciones</th></tr>
      </thead>
      <tbody>
        @forelse($subcategorias as $s)
        <tr>
          <td><strong>{{ $s->nombre }}</strong></td>
          <td><span class="badge badge-primary">{{ $s->categoria->nombre }}</span></td>
          <td class="text-muted">{{ $s->descripcion ?? '—' }}</td>
          <td><span class="badge badge-{{ $s->productos_count > 0 ? 'success':'warning' }}">{{ $s->productos_count }}</span></td>
          <td class="actions">
            <button onclick="editar({{ $s->id }},{{ $s->categoria_id }},'{{ addslashes($s->nombre) }}','{{ addslashes($s->descripcion ?? '') }}')"
                    class="btn btn-sm btn-secondary">✏️</button>
            <button onclick="eliminar({{ $s->id }},this)" class="btn btn-sm btn-danger">🗑️</button>
          </td>
        </tr>
        @empty
        <tr><td colspan="5" class="text-center text-muted" style="padding:2rem">Sin subcategorías registradas.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div id="subModal" class="modal-overlay" style="display:none" onclick="if(event.target===this)cerrarModal()">
  <div class="modal">
    <div class="modal-header"><h3 id="modalTitle">Nueva Subcategoría</h3><button onclick="cerrarModal()">✕</button></div>
    <div class="modal-body">
      <input type="hidden" id="subId">
      <div class="form-group">
        <label>Categoría <span class="req">*</span></label>
        <select id="sub_cat" class="form-control">
          <option value="">— Seleccionar —</option>
          @foreach($categorias as $c)
          <option value="{{ $c->id }}">{{ $c->nombre }}</option>
          @endforeach
        </select>
      </div>
      <div class="form-group">
        <label>Nombre <span class="req">*</span></label>
        <input id="sub_nombre" class="form-control" placeholder="Ej: Laptops">
      </div>
      <div class="form-group">
        <label>Descripción</label>
        <textarea id="sub_desc" class="form-control" rows="2" placeholder="Opcional..."></textarea>
      </div>
      <div id="subError" class="alert alert-error" style="display:none"></div>
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
function abrirModal(){
  document.getElementById('subId').value='';
  document.getElementById('sub_cat').value='';
  document.getElementById('sub_nombre').value='';
  document.getElementById('sub_desc').value='';
  document.getElementById('modalTitle').textContent='Nueva Subcategoría';
  document.getElementById('subError').style.display='none';
  document.getElementById('subModal').style.display='flex';
}
function editar(id,catId,nombre,desc){
  document.getElementById('subId').value=id;
  document.getElementById('sub_cat').value=catId;
  document.getElementById('sub_nombre').value=nombre;
  document.getElementById('sub_desc').value=desc;
  document.getElementById('modalTitle').textContent='Editar Subcategoría';
  document.getElementById('subError').style.display='none';
  document.getElementById('subModal').style.display='flex';
}
function cerrarModal(){ document.getElementById('subModal').style.display='none'; }
async function guardar(){
  const cat=document.getElementById('sub_cat').value;
  const nombre=document.getElementById('sub_nombre').value.trim();
  if(!cat||!nombre){
    document.getElementById('subError').textContent='Categoría y nombre son obligatorios.';
    document.getElementById('subError').style.display='flex'; return;
  }
  const id=document.getElementById('subId').value;
  const url=id?`/subcategorias/${id}`:'{{ route("subcategorias.guardar") }}';
  const body=new URLSearchParams({categoria_id:cat,nombre,descripcion:document.getElementById('sub_desc').value,_token:CSRF});
  if(id) body.append('_method','PUT');
  const r=await fetch(url,{method:'POST',body,headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}});
  const d=await r.json();
  if(d.ok) location.reload();
  else{document.getElementById('subError').textContent=d.error||d.message||'Error.';document.getElementById('subError').style.display='flex';}
}
async function eliminar(id,btn){
  if(!confirm('¿Eliminar subcategoría?')) return;
  btn.disabled=true;
  const r=await fetch(`/subcategorias/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}});
  const d=await r.json();
  if(d.ok) btn.closest('tr').remove();
  else{alert(d.error||'Error.');btn.disabled=false;}
}
</script>
@endpush
@endsection
