<?php
namespace App\Http\Controllers;

use App\Models\{Compra, CompraItem, Producto, Proveedor};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index()
    {
        $this->requireAdmin();
        $compras = Compra::with(['proveedor','usuario'])->latest('created_at')->paginate(20);
        return view('compras.index', compact('compras'));
    }

    public function create()
    {
        $this->requireAdmin();
        $proveedores = Proveedor::orderBy('nombre')->get();
        $productos   = Producto::activo()->orderBy('nombre')->get(['id','nombre','sku','precio_compra','stock']);
        return view('compras.form', compact('proveedores','productos'));
    }

    public function store(Request $request)
    {
        $this->requireAdmin();
        $items = json_decode($request->input('items','[]'), true);
        if (empty($items)) {
            return response()->json(['error' => 'Agrega al menos un producto.'], 422);
        }

        DB::transaction(function () use ($request, $items) {
            $total = collect($items)->sum('subtotal');

            $compra = Compra::create([
                'codigo'       => 'COM-' . strtoupper(uniqid()),
                'proveedor_id' => $request->input('proveedor_id') ?: null,
                'usuario_id'   => $this->userId(),
                'total'        => $total,
                'estado'       => 'completada',
                'notas'        => $request->input('notas'),
            ]);

            foreach ($items as $item) {
                CompraItem::create([
                    'compra_id'       => $compra->id,
                    'producto_id'     => $item['producto_id'],
                    'nombre_producto' => $item['nombre'],
                    'cantidad'        => (int) $item['cantidad'],
                    'precio_unitario' => (float) $item['precio'],
                    'subtotal'        => (float) $item['subtotal'],
                ]);
                // Actualizar stock Y precio de compra
                Producto::where('id', $item['producto_id'])->update([
                    'stock'         => DB::raw('stock + ' . (int)$item['cantidad']),
                    'precio_compra' => (float) $item['precio'],
                ]);
            }
        });

        return redirect()->route('compras.index')->with('success', 'Compra registrada. Stock actualizado.');
    }

    public function show(int $id)
    {
        $this->requireAdmin();
        $compra = Compra::with(['proveedor','usuario','items'])->findOrFail($id);
        return view('compras.show', compact('compra'));
    }

    public function anular(int $id)
    {
        $this->requireAdmin();
        $compra = Compra::with('items')->findOrFail($id);
        DB::transaction(function () use ($compra) {
            foreach ($compra->items as $item) {
                Producto::where('id', $item->producto_id)
                    ->where('stock', '>=', $item->cantidad)
                    ->decrement('stock', $item->cantidad);
            }
            $compra->update(['estado' => 'anulada']);
        });
        return response()->json(['ok' => true]);
    }
}
