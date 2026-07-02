<?php
namespace App\Http\Controllers;

use App\Models\{Venta, Compra, Producto, Credito};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ReporteController extends Controller
{
    public function index()
    {
        $this->requireAdmin();

        $ventasSemana = Venta::where('estado','completada')
            ->where('created_at','>=', now()->subDays(30))
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as cantidad, SUM(total) as total')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('fecha')->get();

        $topProductos = DB::table('venta_items as vi')
            ->join('ventas as v','vi.venta_id','=','v.id')
            ->where('v.estado','completada')
            ->selectRaw('vi.nombre_producto, SUM(vi.cantidad) as total_vendido, SUM(vi.subtotal) as ingresos')
            ->groupBy('vi.producto_id','vi.nombre_producto')
            ->orderByDesc('total_vendido')->limit(10)->get();

        $ventasPorMetodo = Venta::where('estado','completada')
            ->whereMonth('created_at', now()->month)
            ->selectRaw('metodo_pago, COUNT(*) as cantidad, SUM(total) as total')
            ->groupBy('metodo_pago')->get();

        $resumenMensual = Venta::where('estado','completada')
            ->selectRaw('YEAR(created_at) as anio, MONTH(created_at) as mes, COUNT(*) as ventas, SUM(total) as total')
            ->groupByRaw('YEAR(created_at), MONTH(created_at)')
            ->orderByRaw('anio DESC, mes DESC')->limit(6)->get();

        $creditosPendientes = Credito::where('estado','!=','pagado')->sum('saldo');

        return view('reportes.index', compact(
            'ventasSemana','topProductos','ventasPorMetodo',
            'resumenMensual','creditosPendientes'
        ));
    }

    public function hojaConteo()
    {
        $productos = Producto::activo()
            ->with(['categoria','subcategoria'])
            ->orderBy('categoria_id')
            ->orderBy('nombre')
            ->get();
        return view('reportes.hoja_conteo', compact('productos'));
    }

    public function kardex(int $id)
    {
        $producto = Producto::with('categoria')->findOrFail($id);

        // Movimientos: compras (entradas)
        $entradas = DB::table('compra_items as ci')
            ->join('compras as c','ci.compra_id','=','c.id')
            ->join('usuarios as u','c.usuario_id','=','u.id')
            ->where('ci.producto_id', $id)
            ->where('c.estado','completada')
            ->selectRaw("c.created_at as fecha, 'Compra' as tipo, c.codigo as referencia, ci.cantidad, ci.precio_unitario, u.nombre as usuario")
            ->get();

        // Ventas (salidas)
        $salidas = DB::table('venta_items as vi')
            ->join('ventas as v','vi.venta_id','=','v.id')
            ->join('usuarios as u','v.usuario_id','=','u.id')
            ->where('vi.producto_id', $id)
            ->where('v.estado','completada')
            ->selectRaw("v.created_at as fecha, 'Venta' as tipo, v.codigo as referencia, vi.cantidad, vi.precio_unitario, u.nombre as usuario")
            ->get();

        // Traslados
        $traslados = DB::table('traslado_items as ti')
            ->join('traslados as t','ti.traslado_id','=','t.id')
            ->join('usuarios as u','t.usuario_id','=','u.id')
            ->where('ti.producto_id', $id)
            ->selectRaw("t.created_at as fecha, CONCAT('Traslado ',t.tipo) as tipo, t.concepto as referencia, ti.cantidad_ajuste as cantidad, 0 as precio_unitario, u.nombre as usuario")
            ->get();

        // Combinar y ordenar cronológicamente
        $movimientos = $entradas->merge($salidas)->merge($traslados)
            ->sortBy('fecha')->values();

        return view('reportes.kardex', compact('producto','movimientos'));
    }

    public function exportCsv(Request $request)
    {
        $this->requireAdmin();
        $desde = $request->get('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->get('hasta', today()->toDateString());

        $filas = DB::table('ventas as v')
            ->leftJoin('clientes as c','v.cliente_id','=','c.id')
            ->join('usuarios as u','v.usuario_id','=','u.id')
            ->whereBetween(DB::raw('DATE(v.created_at)'),[$desde,$hasta])
            ->orderByDesc('v.created_at')
            ->select('v.codigo','v.created_at','c.nombre as cliente','u.nombre as vendedor',
                     'v.subtotal','v.descuento','v.total','v.metodo_pago','v.estado')
            ->get();

        $filename = "ventas_{$desde}_{$hasta}.csv";
        return response()->stream(function() use ($filas) {
            $h = fopen('php://output','w');
            fputs($h, chr(0xEF).chr(0xBB).chr(0xBF));
            fputcsv($h,['Código','Fecha','Cliente','Vendedor','Subtotal','Descuento','Total','Pago','Estado']);
            foreach($filas as $f) fputcsv($h,(array)$f);
            fclose($h);
        },200,[
            'Content-Type'        => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename="{$filename}"",
        ]);
    }
}
