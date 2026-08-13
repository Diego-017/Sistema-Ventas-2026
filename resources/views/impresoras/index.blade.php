@extends('layouts.main')
@section('title','Impresoras')
@section('content')
<div class="page-header">
  <h1>🖨️ Administrar Impresoras</h1>
  <button onclick="abrirModal()" class="btn btn-primary">+ Nueva Impresora</button>
</div>

<div class="card">
  <div class="card-body p-0">
    <table class="table">
      <thead><tr><th>Nombre</th><th>Tipo</th><th>Conexión</th><th>Papel</th><th>Estado</th><th>Predeterminada</th><th></th></tr></thead>
      <tbody>
        @forelse($impresoras as $i)
        <tr>
          <td><strong>{{ $i->nombre }}</strong></td>
          <td>
            <span class="badge badge-{{ $i->tipo==='termica'?'info':'primary' }}">
              {{ $i->tipo === 'termica' ? '🧾 Térmica' : ($i->tipo === 'laser' ? '⚡ Láser' : '💧 Inkjet') }}
            </span>
          </td>
          <td>{{ $i->conexion ?? '—' }}</td>
          <td>{{ $i->ancho_papel }}mm</td>
          <td><span class="badge badge-{{ $i->activa?'success':'danger' }}">{{ $i->activa?'Activa':'Inactiva' }}</span></td>
          <td>@if($i->predeterminada)<span class="badge badge-primary">⭐ Predeterminada</span>@endif</td>
          <td class="actions">
            <button onclick="editar({{ $i->id }},'{{ addslashes($i->nombre) }}','{{ $i->tipo }}','{{ addslashes($i->conexion??'') }}',{{ $i->ancho_papel }},{{ $i->activa?1:0 }},{{ $i->predeterminada?1:0 }})"
                    class="btn btn-sm btn-light">✏️</button>
            <button onclick="eliminar({{ $i->id }},this)" class="btn btn-sm btn-danger">🗑️</button>
          </td>
        </tr>
        @empty
        <tr><td colspan="7" class="text-center text-muted" style="padding:2rem">
          Sin impresoras configuradas. Agrega una para imprimir tickets de venta.
        </td></tr>
        @endforelse
      </tbody>
    </table>
  </div>
</div>

<div class="card" style="max-width:600px">
  <div class="card-header"><h3>ℹ️ Información</h3></div>
  <div class="card-body">
    <p class="small text-muted">
      Las impresoras térmicas de 58mm o 80mm son compatibles con la mayoría de modelos POS
      (Epson TM-T20, Star TSP100, Xprinter, etc). Configura la conexión USB o de red según tu equipo.
    </p>
  </div>
</div>

<div id="impModal" class="modal-overlay" style="display:none" onclick="if(event.target===this)cerrarModal()">
  <div class="modal">
    <div class="modal-header"><h3 id="modalTitle">Nueva Impresora</h3><button onclick="cerrarModal()">✕</button></div>
    <div class="modal-body">
      <input type="hidden" id="impId">
      <div class="form-group">
        <label>Nombre <span class="req">*</span></label>
        <input id="imp_nombre" class="form-control" placeholder="Ej: Impresora Caja 1">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label>Tipo</label>
          <select id="imp_tipo" class="form-control">
            <option value="termica">🧾 Térmica</option>
            <option value="laser">⚡ Láser</option>
            <option value="inkjet">💧 Inkjet</option>
          </select>
        </div>
        <div class="form-group">
          <label>Ancho Papel</label>
          <select id="imp_papel" class="form-control">
            <option value="80">80mm</option>
            <option value="58">58mm</option>
          </select>
        </div>
      </div>
      <div class="form-group">
        <label>Conexión</label>
        <input id="imp_conexion" class="form-control" placeholder="USB001, 192.168.1.50, Bluetooth...">
      </div>
      <div class="form-row">
        <div class="form-group">
          <label><input type="checkbox" id="imp_activa" checked style="width:auto;margin-right:6px">Activa</label>
        </div>
        <div class="form-group">
          <label><input type="checkbox" id="imp_predet" style="width:auto;margin-right:6px">Predeterminada</label>
        </div>
      </div>
      <div id="impError" class="alert alert-error" style="display:none"></div>
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
  document.getElementById('impId').value='';
  document.getElementById('imp_nombre').value='';
  document.getElementById('imp_tipo').value='termica';
  document.getElementById('imp_papel').value='80';
  document.getElementById('imp_conexion').value='';
  document.getElementById('imp_activa').checked=true;
  document.getElementById('imp_predet').checked=false;
  document.getElementById('modalTitle').textContent='Nueva Impresora';
  document.getElementById('impError').style.display='none';
  document.getElementById('impModal').style.display='flex';
}
function editar(id,nombre,tipo,conexion,papel,activa,predet){
  document.getElementById('impId').value=id;
  document.getElementById('imp_nombre').value=nombre;
  document.getElementById('imp_tipo').value=tipo;
  document.getElementById('imp_papel').value=papel;
  document.getElementById('imp_conexion').value=conexion;
  document.getElementById('imp_activa').checked=!!activa;
  document.getElementById('imp_predet').checked=!!predet;
  document.getElementById('modalTitle').textContent='Editar Impresora';
  document.getElementById('impError').style.display='none';
  document.getElementById('impModal').style.display='flex';
}
function cerrarModal(){ document.getElementById('impModal').style.display='none'; }

async function guardar(){
  const nombre=document.getElementById('imp_nombre').value.trim();
  if(!nombre){document.getElementById('impError').textContent='El nombre es obligatorio.';document.getElementById('impError').style.display='flex';return;}
  const id=document.getElementById('impId').value;
  const body=new URLSearchParams({
    nombre,
    tipo:document.getElementById('imp_tipo').value,
    conexion:document.getElementById('imp_conexion').value,
    ancho_papel:document.getElementById('imp_papel').value,
    activa:document.getElementById('imp_activa').checked?1:0,
    predeterminada:document.getElementById('imp_predet').checked?1:0,
    _token:CSRF
  });
  const url=id?`/impresoras/${id}`:'{{ route("impresoras.guardar") }}';
  if(id) body.append('_method','PUT');
  const r=await fetch(url,{method:'POST',body,headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}});
  const d=await r.json();
  if(d.ok) location.reload();
  else{document.getElementById('impError').textContent=d.message||'Error.';document.getElementById('impError').style.display='flex';}
}
async function eliminar(id,btn){
  if(!confirm('¿Eliminar impresora?')) return;
  btn.disabled=true;
  const r=await fetch(`/impresoras/${id}`,{method:'DELETE',headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}});
  const d=await r.json();
  if(d.ok) btn.closest('tr').remove();
  else{alert('Error.');btn.disabled=false;}
}
</script>
@endpush
@endsection
