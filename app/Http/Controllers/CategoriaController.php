<?php
namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::withCount('productos')->orderBy('nombre')->get();
        return view('categorias.index', compact('categorias'));
    }

    public function create()
    {
        $categoria = null;
        return view('categorias.form', compact('categoria'));
    }

    public function edit(int $id)
    {
        $categoria = Categoria::findOrFail($id);
        return view('categorias.form', compact('categoria'));
    }

    public function confirmDelete(int $id)
    {
        $this->requireAdmin();
        $categoria = Categoria::withCount('productos')->findOrFail($id);
        return view('categorias.eliminar', compact('categoria'));
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:100|unique:categorias,nombre']);
        $data = $request->only(['codigo','nombre','descripcion','color','icono','margen_sugerido','orden']);
        $data['activo'] = $request->has('activo') ? 1 : ($request->input('activo', 1) ? 1 : 0);
        $c = Categoria::create($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'id' => $c->id]);
        }
        return redirect()->route('categorias.index')->with('success', 'Categoría "' . $c->nombre . '" creada con éxito.');
    }

    public function update(Request $request, int $id)
    {
        $request->validate(['nombre' => 'required|string|max:100|unique:categorias,nombre,'.$id]);
        $c = Categoria::findOrFail($id);
        $data = $request->only(['codigo','nombre','descripcion','color','icono','margen_sugerido','orden']);
        $data['activo'] = $request->has('activo') ? 1 : 0;
        $c->update($data);

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true]);
        }
        return redirect()->route('categorias.index')->with('success', 'Categoría "' . $c->nombre . '" actualizada con éxito.');
    }

    public function destroy(int $id, Request $request)
    {
        $this->requireAdmin();
        $cat = Categoria::withCount('productos')->findOrFail($id);
        if ($cat->productos_count > 0) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'No se puede eliminar, tiene productos asignados.'], 422);
            }
            return redirect()->route('categorias.index')->with('error', 'No se puede eliminar la categoría porque tiene productos asignados.');
        }
        $nombre = $cat->nombre;
        $cat->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true]);
        }
        return redirect()->route('categorias.index')->with('success', 'Categoría "' . $nombre . '" eliminada correctamente.');
    }
}
