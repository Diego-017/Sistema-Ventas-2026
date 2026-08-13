@extends('layouts.main')
@section('title','Traslados de Inventario')
@section('content')
<div class="page-header">
  <h1>🔄 Administrar Traslados</h1>
  <button onclick="abrirModalTraslado()" class="btn btn-primary">+ Nuevo Traslado</button>
</div>

<div class="grid-2">
  {{-- Historial --}}
  <div class="card" style="grid-column:1/-1">
    <div class="card-header"><h3>Historial de Traslados / Ajustes</h3></div>
    <div class="card-body p-0">
      <table class="table">
        <thead><tr><th>Fecha</th><th>Tipo</th><th>Concepto</th><th>Productos</th><th>Usuario</th></tr></thead>
        <tbody>
          @forelse($traslados as $t)
          <tr>
            <td>{{ $t->created_at->format('d/m/Y H:i') }}</td>
            <td>
              <span class="badge badge-{{ $t->tipo==='entrada'?'success':($t->tipo==='salida'?'danger':'warning') }}">
                {{ strtoupper($t->tipo) }}
              </span>
            </td>
            <td>{{ $t->concepto ?? '—' }}</td>
            <td>{{ $t->items->count() }} producto(s)</td>
            <td>{{ $t->usuario->nombre }}</td>
          </tr>
          @empty
          <tr><td colspan="5" class="text-center text-muted" style="padding:2rem">Sin traslados.</td></tr>
          @endforelse
        </tbody>
      </table>
      <div class="table-footer">{{ $traslados->links() }}</div>
    </div>
  </div>
</div>

{{-- Modal Nuevo Traslado --}}
<div id="trasladoModal" class="modal-overlay" style="display:none" onclick="if(event.target===this)cerrarModal()">
  <div class="modal" style="max-width:700px">
    <div class="modal-header"><h3>Nuevo Traslado / Ajuste de Inventario</h3><button onclick="cerrarModal()">✕</button></div>
    <div class="modal-body">
      <form id="trasladoForm" method="POST" action="{{ route('inventario.traslados.store') }}">
        @csrf
        <div class="form-row">
          <div class="form-group">
            <label>Concepto <span class="req">*</span></label>
            <input type="text" name="concepto" class="form-control" required placeholder="Ej: Ajuste por conteo físico">
          </div>
          <div class="form-group">
            <label>Tipo <span class="req">*</span></label>
            <select name="tipo" class="form-control" id="tipoTraslado">
              <option value="entrada">📥 Entrada</option>
              <option value="salida">📤 Salida</option>
              <option value="ajuste">🔄 Ajuste</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label>Notas</label>
          <textarea name="notas" class="form-control" rows="2" placeholder="Observaciones..."></textarea>
        </div>

        <div class="card" style="margin-top:12px">
          <div class="card-header">
            <h3>Productos a ajustar</h3>
            <div style="display:flex;gap:8px">
              <input type="text" id="searchProdTraslado" class="form-control" placeholder="Buscar producto..." style="width:220px">
            </div>
          </div>
          <div id="searchResTraslado" class="search-results" style="margin:0 16px 8px"></div>
          <div class="card-body p-0">
            <table class="table" id="trasladoItems">
              <thead><tr><th>Producto</th><th>Stock Actual</th><th id="colCantidad">Cantidad</th><th>Notas</th><th></th></tr></thead>
              <tbody id="trasladoBody">
                <tr id="emptyTraslado"><td colspan="5" class="text-center text-muted" style="padding:1.5rem">Agrega productos</td></tr>
              </tbody>
            </table>
          </div>
        </div>
      </form>
    </div>
    <div class="modal-footer">
      <button class="btn btn-secondary" onclick="cerrarModal()">Cancelar</button>
      <button class="btn btn-primary" onclick="submitTraslado()">✅ Guardar Traslado</button>
    </div>
  </div>
</div>

@push('scripts')
<script>
const CSRF = document.querySelector('meta[name=csrf-token]').content;
const trasladoCart = {};

function abrirModalTraslado(){
  document.getElementById('trasladoModal').style.display='flex';
}
function cerrarModal(){ document.getElementById('trasladoModal').style.display='none'; }

let timer;
document.getElementById('searchProdTraslado').addEventListener('input', function(){
  clearTimeout(timer);
  timer = setTimeout(async () => {
    if(this.value.length < 2){ document.getElementById('searchResTraslado').innerHTML=''; return; }
    const res = await fetch(`/productos/buscar?q=${encodeURIComponent(this.value)}`);
    const list = await res.json();
    document.getElementById('searchResTraslado').innerHTML = list.map(p => `
      <div class="search-item" onclick='addTrasladoItem(${JSON.stringify(p)})'>
        <strong>${p.nombre}</strong>
        <span style="float:right;color:#4f46e5">Stock: <strong>${p.stock}</strong></span><br>
        <small class="text-muted">SKU: ${p.sku||'—'}</small>
      </div>`).join('') || '<div class="search-item text-muted">Sin resultados</div>';
  }, 300);
});

function addTrasladoItem(p){
  document.getElementById('searchResTraslado').innerHTML='';
  document.getElementById('searchProdTraslado').value='';
  if(trasladoCart[p.id]) return;
  trasladoCart[p.id] = p;
  renderTrasladoItems();
}

function removeTrasladoItem(id){ delete trasladoCart[id]; renderTrasladoItems(); }

function renderTrasladoItems(){
  const ids = Object.keys(trasladoCart);
  const tbody = document.getElementById('trasladoBody');
  const tipo = document.getElementById('tipoTraslado').value;
  const label = tipo === 'ajuste' ? 'Cantidad Final' : 'Cantidad';
  document.getElementById('colCantidad').textContent = label;

  if(!ids.length){
    tbody.innerHTML='<tr id="emptyTraslado"><td colspan="5" class="text-center text-muted" style="padding:1.5rem">Agrega productos</td></tr>';
    return;
  }
  tbody.innerHTML = ids.map(id => {
    const p = trasladoCart[id];
    return `<tr>
      <td>${p.nombre}</td>
      <td><span class="badge badge-success">${p.stock}</span></td>
      <td><input type="number" name="items[${id}][cantidad]" min="1" value="1"
           data-producto="${id}" class="form-control form-control-sm" style="width:80px" required></td>
      <td><input type="text" name="items[${id}][notas]" class="form-control form-control-sm" placeholder="Nota...">
          <input type="hidden" name="items[${id}][producto_id]" value="${id}"></td>
      <td><button type="button" class="btn btn-sm btn-danger" onclick="removeTrasladoItem(${id})">✕</button></td>
    </tr>`;
  }).join('');
}

document.getElementById('tipoTraslado').addEventListener('change', renderTrasladoItems);

function submitTraslado(){
  if(!Object.keys(trasladoCart).length){ alert('Agrega al menos un producto.'); return; }
  document.getElementById('trasladoForm').submit();
}
</script>
@endpush
@endsection
