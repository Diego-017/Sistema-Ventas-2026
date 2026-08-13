<?php
namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class UsuarioController extends Controller
{
    public function index()
    {
        $this->requireAdmin();
        $usuarios = Usuario::orderBy('nombre')->get();
        return view('usuarios.index', compact('usuarios'));
    }

    public function store(Request $request)
    {
        $this->requireAdmin();
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'email'    => 'required|email|unique:usuarios,email',
            'password' => 'required|min:6|confirmed',
            'rol'      => 'required|in:admin,vendedor',
        ]);
        Usuario::create([
            'nombre'   => $request->nombre,
            'email'    => $request->email,
            'password' => bcrypt($request->password),
            'rol'      => $request->rol,
            'activo'   => 1,
        ]);
        return back()->with('success', 'Usuario creado correctamente.');
    }

    public function update(Request $request, int $id)
    {
        $this->requireAdmin();
        $user = Usuario::findOrFail($id);
        $request->validate([
            'nombre' => 'required|string|max:100',
            'email'  => 'required|email|unique:usuarios,email,'.$id,
            'rol'    => 'required|in:admin,vendedor',
            'password' => 'nullable|min:6|confirmed',
        ]);
        $data = $request->only(['nombre','email','rol']);
        if ($request->filled('password')) $data['password'] = bcrypt($request->password);
        $user->update($data);
        return response()->json(['ok' => true]);
    }

    public function toggle(int $id)
    {
        $this->requireAdmin();
        $user = Usuario::findOrFail($id);
        if ($user->id === $this->userId()) {
            return response()->json(['error' => 'No puedes desactivarte a ti mismo.'], 422);
        }
        $user->update(['activo' => !$user->activo]);
        return response()->json(['ok' => true, 'activo' => $user->activo]);
    }
}
