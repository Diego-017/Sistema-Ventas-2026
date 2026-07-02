@extends('layouts.main')
@section('title','Detalle de Venta')
@section('content')
<div class="page-header">
  <h1>🧾 Comprobante</h1>
  <div style="display:flex;gap:8px">
    <a href="{{ route('impresoras.ticket',$venta->id) }}" target="_blank" class="btn btn-light">🖨️ Imprimir Ticket</a>
    <a href="{{ route('ventas.index') }}" class="btn btn-light">← Volver</a>
    @if(session('user.rol')==='admin' && $venta->estado==='completada')
    <button onclick="anularVenta({{ $venta->id }})" class="btn btn-danger">❌ Anular</button>
    @endif
  </div>
</div>
<div class="card receipt">
  <div class="receipt-header">
    <div class="receipt-logo">🛒</div>
    <h2>{{ \App\Models\Configuracion::get('nombre_negocio','DigitalsPos') }}</h2>
    <p class="receipt-code">{{ $venta->codigo }}</p>
    <p>{{ $venta->created_at->format('d/m/Y H:i:s') }}</p>
  </div>
  <div class="receipt-meta">
    <div class="meta-row"><span>Vendedor:</span><span>{{ $venta->usuario->nombre }}</span></div>
    @if($venta->cliente)
    <div class="meta-row"><span>Cliente:</span><span>{{ $venta->cliente->nombre }}</span></div>
    @if($venta->cliente->nit)
    <div class="meta-row"><span>NIT:</span><span>{{ $venta->cliente->nit }}</span></div>
    @endif
    @endif
    <div class="meta-row"><span>Tipo:</span><span>{{ ucfirst($venta->tipo_venta) }}</span></div>
    <div class="meta-row"><span>Pago:</span><span>{{ ucfirst($venta->metodo_pago) }}</span></div>
    @if($venta->notas)<div class="meta-row"><span>Notas:</span><span>{{ $venta->notas }}</span></div>@endif
  </div>
  <table class="table">
    <thead><tr><th>Producto</th><th style="text-align:center">Cant.</th><th style="text-align:right">Precio</th><th style="text-align:right">Subtotal</th></tr></thead>
    <tbody>
      @foreach($venta->items as $item)
      <tr>
        <td>{{ $item->nombre_producto }}</td>
        <td style="text-align:center">{{ $item->cantidad }}</td>
        <td style="text-align:right">${{ number_format($item->precio_unitario,2) }}</td>
        <td style="text-align:right">${{ number_format($item->subtotal,2) }}</td>
      </tr>
      @endforeach
    </tbody>
  </table>
  <div class="receipt-footer">
    <div class="total-row"><span>Subtotal:</span><span>${{ number_format($venta->subtotal,2) }}</span></div>
    @if($venta->descuento > 0)
    <div class="total-row" style="color:#e74a3b"><span>Descuento:</span><span>-${{ number_format($venta->descuento,2) }}</span></div>
    @endif
    <div class="total-row total-final"><span>TOTAL:</span><span>${{ number_format($venta->total,2) }}</span></div>
  </div>
  <div style="padding:16px">
    <span class="badge badge-{{ $venta->estado==='completada'?'success':($venta->estado==='anulada'?'danger':'warning') }}">
      {{ strtoupper($venta->estado) }}
    </span>
    @if($venta->tipo_venta==='credito')
    <span class="badge badge-warning">💳 CRÉDITO</span>
    @endif
  </div>
</div>
@push('scripts')
<script>
function anularVenta(id) {
  if (!confirm('¿Anular esta venta? Se restaurará el stock.')) return;
  fetch(`/ventas/${id}/anular`, {
    method: 'PATCH',
    headers: {'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content, 'Accept':'application/json'}
  }).then(r=>r.json()).then(d=>{ if(d.ok) location.reload(); else alert('Error al anular.'); });
}
</script>
@endpush
@endsection
