<?php
namespace App\Http\Controllers;

use App\Models\{Cotizacion, CotizacionItem, Cliente, Producto, Venta, VentaItem};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CotizacionController extends Controller
{
    public function index()
    {
        // Marcar vencidas automáticamente
        Cotizacion::where('estado','vigente')
            ->whereNotNull('valida_hasta')
            ->where('valida_hasta','<', today())
            ->update(['estado' => 'vencida']);

        $cotizaciones = Cotizacion::with(['cliente','usuario'])
            ->latest('created_at')
            ->paginate(20);
        return view('cotizaciones.index', compact('cotizaciones'));
    }

    public function nueva()
    {
        $clientes  = Cliente::orderBy('nombre')->get();
        return view('cotizaciones.nueva', compact('clientes'));
    }

    public function store(Request $request)
    {
        $items = json_decode($request->input('items','[]'), true);
        if (empty($items)) {
            return response()->json(['error' => 'Agrega al menos un producto.'], 422);
        }

        $cotizacionId = null;
        DB::transaction(function () use ($request, $items, &$cotizacionId) {
            $subtotal  = collect($items)->sum('subtotal');
            $descuento = (float) $request->input('descuento', 0);
            $total     = max(0, $subtotal - $descuento);

            $cotizacion = Cotizacion::create([
                'codigo'       => 'COT-' . strtoupper(uniqid()),
                'cliente_id'   => $request->input('cliente_id') ?: null,
                'usuario_id'   => $this->userId(),
                'subtotal'     => $subtotal,
                'descuento'    => $descuento,
                'total'        => $total,
                'estado'       => 'vigente',
                'valida_hasta' => $request->input('valida_hasta') ?: null,
                'notas'        => $request->input('notas'),
            ]);

            foreach ($items as $item) {
                CotizacionItem::create([
                    'cotizacion_id'   => $cotizacion->id,
                    'producto_id'     => $item['producto_id'],
                    'nombre_producto' => $item['nombre'],
                    'cantidad'        => (int) $item['cantidad'],
                    'precio_unitario' => (float) $item['precio'],
                    'subtotal'        => (float) $item['subtotal'],
                ]);
            }
            $cotizacionId = $cotizacion->id;
        });

        return response()->json(['ok' => true, 'id' => $cotizacionId]);
    }

    public function show(int $id)
    {
        $cotizacion = Cotizacion::with(['cliente','usuario','items.producto'])->findOrFail($id);
        return view('cotizaciones.show', compact('cotizacion'));
    }

    public function convertir(int $id)
    {
        $cotizacion = Cotizacion::with(['items'])->findOrFail($id);

        if ($cotizacion->estado !== 'vigente') {
            return back()->with('error', 'Solo se pueden convertir cotizaciones vigentes.');
        }

        $ventaId = null;
        DB::transaction(function () use ($cotizacion, &$ventaId) {
            $venta = Venta::create([
                'codigo'      => 'VTA-' . strtoupper(uniqid()),
                'cliente_id'  => $cotizacion->cliente_id,
                'usuario_id'  => $this->userId(),
                'subtotal'    => $cotizacion->subtotal,
                'descuento'   => $cotizacion->descuento,
                'total'       => $cotizacion->total,
                'metodo_pago' => 'efectivo',
                'estado'      => 'completada',
                'notas'       => 'Convertida de cotización ' . $cotizacion->codigo,
            ]);

            foreach ($cotizacion->items as $item) {
                VentaItem::create([
                    'venta_id'        => $venta->id,
                    'producto_id'     => $item->producto_id,
                    'nombre_producto' => $item->nombre_producto,
                    'cantidad'        => $item->cantidad,
                    'precio_unitario' => $item->precio_unitario,
                    'descuento'       => 0,
                    'subtotal'        => $item->subtotal,
                ]);
                Producto::where('id', $item->producto_id)->decrement('stock', $item->cantidad);
            }

            $cotizacion->update(['estado' => 'convertida']);
            $ventaId = $venta->id;
        });

        return redirect()->route('ventas.ver', $ventaId)
                         ->with('success', 'Cotización convertida a venta exitosamente.');
    }
}
