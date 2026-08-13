<?php
namespace App\Http\Controllers;

use App\Models\{Venta, Producto, Caja, Compra, Credito, Cotizacion};
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // KPIs del día
        $totalHoy   = Venta::where('estado','completada')->whereDate('created_at',today())->sum('total');
        $ventasHoy  = Venta::where('estado','completada')->whereDate('created_at',today())->count();
        $gastoHoy   = Compra::where('estado','completada')->whereDate('created_at',today())->sum('total');

        // Comparativa mes
        $ventasMes    = Venta::where('estado','completada')
            ->whereMonth('created_at',now()->month)->whereYear('created_at',now()->year)->sum('total');
        $ventasMesAnt = Venta::where('estado','completada')
            ->whereMonth('created_at',now()->subMonth()->month)
            ->whereYear('created_at',now()->subMonth()->year)->sum('total');
        $variacion = $ventasMesAnt > 0
            ? round((($ventasMes - $ventasMesAnt) / $ventasMesAnt) * 100, 1) : 0;

        // Margen estimado
        $margenHoy = DB::table('venta_items as vi')
            ->join('ventas as v','vi.venta_id','=','v.id')
            ->join('productos as p','vi.producto_id','=','p.id')
            ->where('v.estado','completada')->whereDate('v.created_at',today())
            ->selectRaw('SUM((vi.precio_unitario - p.precio_compra) * vi.cantidad) as margen')
            ->value('margen') ?? 0;

        // Stock bajo
        $stockBajo = Producto::activo()->lowStock()->with('categoria')->get();

        // Créditos vencidos/pendientes
        $creditosPendientes = Credito::where('estado','pendiente')->count();
        $creditosVencidos   = Credito::where('estado','vencido')->count();

        // Cotizaciones vigentes
        $cotizacionesVigentes = Cotizacion::where('estado','vigente')->count();

        // Gráfica 7 días
        $ventasSemana = Venta::where('estado','completada')
            ->where('created_at','>=',now()->subDays(6)->startOfDay())
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as cantidad, SUM(total) as total')
            ->groupByRaw('DATE(created_at)')->orderBy('fecha')->get();

        // Top 5 productos del mes
        $topProductos = DB::table('venta_items as vi')
            ->join('ventas as v','vi.venta_id','=','v.id')
            ->where('v.estado','completada')->whereMonth('v.created_at',now()->month)
            ->selectRaw('vi.nombre_producto, SUM(vi.cantidad) as total_vendido, SUM(vi.subtotal) as ingresos')
            ->groupBy('vi.producto_id','vi.nombre_producto')
            ->orderByDesc('total_vendido')->limit(5)->get();

        // Últimas ventas
        $ultimasVentas = Venta::with(['cliente','usuario'])
            ->where('estado','completada')->latest('created_at')->limit(6)->get();

        // Caja activa
        $caja = Caja::abierta();

        return view('inicio.index', compact(
            'totalHoy','ventasHoy','gastoHoy','margenHoy',
            'ventasMes','ventasMesAnt','variacion',
            'stockBajo','creditosPendientes','creditosVencidos',
            'cotizacionesVigentes','ventasSemana','topProductos',
            'ultimasVentas','caja'
        ));
    }
}
