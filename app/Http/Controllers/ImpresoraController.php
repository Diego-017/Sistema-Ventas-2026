<?php
namespace App\Http\Controllers;

use App\Models\Impresora;
use Illuminate\Http\Request;

class ImpresoraController extends Controller
{
    public function index()
    {
        $impresoras = Impresora::orderBy('nombre')->get();
        return view('impresoras.index', compact('impresoras'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'      => 'required|string|max:100',
            'tipo'        => 'required|in:termica,laser,inkjet',
            'ancho_papel' => 'required|integer|in:58,80',
        ]);
        if ($request->boolean('predeterminada')) {
            Impresora::where('predeterminada', 1)->update(['predeterminada' => 0]);
        }
        $i = Impresora::create($request->only(['nombre','tipo','conexion','ancho_papel','activa','predeterminada']));
        return response()->json(['ok' => true, 'id' => $i->id]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate(['nombre' => 'required|string|max:100']);
        if ($request->boolean('predeterminada')) {
            Impresora::where('predeterminada', 1)->update(['predeterminada' => 0]);
        }
        Impresora::findOrFail($id)->update($request->only(['nombre','tipo','conexion','ancho_papel','activa','predeterminada']));
        return response()->json(['ok' => true]);
    }

    public function destroy(int $id)
    {
        $this->requireAdmin();
        Impresora::findOrFail($id)->delete();
        return response()->json(['ok' => true]);
    }

    public function ticket(int $ventaId)
    {
        $venta      = \App\Models\Venta::with(['cliente','usuario','items'])->findOrFail($ventaId);
        $impresora  = Impresora::predeterminada();
        $config     = \App\Models\Configuracion::all_config();
        return view('impresoras.ticket', compact('venta','impresora','config'));
    }
}
