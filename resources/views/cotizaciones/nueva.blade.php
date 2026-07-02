@extends('layouts.main')
@section('title','Nueva Cotización')
@section('content')
<div class="page-header">
  <h1>📝 Nueva Cotización</h1>
  <a href="{{ route('cotizaciones.index') }}" class="btn btn-secondary">← Volver</a>
</div>

<div class="grid-pos">
  <div>
    <div class="card">
      <div class="card-header"><h3>🔍 Buscar Producto</h3></div>
      <div class="card-body">
        <input type="search" id="searchProducto" class="form-control"
               placeholder="Nombre, SKU o código de barras..." autocomplete="off">
        <div id="searchResults" class="search-results"></div>
      </div>
    </div>
  </div>

  <div>
    <div class="card">
      <div class="card-header"><h3>📋 Detalle de Cotización</h3></div>
      <div class="card-body p-0">
        <table class="table">
          <thead><tr><th>Producto</th><th>Cant.</th><th>Precio</th><th>Subtotal</th><th></th></tr></thead>
          <tbody id="cartBody">
            <tr id="emptyCart"><td colspan="5" class="text-center text-muted" style="padding:2rem">Agrega productos</td></tr>
          </tbody>
        </table>
      </div>
      <div class="card-footer">
        <div class="cart-totals">
          <div class="total-row"><span>Subtotal:</span><span id="lblSubtotal">$0.00</span></div>
          <div class="total-row">
            <label style="margin:0">Descuento ($):</label>
            <input type="number" id="descuento" value="0" min="0" step="0.01"
                   class="form-control form-control-sm" style="width:100px">
          </div>
          <div class="total-row total-final"><span>TOTAL:</span><span id="lblTotal">$0.00</span></div>
        </div>

        <form id="cotForm" method="POST" action="{{ route('cotizaciones.store') }}">
          @csrf
          <input type="hidden" name="items"     id="itemsInput">
          <input type="hidden" name="subtotal"  id="subtotalInput">
          <input type="hidden" name="descuento" id="descuentoInput">
          <input type="hidden" name="total"     id="totalInput">

          <div class="form-row mt-3">
            <div class="form-group">
              <label>Cliente</label>
              <select name="cliente_id" class="form-control">
                <option value="">— Cliente general —</option>
                @foreach($clientes as $c)
                  <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                @endforeach
              </select>
            </div>
            <div class="form-group">
              <label>Válida hasta</label>
              <input type="date" name="valida_hasta" class="form-control"
                     value="{{ now()->addDays(15)->toDateString() }}">
            </div>
          </div>
          <div class="form-group">
            <label>Notas</label>
            <input type="text" name="notas" class="form-control" placeholder="Observaciones...">
          </div>
          <button type="submit" id="btnGuardar" class="btn btn-primary btn-block btn-lg">💾 Guardar Cotización</button>
        </form>
      </div>
    </div>
  </div>
</div>

@push('scripts')
<script>
const cart={};
const CSRF=document.querySelector('meta[name=csrf-token]').content;

let timer;
document.getElementById('searchProducto').addEventListener('input',function(){
  clearTimeout(timer); timer=setTimeout(()=>doSearch(this.value.trim()),300);
});

async function doSearch(term){
  const box=document.getElementById('searchResults');
  if(term.length<2){box.innerHTML='';return;}
  const res=await fetch(`{{ route('productos.buscar') }}?q=${encodeURIComponent(term)}`);
  const list=await res.json();
  box.innerHTML=list.map(p=>`
    <div class="search-item" onclick='addToCart(${JSON.stringify(p)})'>
      <strong>${p.nombre}</strong>
      <span style="float:right;font-weight:700;color:#4f46e5">$${parseFloat(p.precio_venta).toFixed(2)}</span><br>
      <small class="text-muted">SKU: ${p.sku||'—'} | Stock: ${p.stock}</small>
    </div>`).join('')||'<div class="search-item text-muted">Sin resultados</div>';
}

function addToCart(p){
  document.getElementById('searchResults').innerHTML='';
  document.getElementById('searchProducto').value='';
  if(cart[p.id]) cart[p.id].cantidad++;
  else cart[p.id]={...p,cantidad:1};
  renderCart();
}
function removeFromCart(id){delete cart[id];renderCart();}
function updateQty(id,val){cart[id].cantidad=Math.max(1,parseInt(val)||1);renderCart();}

function renderCart(){
  const ids=Object.keys(cart);
  const tbody=document.getElementById('cartBody');
  if(!ids.length){
    tbody.innerHTML='<tr id="emptyCart"><td colspan="5" class="text-center text-muted" style="padding:2rem">Agrega productos</td></tr>';
    recalc(0);return;
  }
  tbody.innerHTML=ids.map(id=>{
    const i=cart[id];
    return `<tr>
      <td>${i.nombre}</td>
      <td><input type="number" value="${i.cantidad}" min="1" style="width:70px" class="form-control form-control-sm" onchange="updateQty(${id},this.value)"></td>
      <td>$${parseFloat(i.precio_venta).toFixed(2)}</td>
      <td>$${(i.precio_venta*i.cantidad).toFixed(2)}</td>
      <td><button class="btn btn-sm btn-danger" onclick="removeFromCart(${id})">✕</button></td>
    </tr>`;
  }).join('');
  recalc(ids.reduce((s,id)=>s+cart[id].precio_venta*cart[id].cantidad,0));
}

function recalc(sub){
  const desc=Math.max(0,parseFloat(document.getElementById('descuento').value)||0);
  document.getElementById('lblSubtotal').textContent='$'+sub.toFixed(2);
  document.getElementById('lblTotal').textContent='$'+Math.max(0,sub-desc).toFixed(2);
}
document.getElementById('descuento').addEventListener('input',()=>{
  recalc(Object.values(cart).reduce((s,i)=>s+i.precio_venta*i.cantidad,0));
});

document.getElementById('cotForm').addEventListener('submit',async function(e){
  e.preventDefault();
  if(!Object.keys(cart).length){alert('Agrega productos.');return;}
  const btn=document.getElementById('btnGuardar');
  btn.disabled=true;btn.textContent='Guardando...';
  const items=Object.values(cart).map(i=>({producto_id:i.id,nombre:i.nombre,cantidad:i.cantidad,precio:i.precio_venta,subtotal:i.precio_venta*i.cantidad}));
  const sub=items.reduce((s,i)=>s+i.subtotal,0);
  const desc=Math.max(0,parseFloat(document.getElementById('descuento').value)||0);
  document.getElementById('itemsInput').value=JSON.stringify(items);
  document.getElementById('subtotalInput').value=sub.toFixed(2);
  document.getElementById('descuentoInput').value=desc.toFixed(2);
  document.getElementById('totalInput').value=Math.max(0,sub-desc).toFixed(2);
  try{
    const r=await fetch(this.action,{method:'POST',body:new FormData(this),headers:{'X-CSRF-TOKEN':CSRF,'Accept':'application/json'}});
    const d=await r.json();
    if(d.ok) window.location.href=`/cotizaciones/${d.id}`;
    else{alert(d.error||'Error.');btn.disabled=false;btn.textContent='💾 Guardar Cotización';}
  }catch{alert('Error de conexión.');btn.disabled=false;btn.textContent='💾 Guardar Cotización';}
});
</script>
@endpush
@endsection
