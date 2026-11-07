<?php
namespace App\Http\Controllers;

use App\Models\{Venta, VentaItem, Producto, Cliente, Caja, Credito};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class VentaController extends Controller
{
    public function index(Request $request)
    {
        $perPage = (int) $request->input('per_page', 25);
        if (!in_array($perPage, [10, 25, 50, 100])) $perPage = 25;

        $query = Venta::with(['cliente','usuario','items'])->latest('created_at');

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function($b) use ($q) {
                $b->where('codigo', 'like', "%{$q}%")
                  ->orWhereHas('cliente', fn($c) => $c->where('nombre', 'like', "%{$q}%"));
            });
        }

        $ventas = $query->paginate($perPage)->withQueryString();
        return view('ventas.index', compact('ventas', 'perPage'));
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
            $p = Producto::find($item['producto_id'] ?? $item['id'] ?? null);
            if (!$p || $p->stock < $item['cantidad']) {
                $nombre = $item['nombre'] ?? 'producto';
                return response()->json(['error'=>"Stock insuficiente: {$nombre}"],422);
            }
        }

        $ventaId = null;
        DB::transaction(function () use ($request, $items, &$ventaId) {
            $cliente = $request->filled('cliente_id') ? Cliente::find($request->input('cliente_id')) : null;

            // El precio de venta ya incluye el IVA (13%) integrado para que el cliente pague el precio exhibido.
            $descuento = (float) $request->input('descuento', 0);
            $montoBrutoTotal = collect($items)->sum(function($i) {
                return (float)($i['subtotal'] ?? ((float)$i['precio'] * (int)$i['cantidad']));
            });
            $montoConDescuento = max(0, $montoBrutoTotal - $descuento);

            // Separación de productos exentos y gravados
            $exenta = collect($items)->filter(fn($i) => !empty($i['exento']))->sum(fn($i) => (float)($i['subtotal'] ?? ((float)$i['precio'] * (int)$i['cantidad'])));
            $montoGravadoConIva = max(0, $montoConDescuento - $exenta);

            // Desglose fiscal de IVA 13% integrado: Base = Total / 1.13 | IVA = Total - Base
            $gravado = round($montoGravadoConIva / 1.13, 2);
            $impuesto = round($montoGravadoConIva - $gravado, 2);
            $subtotal = $gravado + $exenta;

            $percepcion = ($cliente && $cliente->aplica_percepcion) ? round($gravado * 0.01, 2) : 0;
            $retencion  = ($cliente && $cliente->retiene_iva)       ? round($gravado * 0.01, 2) : 0;

            // Total exacto a pagar (precio neto con IVA integrado)
            $total = max(0, $montoConDescuento + $percepcion - $retencion);
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
                    'producto_id'     => $item['producto_id'] ?? $item['id'],
                    'nombre_producto' => $item['nombre'] ?? '',
                    'cantidad'        => (int)$item['cantidad'],
                    'precio_unitario' => (float)$item['precio'],
                    'descuento'       => (float)($item['descuento_item'] ?? 0),
                    'subtotal'        => (float)($item['subtotal'] ?? (float)$item['precio'] * (int)$item['cantidad']),
                ]);
                Producto::where('id',$item['producto_id'] ?? $item['id'])->decrement('stock',(int)$item['cantidad']);
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

        if ($request->expectsJson() || $request->ajax()) {
            return response()->json(['ok'=>true,'id'=>$ventaId]);
        }
        return redirect()->route('ventas.ver', $ventaId)->with('success', 'Venta registrada correctamente.');
    }

    
    public function pdf(int $id)
    {
        $venta = Venta::with(['cliente','usuario','items'])->findOrFail($id);
        $config = \App\Models\Configuracion::pluck('valor','clave')->toArray();
        return view('ventas.pdf', compact('venta','config'));
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
