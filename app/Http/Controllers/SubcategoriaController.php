<?php
namespace App\Http\Controllers;

use App\Models\{Subcategoria, Categoria};
use Illuminate\Http\Request;

class SubcategoriaController extends Controller
{
    public function index()
    {
        $subcategorias = Subcategoria::with('categoria')
            ->withCount('productos')
            ->orderBy('nombre')
            ->get();
        $categorias = Categoria::orderBy('nombre')->get();
        return view('subcategorias.index', compact('subcategorias','categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre'       => 'required|string|max:100',
        ]);
        $s = Subcategoria::create($request->only(['categoria_id','nombre','descripcion']));
        return response()->json(['ok' => true, 'id' => $s->id]);
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre'       => 'required|string|max:100',
        ]);
        Subcategoria::findOrFail($id)->update($request->only(['categoria_id','nombre','descripcion']));
        return response()->json(['ok' => true]);
    }

    public function destroy(int $id)
    {
        $this->requireAdmin();
        $sub = Subcategoria::withCount('productos')->findOrFail($id);
        if ($sub->productos_count > 0) {
            return response()->json(['error' => 'Tiene productos asignados.'], 422);
        }
        $sub->delete();
        return response()->json(['ok' => true]);
    }

    public function porCategoria(int $categoriaId)
    {
        return response()->json(
            Subcategoria::where('categoria_id', $categoriaId)->orderBy('nombre')->get(['id','nombre'])
        );
    }
}
