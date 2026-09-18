<?php

namespace App\Http\Controllers;

use App\Models\{Producto, Lote, Traslado, TrasladoItem, AuditoriaActividad};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class InventarioController extends Controller
{
    // ── Consultar Stock ──────────────────────────────────────
    public function stock()
    {
        $productos = Producto::activo()
            ->with(['categoria', 'subcategoria', 'proveedor'])
            ->orderBy('nombre')
            ->get();
        return view('inventario.stock', compact('productos'));
    }

    // ── Consulta de Stock por Lote ───────────────────────────
    public function stockLote()
    {
        $lotes = Lote::with(['producto.categoria'])
            ->where('cantidad_actual', '>', 0)
            ->orderBy('fecha_vencimiento')
            ->paginate(30);
        return view('inventario.stock_lote', compact('lotes'));
    }

    // ── Tabla de Márgenes (Rentabilidad) ─────────────────────
    public function margenes()
    {
        $productos = Producto::activo()
            ->with(['categoria'])
            ->orderBy('nombre')
            ->paginate(25);

        return view('inventario.margenes', compact('productos'));
    }

    // ── Control de Vencimientos ──────────────────────────────
    public function vencimientos()
    {
        $lotesVencidos = Lote::with(['producto.categoria'])
            ->where('cantidad_actual', '>', 0)
            ->whereDate('fecha_vencimiento', '<', today())
            ->get();

        $lotesProximos = Lote::with(['producto.categoria'])
            ->where('cantidad_actual', '>', 0)
            ->whereDate('fecha_vencimiento', '>=', today())
            ->whereDate('fecha_vencimiento', '<=', today()->addDays(30))
            ->get();

        $lotesVigentes = Lote::with(['producto.categoria'])
            ->where('cantidad_actual', '>', 0)
            ->whereDate('fecha_vencimiento', '>', today()->addDays(30))
            ->get();

        return view('inventario.vencimientos', compact('lotesVencidos', 'lotesProximos', 'lotesVigentes'));
    }

    // ── Traslados / Ajustes de Inventario ───────────────────
    public function traslados()
    {
        $traslados = Traslado::with(['usuario', 'items.producto'])
            ->latest('created_at')
            ->paginate(20);
        $productos = Producto::activo()->orderBy('nombre')->get(['id', 'nombre', 'sku', 'stock']);
        return view('inventario.traslados', compact('traslados', 'productos'));
    }

    public function storeTraslado(Request $request)
    {
        $request->validate([
            'concepto'            => 'required|string|max:200',
            'tipo'                => 'required|in:entrada,salida,ajuste',
            'items'               => 'required|array|min:1',
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad'    => 'required|integer|min:1',
        ]);

        DB::transaction(function () use ($request) {
            $traslado = Traslado::create([
                'usuario_id' => $this->userId(),
                'concepto'   => $request->concepto,
                'tipo'       => $request->tipo,
                'notas'      => $request->notas,
            ]);

            foreach ($request->items as $item) {
                $producto = Producto::lockForUpdate()->findOrFail($item['producto_id']);
                $cantidadAntes = $producto->stock;

                if ($request->tipo === 'entrada') {
                    $nuevaCantidad = $cantidadAntes + $item['cantidad'];
                } elseif ($request->tipo === 'salida') {
                    $nuevaCantidad = max(0, $cantidadAntes - $item['cantidad']);
                } else { // ajuste
                    $nuevaCantidad = $item['cantidad'];
                }

                $ajuste = $nuevaCantidad - $cantidadAntes;

                TrasladoItem::create([
                    'traslado_id'      => $traslado->id,
                    'producto_id'      => $producto->id,
                    'cantidad_antes'   => $cantidadAntes,
                    'cantidad_ajuste'  => $ajuste,
                    'cantidad_despues' => $nuevaCantidad,
                    'notas'            => $item['notas'] ?? null,
                ]);

                $producto->update(['stock' => $nuevaCantidad]);
            }
        });

        AuditoriaActividad::registrar(
            'Inventario',
            'Ajuste/Traslado de Stock',
            "Concepto: {$request->concepto} | Tipo: {$request->tipo}"
        );

        return redirect()->route('inventario.traslados')
            ->with('success', 'Traslado/ajuste registrado correctamente.');
    }
}
