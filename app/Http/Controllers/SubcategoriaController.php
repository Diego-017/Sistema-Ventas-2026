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

    public function create()
    {
        $subcategoria = null;
        $categorias = Categoria::orderBy('nombre')->get();
        return view('subcategorias.form', compact('subcategoria','categorias'));
    }

    public function edit(int $id)
    {
        $subcategoria = Subcategoria::findOrFail($id);
        $categorias = Categoria::orderBy('nombre')->get();
        return view('subcategorias.form', compact('subcategoria','categorias'));
    }

    public function confirmDelete(int $id)
    {
        $this->requireAdmin();
        $subcategoria = Subcategoria::with('categoria')->withCount('productos')->findOrFail($id);
        return view('subcategorias.eliminar', compact('subcategoria'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre'       => 'required|string|max:100',
        ]);
        $s = Subcategoria::create($request->only(['categoria_id','nombre','descripcion']));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true, 'id' => $s->id]);
        }
        return redirect()->route('subcategorias.index')->with('success', 'Subcategoría "' . $s->nombre . '" creada con éxito.');
    }

    public function update(Request $request, int $id)
    {
        $request->validate([
            'categoria_id' => 'required|exists:categorias,id',
            'nombre'       => 'required|string|max:100',
        ]);
        $s = Subcategoria::findOrFail($id);
        $s->update($request->only(['categoria_id','nombre','descripcion']));

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true]);
        }
        return redirect()->route('subcategorias.index')->with('success', 'Subcategoría "' . $s->nombre . '" actualizada con éxito.');
    }

    public function destroy(int $id, Request $request)
    {
        $this->requireAdmin();
        $sub = Subcategoria::withCount('productos')->findOrFail($id);
        if ($sub->productos_count > 0) {
            if ($request->wantsJson() || $request->ajax()) {
                return response()->json(['error' => 'Tiene productos asignados.'], 422);
            }
            return redirect()->route('subcategorias.index')->with('error', 'No se puede eliminar la subcategoría porque tiene productos asignados.');
        }
        $nombre = $sub->nombre;
        $sub->delete();

        if ($request->wantsJson() || $request->ajax()) {
            return response()->json(['ok' => true]);
        }
        return redirect()->route('subcategorias.index')->with('success', 'Subcategoría "' . $nombre . '" eliminada correctamente.');
    }

    public function porCategoria(int $categoriaId)
    {
        return response()->json(
            Subcategoria::where('categoria_id', $categoriaId)->orderBy('nombre')->get(['id','nombre'])
        );
    }
}
