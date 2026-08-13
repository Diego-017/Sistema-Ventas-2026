<?php
namespace App\Http\Controllers;

use App\Models\{Venta, VentaItem, Producto, Cliente, Caja, Credito};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index()
    {
        $ventas = Venta::with(['cliente','usuario'])
            ->latest('created_at')->paginate(25);
        return view('ventas.index', compact('ventas'));
    }

    public function nueva()
    {
        $clientes    = Cliente::orderBy('nombre')->get();
        $cajaAbierta = Caja::abierta();
        return view('ventas.nueva', compact('clientes','cajaAbierta'));
    }

    public function store(Request $request)
    {
        $items = json_decode($request->input('items','[]'), true);
        if (empty($items)) return response()->json(['error'=>'Carrito vacío.'],422);

        // Validar stock
        foreach ($items as $item) {
            $p = Producto::find($item['producto_id']);
            if (!$p || $p->stock < $item['cantidad']) {
                return response()->json(['error'=>"Stock insuficiente: {$item['nombre']}"],422);
            }
        }

        $ventaId = null;
        DB::transaction(function () use ($request, $items, &$ventaId) {
            $cliente = $request->filled('cliente_id') ? Cliente::find($request->input('cliente_id')) : null;

            // Gravado = base de productos NO exentos. Exenta = base de productos exentos.
            $gravado = collect($items)->filter(fn($i) => empty($i['exento']))->sum('subtotal');
            $exenta  = collect($items)->filter(fn($i) => !empty($i['exento']))->sum('subtotal');
            $subtotal  = $gravado + $exenta;
            $descuento = (float) $request->input('descuento', 0);
            $impuesto  = round($gravado * 0.13, 2); // IVA 13% sobre lo gravado (los precios base ya no lo incluyen)

            // Percepción (1%) y Retención (1%) sólo si el cliente fiscal seleccionado las tiene activas.
            $percepcion = ($cliente && $cliente->aplica_percepcion) ? round($gravado * 0.01, 2) : 0;
            $retencion  = ($cliente && $cliente->retiene_iva)       ? round($gravado * 0.01, 2) : 0;

            $total    = max(0, $subtotal - $descuento + $impuesto + $percepcion - $retencion);
            $tipoPago = $request->input('tipo_venta','contado');

            $venta = Venta::create([
                'codigo'       => 'VTA-'.strtoupper(uniqid()),
                'cliente_id'   => $cliente?->id,
                'usuario_id'   => $this->userId(),
                'caja_id'      => Caja::abierta()?->id,
                'subtotal'     => $subtotal,
                'descuento'    => $descuento,
                'impuesto'     => $impuesto,
                'venta_exenta' => $exenta,
                'percepcion'   => $percepcion,
                'retencion'    => $retencion,
                'total'        => $total,
                'metodo_pago'  => $request->input('metodo_pago','efectivo'),
                'tipo_venta'   => $tipoPago,
                'estado'       => 'completada',
                'notas'        => $request->input('notas'),
            ]);

            foreach ($items as $item) {
                VentaItem::create([
                    'venta_id'        => $venta->id,
                    'producto_id'     => $item['producto_id'],
                    'nombre_producto' => $item['nombre'],
                    'cantidad'        => (int)$item['cantidad'],
                    'precio_unitario' => (float)$item['precio'],
                    'descuento'       => (float)($item['descuento_item'] ?? 0),
                    'subtotal'        => (float)$item['subtotal'],
                ]);
                Producto::where('id',$item['producto_id'])->decrement('stock',(int)$item['cantidad']);
            }

            // Si es crédito, crear registro (usa los días de crédito propios del cliente si los tiene)
            if ($tipoPago === 'credito' && $venta->cliente_id) {
                $dias = $cliente?->dias_credito ?: 30;
                Credito::create([
                    'venta_id'         => $venta->id,
                    'cliente_id'       => $venta->cliente_id,
                    'usuario_id'       => $this->userId(),
                    'monto_total'      => $total,
                    'monto_pagado'     => 0,
                    'saldo'            => $total,
                    'estado'           => 'pendiente',
                    'fecha_vencimiento'=> now()->addDays($dias),
                    'notas'            => 'Venta a crédito: '.$venta->codigo,
                ]);
            }

            // Actualizar caja
            if ($caja = Caja::abierta()) {
                $caja->increment('total_ventas', $total);
            }

            $ventaId = $venta->id;
        });

        return response()->json(['ok'=>true,'id'=>$ventaId]);
    }

    public function show(int $id)
    {
        $venta = Venta::with(['cliente','usuario','items'])->findOrFail($id);
        return view('ventas.show', compact('venta'));
    }

    public function anular(int $id)
    {
        $this->requireAdmin();
        $venta = Venta::with('items')->findOrFail($id);
        DB::transaction(function () use ($venta) {
            foreach ($venta->items as $item) {
                Producto::where('id',$item->producto_id)->increment('stock',$item->cantidad);
            }
            if ($caja = Caja::abierta()) {
                $caja->decrement('total_ventas', $venta->total);
            }
            $venta->update(['estado'=>'anulada']);
        });
        return response()->json(['ok'=>true]);
    }

    public function porVendedor()
    {
        $this->requireAdmin();
        $datos = DB::table('ventas as v')
            ->join('usuarios as u','v.usuario_id','=','u.id')
            ->where('v.estado','completada')
            ->whereMonth('v.created_at', now()->month)
            ->selectRaw('u.nombre, COUNT(*) as total_ventas, SUM(v.total) as monto_total')
            ->groupBy('u.id','u.nombre')
            ->orderByDesc('monto_total')->get();
        return view('ventas.por_vendedor', compact('datos'));
    }
}
