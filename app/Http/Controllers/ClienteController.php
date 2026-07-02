<?php
namespace App\Http\Controllers;

use App\Models\{Cliente, Venta, Credito};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes = Cliente::orderBy('nombre')->paginate(20);
        return view('clientes.index', compact('clientes'));
    }

    public function show(int $id)
    {
        $cliente = Cliente::findOrFail($id);
        $ventas  = Venta::where('cliente_id',$id)
            ->where('estado','completada')
            ->latest('created_at')->paginate(10);
        $creditos = Credito::where('cliente_id',$id)->latest('created_at')->get();
        $totalComprado = Venta::where('cliente_id',$id)->where('estado','completada')->sum('total');
        return view('clientes.show', compact('cliente','ventas','creditos','totalComprado'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'         => 'required|string|max:150',
            'tipo'           => 'nullable|in:contado,credito',
            'limite_credito' => 'nullable|numeric|min:0',
        ]);
        $c = Cliente::create($request->only(['nombre','email','telefono','nit','direccion','tipo','limite_credito']));
        return response()->json(['ok'=>true,'id'=>$c->id,'nombre'=>$c->nombre]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate(['nombre' => 'required|string|max:150']);
        Cliente::findOrFail($id)->update($request->only(['nombre','email','telefono','nit','direccion','tipo','limite_credito']));
        return response()->json(['ok'=>true]);
    }

    public function destroy(int $id)
    {
        $this->requireAdmin();
        Cliente::findOrFail($id)->delete();
        return response()->json(['ok'=>true]);
    }

    public function search(Request $request)
    {
        $t = $request->get('q','');
        return response()->json(
            Cliente::where('nombre','like',"%$t%")->orWhere('nit','like',"%$t%")->limit(10)->get()
        );
    }
}
