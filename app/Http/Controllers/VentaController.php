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
            $subtotal  = collect($items)->sum('subtotal');
            $descuento = (float)$request->input('descuento',0);
            $impuesto  = (float)$request->input('impuesto',0);
            $total     = max(0, $subtotal - $descuento + $impuesto);
            $tipoPago  = $request->input('tipo_venta','contado');

            $venta = Venta::create([
                'codigo'      => 'VTA-'.strtoupper(uniqid()),
                'cliente_id'  => $request->input('cliente_id') ?: null,
                'usuario_id'  => $this->userId(),
                'caja_id'     => Caja::abierta()?->id,
                'subtotal'    => $subtotal,
                'descuento'   => $descuento,
                'impuesto'    => $impuesto,
                'total'       => $total,
                'metodo_pago' => $request->input('metodo_pago','efectivo'),
                'tipo_venta'  => $tipoPago,
                'estado'      => 'completada',
                'notas'       => $request->input('notas'),
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

            // Si es crédito, crear registro
            if ($tipoPago === 'credito' && $venta->cliente_id) {
                Credito::create([
                    'venta_id'         => $venta->id,
                    'cliente_id'       => $venta->cliente_id,
                    'usuario_id'       => $this->userId(),
                    'monto_total'      => $total,
                    'monto_pagado'     => 0,
                    'saldo'            => $total,
                    'estado'           => 'pendiente',
                    'fecha_vencimiento'=> now()->addDays(30),
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
