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

    public function store(Request $request)
    {
        $request->validate(['nombre' => 'required|string|max:100|unique:categorias,nombre']);
        $c = Categoria::create(['nombre' => $request->nombre, 'descripcion' => $request->descripcion]);
        return response()->json(['ok' => true, 'id' => $c->id]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate(['nombre' => 'required|string|max:100|unique:categorias,nombre,'.$id]);
        Categoria::findOrFail($id)->update(['nombre' => $request->nombre, 'descripcion' => $request->descripcion]);
        return response()->json(['ok' => true]);
    }

    public function destroy(int $id)
    {
        $this->requireAdmin();
        $cat = Categoria::withCount('productos')->findOrFail($id);
        if ($cat->productos_count > 0) {
            return response()->json(['error' => 'No se puede eliminar, tiene productos asignados.'], 422);
        }
        $cat->delete();
        return response()->json(['ok' => true]);
    }
}
