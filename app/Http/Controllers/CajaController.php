<?php
namespace App\Http\Controllers;

use App\Models\{Caja, Gasto, CorteCaja, Venta};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    public function index()
    {
        $caja       = Caja::abierta();
        $gastos     = $caja ? Gasto::where('caja_id',$caja->id)->latest('created_at')->get() : collect();
        return view('caja.index', compact('caja','gastos'));
    }

    public function abrir(Request $request)
    {
        if (Caja::abierta()) return back()->with('error','Ya hay una caja abierta.');
        $request->validate(['monto_apertura' => 'required|numeric|min:0']);
        Caja::create([
            'nombre'          => $request->nombre ?? 'Caja Principal',
            'usuario_id'      => $this->userId(),
            'monto_apertura'  => $request->monto_apertura,
            'notas_apertura'  => $request->notas_apertura,
        ]);
        return back()->with('success','✅ Caja abierta correctamente.');
    }

    public function cerrar(Request $request)
    {
        $caja = Caja::abierta();
        if (!$caja) return back()->with('error','No hay caja abierta.');
        $request->validate(['monto_cierre' => 'required|numeric|min:0']);

        $esperado    = $caja->balance_estimado;
        $contado     = (float) $request->monto_cierre;
        $diferencia  = $contado - $esperado;

        // Desglose por método de pago
        $desglose = Venta::where('caja_id',$caja->id)->where('estado','completada')
            ->selectRaw('metodo_pago, SUM(total) as total')
            ->groupBy('metodo_pago')->pluck('total','metodo_pago');

        DB::transaction(function() use ($caja, $request, $esperado, $contado, $diferencia, $desglose) {
            CorteCaja::create([
                'caja_id'               => $caja->id,
                'usuario_id'            => $this->userId(),
                'efectivo_esperado'     => $esperado,
                'efectivo_contado'      => $contado,
                'diferencia'            => $diferencia,
                'ventas_efectivo'       => $desglose['efectivo'] ?? 0,
                'ventas_tarjeta'        => $desglose['tarjeta'] ?? 0,
                'ventas_transferencia'  => $desglose['transferencia'] ?? 0,
                'ventas_credito'        => $desglose['credito'] ?? 0,
                'denominaciones'        => $request->denominaciones ?? null,
                'notas'                 => $request->notas_cierre,
            ]);
            $caja->update([
                'monto_cierre' => $contado,
                'estado'       => 'cerrada',
                'cerrada_at'   => now(),
                'notas_cierre' => $request->notas_cierre,
            ]);
        });

        return redirect()->route('caja.corte', $caja->id)
                         ->with('success','Caja cerrada. Revisa el corte.');
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
        if ($caja && in_array($request->tipo,['egreso','retiro'])) {
            $caja->increment('total_gastos', $request->monto);
        }
        return back()->with('success','Movimiento registrado.');
    }

    public function corte(int $cajaId)
    {
        $caja   = Caja::with(['usuario','cortes.usuario','ventas.cliente'])->findOrFail($cajaId);
        $corte  = $caja->cortes->last();
        return view('caja.corte', compact('caja','corte'));
    }

    public function historial()
    {
        $cajas = Caja::with(['usuario','cortes'])->latest('abierta_at')->paginate(15);
        return view('caja.historial', compact('cajas'));
    }
}
