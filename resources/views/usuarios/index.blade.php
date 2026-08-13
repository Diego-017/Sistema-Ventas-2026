@extends('layouts.main')
@section('title','Usuarios')
@section('content')

<div class="page-header">
  <h1>👤 Gestión de Usuarios</h1>
  <button onclick="abrirModal()" class="btn btn-primary">+ Nuevo Usuario</button>
</div>

<div class="card">
  <div class="card-body p-0">
    <table class="table">
      <thead><tr><th>Nombre</th><th>Email</th><th>Rol</th><th>Estado</th><th>Creado</th><th>Acciones</th></tr></thead>
      <tbody>
        @foreach($usuarios as $u)
        <tr class="{{ !$u->activo ? 'row-inactive':'' }}">
          <td>
            <div style="display:flex;align-items:center;gap:10px">
              <span class="user-avatar-sm" style="background:{{ $u->rol==='admin'?'#4f46e5':'#059669' }}">
                {{ strtoupper(substr($u->nombre,0,1)) }}
              </span>
              <strong>{{ $u->nombre }}</strong>
            </div>
          </td>
          <td>{{ $u->email }}</td>
          <td>
            <span class="badge badge-{{ $u->rol==='admin'?'primary':'success' }}">
              {{ $u->rol === 'admin' ? '👑 Admin' : '🧑 Vendedor' }}
            </span>
          </td>
          <td>
            <span class="badge badge-{{ $u->activo?'success':'danger' }}">
              {{ $u->activo ? 'Activo':'Inactivo' }}
            </span>
          </td>
          <td>{{ $u->created_at->format('d/m/Y') }}</td>
          <td class="actions">
            <button onclick="editarUsuario({{ $u->id }},'{{ addslashes($u->nombre) }}','{{ $u->email }}','{{ $u->rol }}')"
                    class="btn btn-sm btn-secondary">✏️</button>
            @if($u->id !== session('user_id'))
            <button onclick="toggleUsuario({{ $u->id }},this,{{ $u->activo }})"
                    class="btn btn-sm btn-{{ $u->activo?'warning':'success' }}">
              {{ $u->activo ? '🚫 Desactivar':'✅ Activar' }}
            </button>
            @endif
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  </div>
</div>

{{-- Modal crear / editar --}}
<div id="userModal" class="modal-overlay" style="display:none" onclick="if(event.target===this)cerrarModal()">
  <div class="modal">
    <div class="modal-header"><h3 id="modalTitle">Nuevo Usuario</h3><button onclick="cerrarModal()">✕</button></div>
    <div class="modal-body">
      <input type="hidden" id="userId">
      <div class="form-row">
        <div class="form-group">
          <label>Nombre <span class="req">*</span></label>
          <input id="u_nombre" class="form-control" placeholder="Nombre completo">
        </div>
        <div class="form-group">
          <label>Rol <span class="req">*</span></label>
          <select id="u_rol" class="form-control">
            <option value="vendedor">🧑 Vendedor</option>
            <option value="admin">👑 Administrador</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Email <span class="req">*</span></label>
        <input id="u_email" type="email" class="form-control" placeholder="correo@ejemplo.com">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label id="passLabel">Contraseña <span class="req">*</span></label>
          <input id="u_password" type="password" class="form-control" placeholder="Mínimo 6 caracteres">
        </div>
        <div class="form-group">
          <label>Confirmar Contraseña</label>
          <input id="u_password_confirmation" type="password" class="form-control" placeholder="Repetir contraseña">
        </div>
      </div>
      <p id="passHint" class="text-muted small" style="display:none">Deja en blanco para no cambiar la contraseña.</p>
      <div id="userError" class="alert alert-error" style="display:none"></div>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
      <button class="btn btn-primary" id="btnGuardar" onclick="guardar()">💾 Guardar</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name=csrf-token]').content;

function abrirModal() {
  document.getElementById('userId').value='';
  document.getElementById('u_nombre').value='';
  document.getElementById('u_email').value='';
  document.getElementById('u_password').value='';
  document.getElementById('u_password_confirmation').value='';
  document.getElementById('u_rol').value='vendedor';
  document.getElementById('modalTitle').textContent='Nuevo Usuario';
  document.getElementById('passLabel').innerHTML='Contraseña <span class="req">*</span>';
  document.getElementById('passHint').style.display='none';
  document.getElementById('userError').style.display='none';
  document.getElementById('userModal').style.display='flex';
}

function editarUsuario(id,nombre,email,rol) {
  document.getElementById('userId').value=id;
  document.getElementById('u_nombre').value=nombre;
  document.getElementById('u_email').value=email;
  document.getElementById('u_rol').value=rol;
  document.getElementById('u_password').value='';
  document.getElementById('u_password_confirmation').value='';
  document.getElementById('modalTitle').textContent='Editar Usuario';
  document.getElementById('passLabel').innerHTML='Nueva Contraseña';
  document.getElementById('passHint').style.display='block';
  document.getElementById('userError').style.display='none';
  document.getElementById('userModal').style.display='flex';
}

function cerrarModal(){ document.getElementById('userModal').style.display='none'; }

async function guardar() {
  const id  = document.getElementById('userId').value;
  const btn = document.getElementById('btnGuardar');
  btn.disabled = true;

  const body = new URLSearchParams({
    nombre:   document.getElementById('u_nombre').value,
    email:    document.getElementById('u_email').value,
    rol:      document.getElementById('u_rol').value,
    password: document.getElementById('u_password').value,
    password_confirmation: document.getElementById('u_password_confirmation').value,
    _token:   CSRF,
  });

  const url = id ? `/usuarios/${id}` : '{{ route("usuarios.guardar") }}';
  if (id) body.append('_method','PUT');

  try {
    const r = await fetch(url,{method:'POST',body,headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}});
    const d = await r.json();
    if (d.ok || r.redirected) { location.reload(); }
    else {
      const msg = d.message || (d.errors ? Object.values(d.errors).flat().join(', ') : 'Error.');
      document.getElementById('userError').textContent = msg;
      document.getElementById('userError').style.display = 'flex';
    }
  } catch(e) {
    document.getElementById('userError').textContent = 'Error de conexión.';
    document.getElementById('userError').style.display = 'flex';
  } finally { btn.disabled=false; }
}

async function toggleUsuario(id, btn, activo) {
  if (!confirm(activo ? '¿Desactivar este usuario?' : '¿Activar este usuario?')) return;
  btn.disabled=true;
  const r = await fetch(`/usuarios/${id}/toggle`,{
    method:'PATCH', headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}
  });
  const d = await r.json();
  if(d.ok) location.reload();
  else { alert(d.error||'Error.'); btn.disabled=false; }
}
</script>
@endpush
@endsection
