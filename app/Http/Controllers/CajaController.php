<?php
namespace App\Http\Controllers;

use App\Models\{Caja, Gasto, CorteCaja, Venta, MovimientoCaja, VoucherPos, CuadraturaCaja, AuditoriaActividad};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    public function index()
    {
        $caja   = Caja::abierta();
        $gastos = $caja ? Gasto::where('caja_id', $caja->id)->latest('created_at')->get() : collect();
        return view('caja.index', compact('caja', 'gastos'));
    }

    // ── CAJA CHICA AVANZADA (Segunda Vista / Modulo Especial) ───────────
    public function cajaChica(Request $request)
    {
        $caja = Caja::abierta();
        $fecha = $request->get('fecha', date('Y-m-d'));
        $verTodos = $request->boolean('ver_todos', false);
        $rol = session('user.rol');

        $query = MovimientoCaja::with('usuario')
            ->whereDate('created_at', $fecha);

        // Si no es admin o no activó 'ver_todos', filtra solo sus movimientos
        if ($rol !== 'admin' || !$verTodos) {
            $query->where('usuario_id', $this->userId());
        }

        $movimientos = $query->latest('created_at')->paginate(25);
        $vouchers = VoucherPos::whereDate('created_at', $fecha)->latest('created_at')->get();

        // Métricas de Caja Chica
        $totalIngresos = MovimientoCaja::whereDate('created_at', $fecha)->where('tipo', 'ingreso')->sum('monto');
        $totalEgresos  = MovimientoCaja::whereDate('created_at', $fecha)->where('tipo', 'egreso')->sum('monto');
        $totalVouchers = VoucherPos::whereDate('created_at', $fecha)->sum('monto');

        return view('caja.chica', compact(
            'caja',
            'movimientos',
            'vouchers',
            'fecha',
            'verTodos',
            'totalIngresos',
            'totalEgresos',
            'totalVouchers'
        ));
    }

    public function storeMovimiento(Request $request)
    {
        $request->validate([
            'tipo'        => 'required|in:ingreso,egreso',
            'categoria'   => 'required|string|max:100',
            'descripcion' => 'required|string|max:255',
            'monto'       => 'required|numeric|min:0.01',
            'comprobante' => 'nullable|string|max:100',
            'pago_a'      => 'nullable|string|max:150',
        ]);

        $caja = Caja::abierta();
        $movimiento = MovimientoCaja::create([
            'caja_id'     => $caja?->id,
            'usuario_id'  => $this->userId(),
            'tipo'        => $request->tipo,
            'categoria'   => $request->categoria,
            'descripcion' => $request->descripcion,
            'comprobante' => $request->comprobante,
            'monto'       => $request->monto,
            'pago_a'      => $request->pago_a,
            'estado'      => 'aprobado',
        ]);

        if ($caja && $request->tipo === 'egreso') {
            $caja->increment('total_gastos', $request->monto);
        }

        // Registrar en Auditoría en tiempo real para el Admin
        AuditoriaActividad::registrar(
            'Caja Chica',
            'Registro ' . ucfirst($request->tipo),
            "{$request->categoria}: {$request->descripcion} (Pago a: " . ($request->pago_a ?? 'N/A') . ")",
            $request->monto
        );

        return back()->with('success', '✅ Movimiento de ' . $request->tipo . ' registrado correctamente.');
    }

    public function storeVoucher(Request $request)
    {
        $request->validate([
            'banco'          => 'required|string|max:100',
            'numero_lote'    => 'required|string|max:50',
            'numero_voucher' => 'required|string|max:50',
            'monto'          => 'required|numeric|min:0.01',
            'comision'       => 'nullable|numeric|min:0',
        ]);

        $caja = Caja::abierta();
        $monto = (float) $request->monto;
        $comisionPorcentaje = (float) ($request->comision ?? 2.5); // 2.5% por defecto si no se especifica
        $comisionMonto = round(($monto * $comisionPorcentaje) / 100, 2);
        $montoNeto = $monto - $comisionMonto;

        VoucherPos::create([
            'caja_id'             => $caja?->id,
            'usuario_id'          => $this->userId(),
            'banco'               => $request->banco,
            'numero_lote'         => $request->numero_lote,
            'numero_voucher'      => $request->numero_voucher,
            'monto'               => $monto,
            'comision_porcentaje' => $comisionPorcentaje,
            'comision_monto'      => $comisionMonto,
            'monto_neto'          => $montoNeto,
            'estado'              => 'registrado',
        ]);

        AuditoriaActividad::registrar(
            'Vouchers POS',
            'Registro Voucher Tarjeta',
            "Banco: {$request->banco} | Lote: {$request->numero_lote} | Voucher: {$request->numero_voucher}",
            $monto
        );

        return back()->with('success', '💳 Voucher de tarjeta registrado correctamente.');
    }

    public function cuadraturasIndex()
    {
        $caja = Caja::abierta();
        $cuadraturas = CuadraturaCaja::with(['caja', 'usuario'])->latest('created_at')->paginate(20);

        // Totales de hoy del sistema
        $efectivoSistema = $caja ? Venta::where('caja_id', $caja->id)->where('estado', 'completada')->where('metodo_pago', 'efectivo')->sum('total') : 0;
        $tarjetaSistema  = $caja ? Venta::where('caja_id', $caja->id)->where('estado', 'completada')->where('metodo_pago', 'tarjeta')->sum('total') : 0;
        $vouchersRegistrados = $caja ? VoucherPos::where('caja_id', $caja->id)->sum('monto') : 0;

        return view('caja.cuadraturas', compact(
            'caja',
            'cuadraturas',
            'efectivoSistema',
            'tarjetaSistema',
            'vouchersRegistrados'
        ));
    }

    public function storeCuadratura(Request $request)
    {
        $caja = Caja::abierta();
        if (!$caja) return back()->with('error', 'No hay caja abierta para realizar cuadratura.');

        $request->validate([
            'efectivo_real'         => 'required|numeric|min:0',
            'tarjeta_real_vouchers' => 'required|numeric|min:0',
        ]);

        $efectivoSistema = Venta::where('caja_id', $caja->id)->where('estado', 'completada')->where('metodo_pago', 'efectivo')->sum('total');
        $tarjetaSistema  = Venta::where('caja_id', $caja->id)->where('estado', 'completada')->where('metodo_pago', 'tarjeta')->sum('total');

        $difEfectivo = $request->efectivo_real - $efectivoSistema;
        $difTarjeta  = $request->tarjeta_real_vouchers - $tarjetaSistema;
        $diferenciaTotal = $difEfectivo + $difTarjeta;

        $estadoCuadratura = ($diferenciaTotal == 0) ? 'cuadrada' : 'con_descuadre';

        CuadraturaCaja::create([
            'caja_id'               => $caja->id,
            'usuario_id'            => $this->userId(),
            'efectivo_sistema'      => $efectivoSistema,
            'efectivo_real'         => $request->efectivo_real,
            'tarjeta_sistema'       => $tarjetaSistema,
            'tarjeta_real_vouchers' => $request->tarjeta_real_vouchers,
            'diferencia'            => $diferenciaTotal,
            'estado_cuadratura'     => $estadoCuadratura,
            'observaciones'         => $request->observaciones,
        ]);

        AuditoriaActividad::registrar(
            'Cuadratura Caja',
            'Arqueo y Conciliación',
            "Estado: {$estadoCuadratura} | Diferencia: $" . number_format($diferenciaTotal, 2),
            $diferenciaTotal
        );

        return back()->with('success', '⚖️ Cuadratura de caja procesada correctamente.');
    }

    public function abrir(Request $request)
    {
        if (Caja::abierta()) return back()->with('error', 'Ya hay una caja abierta.');
        $request->validate(['monto_apertura' => 'required|numeric|min:0']);
        $caja = Caja::create([
            'nombre'          => $request->nombre ?? 'Caja Principal',
            'usuario_id'      => $this->userId(),
            'monto_apertura'  => $request->monto_apertura,
            'notas_apertura'  => $request->notas_apertura,
        ]);

        AuditoriaActividad::registrar('Caja', 'Apertura de Caja', 'Fondo Inicial de Caja', $request->monto_apertura);

        return back()->with('success', '✅ Caja abierta correctamente.');
    }

    public function cerrar(Request $request)
    {
        $caja = Caja::abierta();
        if (!$caja) return back()->with('error', 'No hay caja abierta.');
        $request->validate(['monto_cierre' => 'required|numeric|min:0']);

        $esperado   = $caja->balance_estimado;
        $contado    = (float) $request->monto_cierre;
        $diferencia = $contado - $esperado;

        // Desglose por método de pago
        $desglose = Venta::where('caja_id', $caja->id)->where('estado', 'completada')
            ->selectRaw('metodo_pago, SUM(total) as total')
            ->groupBy('metodo_pago')->pluck('total', 'metodo_pago');

        DB::transaction(function () use ($caja, $request, $esperado, $contado, $diferencia, $desglose) {
            CorteCaja::create([
                'caja_id'              => $caja->id,
                'usuario_id'           => $this->userId(),
                'efectivo_esperado'    => $esperado,
                'efectivo_contado'     => $contado,
                'diferencia'           => $diferencia,
                'ventas_efectivo'      => $desglose['efectivo'] ?? 0,
                'ventas_tarjeta'       => $desglose['tarjeta'] ?? 0,
                'ventas_transferencia' => $desglose['transferencia'] ?? 0,
                'ventas_credito'       => $desglose['credito'] ?? 0,
                'denominaciones'       => $request->denominaciones ?? null,
                'notas'                => $request->notas_cierre,
            ]);
            $caja->update([
                'monto_cierre' => $contado,
                'estado'       => 'cerrada',
                'cerrada_at'   => now(),
                'notas_cierre' => $request->notas_cierre,
            ]);
        });

        AuditoriaActividad::registrar('Caja', 'Cierre de Caja', 'Cierre y arqueo final de turno', $contado);

        return redirect()->route('caja.corte', $caja->id)
            ->with('success', 'Caja cerrada. Revisa el corte.');
    }

    public function registrarGasto(Request $request)
    {
        $request->validate([
            'concepto' => 'required|string|max:200',
            'monto'    => 'required|numeric|min:0.01',
            'tipo'     => 'required|in:egreso,retiro,ingreso',
        ]);
        $caja = Caja::abierta();
        Gasto::create([
            'caja_id'    => $caja?->id,
            'usuario_id' => $this->userId(),
            'concepto'   => $request->concepto,
            'monto'      => $request->monto,
            'tipo'       => $request->tipo,
        ]);
        if ($caja && in_array($request->tipo, ['egreso', 'retiro'])) {
            $caja->increment('total_gastos', $request->monto);
        }

        AuditoriaActividad::registrar('Caja', 'Gasto Rápido', $request->concepto, $request->monto);

        return back()->with('success', 'Movimiento registrado.');
    }

    public function corte(int $cajaId)
    {
        $caja  = Caja::with(['usuario', 'cortes.usuario', 'ventas.cliente'])->findOrFail($cajaId);
        $corte = $caja->cortes->last();
        return view('caja.corte', compact('caja', 'corte'));
    }

    public function historial()
    {
        $cajas = Caja::with(['usuario', 'cortes'])->latest('abierta_at')->paginate(15);
        return view('caja.historial', compact('cajas'));
    }

    public function movimientos(Request $request)
    {
        $query = Gasto::with(['usuario','caja'])->latest('created_at');

        if ($request->filled('buscar')) {
            $query->where('concepto', 'like', '%'.$request->buscar.'%');
        }
        if ($request->filled('caja_id')) {
            $query->where('caja_id', $request->caja_id);
        }
        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->desde);
        }
        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->hasta);
        }

        $movimientos = $query->paginate(20)->withQueryString();
        $caja        = Caja::abierta();
        $cajas       = Caja::orderByDesc('abierta_at')->get(['id','nombre']);

        return view('caja.movimientos', compact('movimientos','caja','cajas'));
    }

    public function eliminarMovimiento(int $id)
    {
        $gasto = Gasto::findOrFail($id);

        // Si la caja del movimiento sigue abierta, revertir el impacto en total_gastos
        if ($gasto->caja && $gasto->caja->estado === 'abierta' && in_array($gasto->tipo, ['egreso','retiro'])) {
            $gasto->caja->decrement('total_gastos', $gasto->monto);
        }

        $gasto->delete();

        return back()->with('success', 'Movimiento eliminado correctamente.');
    }
}
