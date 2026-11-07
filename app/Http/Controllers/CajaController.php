<?php

namespace App\Http\Controllers;

use App\Models\{Caja, Gasto, CorteCaja, Venta, Usuario};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CajaController extends Controller
{
    /**
     * Administrar Cajas fusionado con Corte de Caja:
     * Redirige directamente al panel unificado 'Administrar Corte'
     */
    public function index()
    {
        return redirect()->route('caja.administrar_corte');
    }

    /**
     * Página dedicada para Apertura de Caja y Turno
     */
    public function formApertura(Request $request)
    {
        $cajaAbierta = Caja::abierta();
        if ($cajaAbierta) {
            $cajeroNombre = $cajaAbierta->usuario ? ($cajaAbierta->usuario->nombre ?? $cajaAbierta->usuario->name) : 'otro usuario';
            return redirect()->route('caja.administrar_corte')
                ->with('warning', "Actualmente ya existe una caja abierta ('{$cajaAbierta->nombre}') asignada a {$cajeroNombre}. Debe cerrarse antes de realizar una nueva apertura.");
        }

        $sucursales = ['LAS PALMERAS', 'Sucursal Central', 'Sucursal Norte', 'Sucursal Sur', 'Bodega Principal'];
        $sucursalSeleccionada = $request->get('sucursal', 'LAS PALMERAS');

        $cajas = Caja::orderBy('id')->get();
        if ($cajas->isEmpty()) {
            Caja::create([
                'nombre'          => 'CAJA 2lp',
                'sucursal'        => 'LAS PALMERAS',
                'turno'           => '1',
                'usuario_id'      => $this->userId(),
                'monto_apertura'  => 0,
                'estado'          => 'cerrada',
            ]);
            Caja::create([
                'nombre'          => 'CAJA 1',
                'sucursal'        => 'LAS PALMERAS',
                'turno'           => '1',
                'usuario_id'      => $this->userId(),
                'monto_apertura'  => 0,
                'estado'          => 'cerrada',
            ]);
            $cajas = Caja::orderBy('id')->get();
        }

        $cajaSeleccionadaId = $request->get('caja_id', $cajas->first()->id);
        $usuarioActual = Usuario::find($this->userId());

        return view('caja.apertura', compact(
            'sucursales',
            'sucursalSeleccionada',
            'cajas',
            'cajaSeleccionadaId',
            'usuarioActual'
        ));
    }

    /**
     * Panel Principal Unificado: Administrar Corte (Corte X / Corte Z)
     * Coincide exactamente con la interfaz mostrada en las referencias
     */
    public function administrarCorte(Request $request)
    {
        $sucursales = ['LAS PALMERAS', 'Sucursal Central', 'Sucursal Norte', 'Sucursal Sur', 'Bodega Principal'];
        $sucursalSeleccionada = $request->get('sucursal', 'LAS PALMERAS');

        $cajas = Caja::orderBy('id')->get();
        if ($cajas->isEmpty()) {
            Caja::create([
                'nombre'          => 'CAJA 2lp',
                'sucursal'        => 'LAS PALMERAS',
                'turno'           => '1',
                'usuario_id'      => $this->userId(),
                'monto_apertura'  => 0,
                'estado'          => 'cerrada',
            ]);
            Caja::create([
                'nombre'          => 'CAJA 1',
                'sucursal'        => 'LAS PALMERAS',
                'turno'           => '1',
                'usuario_id'      => $this->userId(),
                'monto_apertura'  => 0,
                'estado'          => 'cerrada',
            ]);
            $cajas = Caja::orderBy('id')->get();
        }

        // Si hay una caja abierta actualmente, priorizarla
        $cajaAbierta = Caja::abierta();
        $cajaSeleccionadaId = $request->get('caja_id', $cajaAbierta ? $cajaAbierta->id : $cajas->first()->id);
        $cajaActual = $cajas->firstWhere('id', $cajaSeleccionadaId) ?? $cajas->first();

        // Verificar si el usuario en sesión es el cajero que abrió la caja
        $esPropietarioCaja = $cajaActual && $cajaActual->usuario_id == $this->userId();

        $datosLectura = null;
        if ($cajaActual && $cajaActual->estado === 'abierta') {
            $ventasDesglose = Venta::where('caja_id', $cajaActual->id)
                ->where('estado', 'completada')
                ->selectRaw('metodo_pago, SUM(total) as total, COUNT(*) as cantidad')
                ->groupBy('metodo_pago')
                ->get()
                ->keyBy('metodo_pago');

            $ingresosExtra = Gasto::where('caja_id', $cajaActual->id)->where('tipo', 'ingreso')->sum('monto');
            $egresosGastos = Gasto::where('caja_id', $cajaActual->id)->whereIn('tipo', ['egreso', 'retiro'])->sum('monto');

            $efectivoVentas = $ventasDesglose['efectivo']->total ?? 0;
            $tarjetaVentas  = $ventasDesglose['tarjeta']->total ?? 0;
            $transfVentas   = $ventasDesglose['transferencia']->total ?? 0;
            $creditoVentas  = $ventasDesglose['credito']->total ?? 0;

            $totalEnGavetaEsperado = $cajaActual->monto_apertura + $efectivoVentas + $ingresosExtra - $egresosGastos;

            $datosLectura = [
                'efectivo_ventas'       => $efectivoVentas,
                'tarjeta_ventas'        => $tarjetaVentas,
                'transf_ventas'         => $transfVentas,
                'credito_ventas'        => $creditoVentas,
                'ingresos_extra'        => $ingresosExtra,
                'egresos_gastos'        => $egresosGastos,
                'total_gaveta_esperado' => $totalEnGavetaEsperado,
                'total_transacciones'   => Venta::where('caja_id', $cajaActual->id)->where('estado', 'completada')->count(),
                'total_ventas_bruto'    => $cajaActual->total_ventas,
            ];
        }

        return view('caja.corte_admin', compact(
            'sucursales',
            'sucursalSeleccionada',
            'cajas',
            'cajaActual',
            'datosLectura',
            'esPropietarioCaja'
        ));
    }

    /**
     * Procesar Apertura de Caja
     */
    public function abrir(Request $request)
    {
        $cajaAbierta = Caja::abierta();
        if ($cajaAbierta) {
            return redirect()->route('caja.administrar_corte')
                ->with('error', 'Ya existe una caja abierta en el sistema. Debe cerrarse antes de abrir otra.');
        }

        $request->validate([
            'monto_apertura' => 'required|numeric|min:0',
            'nombre'         => 'nullable|string|max:100',
            'sucursal'       => 'nullable|string|max:150',
            'turno'          => 'nullable|string|max:50',
            'notas_apertura' => 'nullable|string|max:500',
        ]);

        $caja = null;
        if ($request->filled('caja_id')) {
            $caja = Caja::find($request->caja_id);
        }

        if (!$caja) {
            $caja = Caja::create([
                'nombre'          => $request->nombre ?? 'CAJA 2lp',
                'sucursal'        => $request->sucursal ?? 'LAS PALMERAS',
                'turno'           => $request->turno ?? '1',
                'usuario_id'      => $this->userId(),
                'monto_apertura'  => $request->monto_apertura,
                'estado'          => 'abierta',
                'abierta_at'      => now(),
                'notas_apertura'  => $request->notas_apertura,
            ]);
        } else {
            $caja->update([
                'sucursal'        => $request->sucursal ?? $caja->sucursal ?? 'LAS PALMERAS',
                'turno'           => $request->turno ?? '1',
                'usuario_id'      => $this->userId(),
                'monto_apertura'  => $request->monto_apertura,
                'monto_cierre'    => null,
                'total_ventas'    => 0,
                'total_gastos'    => 0,
                'estado'          => 'abierta',
                'abierta_at'      => now(),
                'cerrada_at'      => null,
                'notas_apertura'  => $request->notas_apertura,
                'notas_cierre'    => null,
            ]);
        }

        return redirect()->route('caja.administrar_corte')
            ->with('success', "Apertura de {$caja->nombre} realizada exitosamente (Turno: {$caja->turno}).");
    }

    /**
     * Procesar Cierre de Caja (Corte Z)
     * RESTRICCIÓN: Solo el usuario que abrió la caja puede cerrarla
     */
    public function cerrar(Request $request)
    {
        $caja = Caja::abierta();
        if (!$caja) {
            return redirect()->route('caja.administrar_corte')
                ->with('error', 'No hay ninguna caja abierta para cerrar.');
        }

        // Restricción de propietario:
        // "Que otros usuarios no puedan cerrar las cajas o cortes si fue abierto ya sea por el administrador o por otro vendedor"
        if ($caja->usuario_id !== $this->userId()) {
            $cajeroNombre = $caja->usuario ? ($caja->usuario->nombre ?? $caja->usuario->name) : 'otro usuario';
            return redirect()->route('caja.administrar_corte')
                ->with('error', "ACCESO DENEGADO: Esta caja fue abierta por '{$cajeroNombre}'. Solo dicho cajero puede realizar el cierre o corte de caja.");
        }

        $request->validate([
            'monto_cierre' => 'required|numeric|min:0',
        ]);

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
                'sucursal'             => $caja->sucursal ?? 'LAS PALMERAS',
                'turno'                => $caja->turno ?? '1',
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

        return redirect()->route('caja.administrar_corte')
            ->with('success', 'Corte Z finalizado correctamente. Caja cerrada con éxito.');
    }

    /**
     * Corte X (Lectura de Turno Parcial)
     */
    public function corteX(int $id)
    {
        $caja = Caja::with(['usuario', 'ventas' => function ($q) {
            $q->where('estado', 'completada');
        }])->findOrFail($id);

        if ($caja->estado !== 'abierta') {
            return back()->with('error', 'La caja seleccionada no está abierta.');
        }

        $ventasDesglose = Venta::where('caja_id', $caja->id)
            ->where('estado', 'completada')
            ->selectRaw('metodo_pago, SUM(total) as total, COUNT(*) as cantidad')
            ->groupBy('metodo_pago')
            ->get()
            ->keyBy('metodo_pago');

        $ingresosExtra = Gasto::where('caja_id', $caja->id)->where('tipo', 'ingreso')->sum('monto');
        $egresosGastos = Gasto::where('caja_id', $caja->id)->whereIn('tipo', ['egreso', 'retiro'])->sum('monto');

        $efectivoVentas = $ventasDesglose['efectivo']->total ?? 0;
        $tarjetaVentas  = $ventasDesglose['tarjeta']->total ?? 0;
        $transfVentas   = $ventasDesglose['transferencia']->total ?? 0;
        $creditoVentas  = $ventasDesglose['credito']->total ?? 0;

        $efectivoEsperado = $caja->monto_apertura + $efectivoVentas + $ingresosExtra - $egresosGastos;

        return view('caja.corte_x', compact(
            'caja',
            'ventasDesglose',
            'ingresosExtra',
            'egresosGastos',
            'efectivoVentas',
            'tarjetaVentas',
            'transfVentas',
            'creditoVentas',
            'efectivoEsperado'
        ));
    }

    /**
     * Movimientos de Caja (Gastos / Retiros / Ingresos)
     */
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

        return back()->with('success', 'Movimiento registrado correctamente.');
    }

    public function corte(int $cajaId)
    {
        $caja  = Caja::with(['usuario', 'cortes.usuario', 'ventas.cliente'])->findOrFail($cajaId);
        $corte = $caja->cortes->last();
        return view('caja.corte', compact('caja', 'corte'));
    }

    /**
     * Historial de Cortes: Solo Administrador
     * "también deshabilita la opción de historial de cortes para el empleado o vendedor, esto lo puede ver el administrador"
     */
    public function historial()
    {
        $this->requireAdmin();

        $cajas = Caja::with(['usuario', 'cortes'])->latest('abierta_at')->paginate(15);
        return view('caja.historial', compact('cajas'));
    }

    public function movimientos(Request $request)
    {
        $query = Gasto::with(['usuario', 'caja'])->latest('created_at');

        if ($request->filled('buscar')) {
            $query->where('concepto', 'like', '%' . $request->buscar . '%');
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
        $cajas       = Caja::orderByDesc('abierta_at')->get(['id', 'nombre']);

        return view('caja.movimientos', compact('movimientos', 'caja', 'cajas'));
    }

    public function eliminarMovimiento(int $id)
    {
        $gasto = Gasto::findOrFail($id);

        if ($gasto->caja && $gasto->caja->estado === 'abierta' && in_array($gasto->tipo, ['egreso', 'retiro'])) {
            $gasto->caja->decrement('total_gastos', $gasto->monto);
        }

        $gasto->delete();

        return back()->with('success', 'Movimiento eliminado correctamente.');
    }
}
