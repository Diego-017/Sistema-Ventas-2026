<?php
namespace App\Http\Controllers;

use App\Models\{Producto, Categoria, Subcategoria, Proveedor, PrecioHistorial};
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        $productos = Producto::activo()
            ->with(['categoria','subcategoria','proveedor'])
            ->orderBy('nombre')->paginate(20);
        return view('productos.index', compact('productos'));
    }

    public function create()
    {
        $this->requireAdmin();
        return view('productos.form', [
            'producto'     => null,
            'categorias'   => Categoria::orderBy('nombre')->get(),
            'subcategorias'=> Subcategoria::orderBy('nombre')->get(),
            'proveedores'  => Proveedor::orderBy('nombre')->get(),
        ]);
    }

    public function store(Request $request)
    {
        $this->requireAdmin();
        $data = $this->validatedData($request);
        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos','public');
        }
        Producto::create($data);
        return redirect()->route('productos.index')->with('success', 'Producto creado correctamente.');
    }

    public function edit(int $id)
    {
        $this->requireAdmin();
        $producto = Producto::with(['categoria','subcategoria','proveedor'])->findOrFail($id);
        $subcategorias = $producto->categoria_id
            ? Subcategoria::where('categoria_id', $producto->categoria_id)->orderBy('nombre')->get()
            : collect();
        return view('productos.form', [
            'producto'      => $producto,
            'categorias'    => Categoria::orderBy('nombre')->get(),
            'subcategorias' => $subcategorias,
            'proveedores'   => Proveedor::orderBy('nombre')->get(),
        ]);
    }

    public function update(Request $request, int $id)
    {
        $this->requireAdmin();
        $producto = Producto::findOrFail($id);
        $data = $this->validatedData($request, $id);

        // Registrar historial si cambió el precio
        if ($producto->precio_compra != $data['precio_compra'] || $producto->precio_venta != $data['precio_venta']) {
            PrecioHistorial::create([
                'producto_id'            => $producto->id,
                'usuario_id'             => $this->userId(),
                'precio_compra_anterior' => $producto->precio_compra,
                'precio_compra_nuevo'    => $data['precio_compra'],
                'precio_venta_anterior'  => $producto->precio_venta,
                'precio_venta_nuevo'     => $data['precio_venta'],
                'motivo'                 => $request->input('motivo_cambio_precio'),
            ]);
        }

        if ($request->hasFile('imagen')) {
            $data['imagen'] = $request->file('imagen')->store('productos','public');
        }
        $producto->update($data);
        return redirect()->route('productos.index')->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(int $id)
    {
        $this->requireAdmin();
        Producto::findOrFail($id)->update(['activo' => 0]);
        return response()->json(['ok' => true]);
    }

    public function search(Request $request)
    {
        $productos = Producto::activo()->search($request->get('q',''))->limit(15)->get([
            'id','nombre','sku','precio_venta','precio_mayoreo','precio_compra','stock'
        ]);
        return response()->json($productos);
    }

    private function validatedData(Request $request, ?int $ignoreId = null): array
    {
        $skuRule = 'nullable|string|max:50';
        if ($ignoreId) $skuRule .= '|unique:productos,sku,'.$ignoreId;

        return $request->validate([
            'sku'             => $skuRule,
            'codigo_barras'   => 'nullable|string|max:100',
            'codigo_barras2'  => 'nullable|string|max:100',
            'nombre'          => 'required|string|max:200',
            'descripcion'     => 'nullable|string',
            'categoria_id'    => 'nullable|exists:categorias,id',
            'subcategoria_id' => 'nullable|exists:subcategorias,id',
            'proveedor_id'    => 'nullable|exists:proveedores,id',
            'precio_compra'   => 'nullable|numeric|min:0',
            'precio_venta'    => 'required|numeric|min:0.01',
            'precio_mayoreo'  => 'nullable|numeric|min:0',
            'stock'           => 'nullable|integer|min:0',
            'stock_minimo'    => 'nullable|integer|min:0',
            'unidad'          => 'nullable|string|max:30',
            'imagen'          => 'nullable|image|max:2048',
        ]);
    }
}
