@extends('layouts.main')
@section('title','Categorías')
@section('content')

<div class="page-header">
  <h1>🏷️ Categorías</h1>
  <button onclick="abrirModal()" class="btn btn-primary">+ Nueva Categoría</button>
</div>

<div class="card">
  <div class="card-body p-0">
    <table class="table" id="catTable">
      <thead><tr><th>Nombre</th><th>Descripción</th><th>Productos</th><th>Acciones</th></tr></thead>
      <tbody>
        @forelse($categorias as $c)
        <tr>
          <td><strong>{{ $c->nombre }}</strong></td>
          <td class="text-muted">{{ $c->descripcion ?? '—' }}</td>
          <td><span class="badge badge-{{ $c->productos_count > 0 ? 'success':'warning' }}">{{ $c->productos_count }}</span></td>
          <td class="actions">
            <button onclick="editarCategoria({{ $c->id }},'{{ addslashes($c->nombre) }}','{{ addslashes($c->descripcion ?? '') }}')"
                    class="btn btn-sm btn-secondary">✏️</button>
            <button onclick="eliminarCategoria({{ $c->id }},this)" class="btn btn-sm btn-danger">🗑️</button>
          </td>
        </tr>
        @empty
        <tr><td colspan="4" class="text-center text-muted" style="padding:2rem">Sin categorías aún.</td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div id="catModal" class="modal-overlay" style="display:none" onclick="if(event.target===this)cerrarModal()">
  <div class="modal">
    <div class="modal-header">
      <h3 id="modalTitle">Nueva Categoría</h3>
      <button onclick="cerrarModal()">✕</button>
    </div>
    <div class="modal-body">
      <input type="hidden" id="catId">
      <div class="form-group">
        <label>Nombre <span class="req">*</span></label>
        <input id="cat_nombre" class="form-control" placeholder="Ej: Tecnología">
      </div>
      <div class="form-group">
        <label>Descripción</label>
        <textarea id="cat_desc" class="form-control" rows="2" placeholder="Descripción opcional..."></textarea>
      </div>
      <div id="catError" class="alert alert-error" style="display:none"></div>
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
function abrirModal() {
  document.getElementById('catId').value = '';
  document.getElementById('cat_nombre').value = '';
  document.getElementById('cat_desc').value = '';
  document.getElementById('modalTitle').textContent = 'Nueva Categoría';
  document.getElementById('catError').style.display = 'none';
  document.getElementById('catModal').style.display = 'flex';
}
function editarCategoria(id, nombre, desc) {
  document.getElementById('catId').value = id;
  document.getElementById('cat_nombre').value = nombre;
  document.getElementById('cat_desc').value = desc;
  document.getElementById('modalTitle').textContent = 'Editar Categoría';
  document.getElementById('catError').style.display = 'none';
  document.getElementById('catModal').style.display = 'flex';
}
function cerrarModal() { document.getElementById('catModal').style.display = 'none'; }

async function guardar() {
  const nombre = document.getElementById('cat_nombre').value.trim();
  if (!nombre) {
    document.getElementById('catError').textContent = 'El nombre es obligatorio.';
    document.getElementById('catError').style.display = 'flex'; return;
  }
  const id = document.getElementById('catId').value;
  const url = id ? `/categorias/${id}` : '{{ route("categorias.guardar") }}';
  const body = new URLSearchParams({ nombre, descripcion: document.getElementById('cat_desc').value, _token: CSRF });
  if (id) body.append('_method','PUT');
  const r = await fetch(url, { method:'POST', body, headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'} });
  const d = await r.json();
  if (d.ok) location.reload();
  else {
    document.getElementById('catError').textContent = d.error || d.message || 'Error.';
    document.getElementById('catError').style.display = 'flex';
  }
}

async function eliminarCategoria(id, btn) {
  if (!confirm('¿Eliminar esta categoría?')) return;
  btn.disabled = true;
  const r = await fetch(`/categorias/${id}`, {
    method:'DELETE', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}
  });
  const d = await r.json();
  if (d.ok) btn.closest('tr').remove();
  else { alert(d.error || 'No se puede eliminar.'); btn.disabled = false; }
}
</script>
@endpush
@endsection
