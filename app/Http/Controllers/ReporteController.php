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
            ->where('created_at', '>=', now()->subMonths(6)->startOfMonth())
            ->get(['created_at','total'])
            ->groupBy(fn($v) => $v->created_at->format('Y-m'))
            ->map(fn($grupo, $key) => (object)[
                'anio'   => (int) substr($key, 0, 4),
                'mes'    => (int) substr($key, 5, 2),
                'ventas' => $grupo->count(),
                'total'  => $grupo->sum('total'),
            ])
            ->sortByDesc(fn($v, $key) => $key)
            ->values();

        $creditosPendientes = Credito::where('estado','!=','pagado')->sum('saldo');

        return view('reportes.index', compact(
            'ventasSemana','topProductos','ventasPorMetodo',
            'resumenMensual','creditosPendientes'
        ));
    }

    
    public function estadisticas(Request $request)
    {
        $dias = (int) $request->get('dias', 30);
        if (!in_array($dias, [7, 15, 30, 90, 365])) {
            $dias = 30;
        }

        $fechaInicio = now()->subDays($dias)->startOfDay();

        // Ventas por día
        $ventasPorDia = Venta::where('estado', 'completada')
            ->where('created_at', '>=', $fechaInicio)
            ->selectRaw('DATE(created_at) as fecha, COUNT(*) as transacciones, SUM(total) as total')
            ->groupByRaw('DATE(created_at)')
            ->orderBy('fecha')
            ->get();

        // Top 10 productos
        $topProductos = DB::table('venta_items as vi')
            ->join('ventas as v', 'vi.venta_id', '=', 'v.id')
            ->where('v.estado', 'completada')
            ->where('v.created_at', '>=', $fechaInicio)
            ->selectRaw('vi.nombre_producto, SUM(vi.cantidad) as total_vendido, SUM(vi.subtotal) as ingresos')
            ->groupBy('vi.producto_id', 'vi.nombre_producto')
            ->orderByDesc('total_vendido')
            ->limit(10)
            ->get();

        // Métodos de pago
        $metodosPago = Venta::where('estado', 'completada')
            ->where('created_at', '>=', $fechaInicio)
            ->selectRaw('metodo_pago, COUNT(*) as transacciones, SUM(total) as total')
            ->groupBy('metodo_pago')
            ->get();

        // Comparativa mensual últimos 12 meses: Ventas vs Compras
        $meses = [];
        for ($i = 11; $i >= 0; $i--) {
            $mesDate   = now()->subMonths($i);
            $mesNombre = $mesDate->translatedFormat('M Y');

            $totalV = Venta::where('estado', 'completada')
                ->whereMonth('created_at', $mesDate->month)
                ->whereYear('created_at', $mesDate->year)
                ->sum('total');

            $totalC = Compra::where('estado', 'completada')
                ->whereMonth('created_at', $mesDate->month)
                ->whereYear('created_at', $mesDate->year)
                ->sum('total');

            $meses[] = [
                'periodo' => $mesNombre,
                'ventas'  => (float) $totalV,
                'compras' => (float) $totalC,
                'margen'  => (float) ($totalV - $totalC),
            ];
        }

        // Totales globales en el período
        $totalIngresos      = Venta::where('estado', 'completada')->where('created_at', '>=', $fechaInicio)->sum('total');
        $totalTransacciones = Venta::where('estado', 'completada')->where('created_at', '>=', $fechaInicio)->count();
        $ticketPromedio     = $totalTransacciones > 0 ? round($totalIngresos / $totalTransacciones, 2) : 0;
        $totalCompras       = Compra::where('estado', 'completada')->where('created_at', '>=', $fechaInicio)->sum('total');

        return view('reportes.estadisticas', compact(
            'dias',
            'ventasPorDia',
            'topProductos',
            'metodosPago',
            'meses',
            'totalIngresos',
            'totalTransacciones',
            'ticketPromedio',
            'totalCompras'
        ));
    }

    
    public function hojaConteoPdf()
    {
        $productos = Producto::activo()
            ->with(['categoria','subcategoria'])
            ->orderBy('categoria_id')
            ->orderBy('nombre')
            ->get();
        $config = \App\Models\Configuracion::pluck('valor','clave')->toArray();
        return view('reportes.hoja_conteo_pdf', compact('productos', 'config'));
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

    public function kardexBuscar(Request $request)
    {
        $this->requireAdmin();
        $productos = collect();

        if ($request->filled('q')) {
            $productos = Producto::activo()
                ->where('nombre', 'like', '%'.$request->q.'%')
                ->orWhere('sku', 'like', '%'.$request->q.'%')
                ->orderBy('nombre')->limit(20)->get();
        }

        $desde = $request->get('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->get('hasta', today()->toDateString());

        return view('reportes.kardex_buscar', compact('productos','desde','hasta'));
    }

    public function kardex(int $id, Request $request)
    {
        $producto = Producto::with('categoria')->findOrFail($id);

        $desde = $request->get('desde');
        $hasta = $request->get('hasta');

        $movimientos = $this->movimientosKardex($id, $desde, $hasta);

        // Saldo inicial: stock acumulado ANTES de la fecha "desde" (para que
        // el saldo corrido dentro del rango parta del valor correcto).
        $saldoInicial = 0;
        if ($desde) {
            $previos = $this->movimientosKardex($id, null, null)
                ->filter(fn($m) => \Carbon\Carbon::parse($m->fecha)->lt(\Carbon\Carbon::parse($desde)->startOfDay()));
            foreach ($previos as $p) {
                $saldoInicial += $p->delta;
            }
        }

        $saldo = $saldoInicial;
        $movimientos = $movimientos->map(function ($m) use (&$saldo) {
            $saldo += $m->delta;
            $m->saldo = $saldo;
            return $m;
        });

        return view('reportes.kardex', compact('producto','movimientos','desde','hasta','saldoInicial'));
    }

    
    public function kardexPdf(int $id, Request $request)
    {
        $producto = Producto::with('categoria')->findOrFail($id);
        $desde = $request->get('desde', now()->startOfMonth()->toDateString());
        $hasta = $request->get('hasta', today()->toDateString());

        $movimientos = $this->movimientosKardex($id, $desde, $hasta);

        $saldoInicial = 0;
        if ($desde) {
            $previos = $this->movimientosKardex($id, null, null)
                ->filter(fn($m) => \Carbon\Carbon::parse($m->fecha)->lt(\Carbon\Carbon::parse($desde)->startOfDay()));
            foreach ($previos as $p) {
                $saldoInicial += $p->delta;
            }
        }

        $saldo = $saldoInicial;
        $movimientos = $movimientos->map(function ($m) use (&$saldo) {
            $saldo += $m->delta;
            $m->saldo = $saldo;
            return $m;
        });

        $config = \App\Models\Configuracion::pluck('valor','clave')->toArray();
        return view('reportes.kardex_pdf', compact('producto', 'movimientos', 'desde', 'hasta', 'saldoInicial', 'config'));
    }

    public function kardexExcel(int $id, Request $request)
    {
        $producto    = Producto::findOrFail($id);
        $movimientos = $this->kardex($id, $request)->getData()['movimientos'];

        $filename = 'kardex_' . \Illuminate\Support\Str::slug($producto->nombre) . '.xls';

        $html  = '<table border="1"><tr>'
               . '<th>Fecha</th><th>Tipo</th><th>Referencia</th><th>Cantidad</th><th>Precio Unit.</th><th>Usuario</th><th>Saldo</th></tr>';
        foreach ($movimientos as $m) {
            $html .= '<tr>'
                   . '<td>' . \Carbon\Carbon::parse($m->fecha)->format('d/m/Y H:i') . '</td>'
                   . '<td>' . e($m->tipo) . '</td>'
                   . '<td>' . e($m->referencia) . '</td>'
                   . '<td>' . $m->cantidad . '</td>'
                   . '<td>' . number_format($m->precio_unitario, 2) . '</td>'
                   . '<td>' . e($m->usuario) . '</td>'
                   . '<td>' . $m->saldo . '</td>'
                   . '</tr>';
        }
        $html .= '</table>';

        return response($html, 200, [
            'Content-Type'        => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }

    /** Trae las 3 fuentes de movimiento de un producto (compras, ventas, traslados), sin ordenar/acumular. */
    private function movimientosKardex(int $id, ?string $desde, ?string $hasta)
    {
        $entradas = DB::table('compra_items as ci')
            ->join('compras as c','ci.compra_id','=','c.id')
            ->join('usuarios as u','c.usuario_id','=','u.id')
            ->where('ci.producto_id', $id)
            ->where('c.estado','completada')
            ->when($desde, fn($q) => $q->whereDate('c.created_at','>=',$desde))
            ->when($hasta, fn($q) => $q->whereDate('c.created_at','<=',$hasta))
            ->selectRaw("c.created_at as fecha, 'Compra' as tipo, c.codigo as referencia, ci.cantidad, ci.cantidad as delta, ci.precio_unitario, u.nombre as usuario")
            ->get();

        $salidas = DB::table('venta_items as vi')
            ->join('ventas as v','vi.venta_id','=','v.id')
            ->join('usuarios as u','v.usuario_id','=','u.id')
            ->where('vi.producto_id', $id)
            ->where('v.estado','completada')
            ->when($desde, fn($q) => $q->whereDate('v.created_at','>=',$desde))
            ->when($hasta, fn($q) => $q->whereDate('v.created_at','<=',$hasta))
            ->selectRaw("v.created_at as fecha, 'Venta' as tipo, v.codigo as referencia, vi.cantidad, -vi.cantidad as delta, vi.precio_unitario, u.nombre as usuario")
            ->get();

        $traslados = DB::table('traslado_items as ti')
            ->join('traslados as t','ti.traslado_id','=','t.id')
            ->join('usuarios as u','t.usuario_id','=','u.id')
            ->where('ti.producto_id', $id)
            ->when($desde, fn($q) => $q->whereDate('t.created_at','>=',$desde))
            ->when($hasta, fn($q) => $q->whereDate('t.created_at','<=',$hasta))
            ->selectRaw("t.created_at as fecha, CONCAT('Traslado ',t.tipo) as tipo, t.concepto as referencia, ti.cantidad_ajuste as cantidad, ti.cantidad_ajuste as delta, 0 as precio_unitario, u.nombre as usuario")
            ->get();

        return $entradas->merge($salidas)->merge($traslados)->sortBy('fecha')->values();
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
            'Content-Disposition' => 'attachment; filename="' . $filename . '"',
        ]);
    }
}
