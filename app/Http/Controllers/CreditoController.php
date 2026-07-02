<?php
namespace App\Http\Controllers;

use App\Models\{Credito, PagoCredito, Cliente};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CreditoController extends Controller
{
    public function index()
    {
        // Actualizar créditos vencidos automáticamente
        Credito::where('estado','pendiente')
            ->whereNotNull('fecha_vencimiento')
            ->where('fecha_vencimiento','<', today())
            ->update(['estado' => 'vencido']);

        $creditos = Credito::with(['cliente','usuario','venta'])
            ->orderByRaw("FIELD(estado,'vencido','pendiente','pagado')")
            ->latest('created_at')
            ->paginate(25);

        $totalPendiente = Credito::where('estado','pendiente')->sum('saldo');
        $totalVencido   = Credito::where('estado','vencido')->sum('saldo');

        return view('creditos.index', compact('creditos','totalPendiente','totalVencido'));
    }

    public function show(int $id)
    {
        $credito = Credito::with(['cliente','usuario','venta.items','pagos.usuario'])->findOrFail($id);
        return view('creditos.show', compact('credito'));
    }

    public function registrarPago(Request $request, int $id)
    {
        $credito = Credito::findOrFail($id);

        if ($credito->estado === 'pagado') {
            return back()->with('error', 'Este crédito ya está pagado.');
        }

        $request->validate([
            'monto'  => 'required|numeric|min:0.01|max:' . $credito->saldo,
            'metodo' => 'required|in:efectivo,tarjeta,transferencia',
        ]);

        DB::transaction(function () use ($request, $credito) {
            PagoCredito::create([
                'credito_id' => $credito->id,
                'usuario_id' => $this->userId(),
                'monto'      => $request->monto,
                'metodo'     => $request->metodo,
                'notas'      => $request->notas,
            ]);

            $nuevoPagado = $credito->monto_pagado + $request->monto;
            $nuevoSaldo  = $credito->monto_total - $nuevoPagado;
            $nuevoEstado = $nuevoSaldo <= 0 ? 'pagado' : 'pendiente';

            $credito->update([
                'monto_pagado' => $nuevoPagado,
                'saldo'        => max(0, $nuevoSaldo),
                'estado'       => $nuevoEstado,
            ]);
        });

        return back()->with('success', 'Pago registrado correctamente.');
    }
}
