<?php
namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::orderBy('nombre')->get();
        return view('proveedores.index', compact('proveedores'));
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:150']);
        $p = Proveedor::create($request->only([
            'nombre','razon_social','contacto','telefono','email','direccion',
            'departamento','municipio','giro','tipo_documento','nit','nrc','dui',
            'aplica_percepcion','aplica_retencion_renta',
        ]));
        return response()->json(['ok' => true, 'id' => $p->id]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate(['nombre' => 'required|string|max:150']);
        Proveedor::findOrFail($id)->update($request->only([
            'nombre','razon_social','contacto','telefono','email','direccion',
            'departamento','municipio','giro','tipo_documento','nit','nrc','dui',
            'aplica_percepcion','aplica_retencion_renta',
        ]));
        return response()->json(['ok' => true]);
    }

    public function destroy(int $id)
    {
        $this->requireAdmin();
        Proveedor::findOrFail($id)->delete();
        return response()->json(['ok' => true]);
    }
}
