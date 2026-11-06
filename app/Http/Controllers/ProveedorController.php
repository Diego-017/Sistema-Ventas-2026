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

    public function create()
    {
        $proveedor = null;
        return view('proveedores.form', compact('proveedor'));
    }

    public function edit(int $id)
    {
        $proveedor = Proveedor::findOrFail($id);
        return view('proveedores.form', compact('proveedor'));
    }

    public function confirmDelete(int $id)
    {
        $this->requireAdmin();
        $proveedor = Proveedor::withCount('compras')->findOrFail($id);
        return view('proveedores.eliminar', compact('proveedor'));
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:150']);
        $p = Proveedor::create($request->only([
            'nombre','razon_social','contacto','telefono','email','direccion',
            'departamento','municipio','giro','tipo_documento','nit','nrc','dui',
            'aplica_percepcion','aplica_retencion_renta',
        ]));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'id' => $p->id]);
        }
        return redirect()->route('proveedores.index')->with('success', 'Proveedor "' . $p->nombre . '" registrado con éxito.');
    }

    public function update(Request $request, int $id)
    {
        $request->validate(['nombre' => 'required|string|max:150']);
        $p = Proveedor::findOrFail($id);
        $p->update($request->only([
            'nombre','razon_social','contacto','telefono','email','direccion',
            'departamento','municipio','giro','tipo_documento','nit','nrc','dui',
            'aplica_percepcion','aplica_retencion_renta',
        ]));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true]);
        }
        return redirect()->route('proveedores.index')->with('success', 'Proveedor "' . $p->nombre . '" actualizado con éxito.');
    }

    public function destroy(int $id, Request $request)
    {
        $this->requireAdmin();
        $p = Proveedor::findOrFail($id);
        $nombre = $p->nombre;
        $p->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true]);
        }
        return redirect()->route('proveedores.index')->with('success', 'Proveedor "' . $nombre . '" eliminado correctamente.');
    }
}
