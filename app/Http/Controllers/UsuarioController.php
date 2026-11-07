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

    public function create()
    {
        $this->requireAdmin();
        $usuario = null;
        $roles = [
            'admin'      => 'Administrador General',
            'vendedor'   => 'Vendedor / Cajero',
            'supervisor' => 'Supervisor de Ventas y Caja',
            'bodeguero'  => 'Encargado de Inventario / Bodega',
            'contador'   => 'Auditor / Contador',
        ];
        $sucursales = ['Sucursal Central', 'Sucursal Norte', 'Sucursal Sur', 'Bodega Principal'];
        return view('usuarios.form', compact('usuario', 'roles', 'sucursales'));
    }

    public function store(Request $request)
    {
        $this->requireAdmin();
        $request->validate([
            'nombre'    => 'required|string|max:100',
            'email'     => 'required|email|unique:usuarios,email',
            'password'  => 'required|min:6|confirmed',
            'rol'       => 'required|in:admin,vendedor,supervisor,bodeguero,contador',
            'telefono'  => 'nullable|string|max:50',
            'cargo'     => 'nullable|string|max:100',
            'sucursal'  => 'nullable|string|max:100',
            'direccion' => 'nullable|string|max:255',
            'notas'     => 'nullable|string',
        ]);

        $usuario = Usuario::create([
            'nombre'    => $request->nombre,
            'email'     => $request->email,
            'password'  => bcrypt($request->password),
            'rol'       => $request->rol,
            'telefono'  => $request->telefono,
            'cargo'     => $request->cargo,
            'sucursal'  => $request->sucursal ?? 'Sucursal Central',
            'direccion' => $request->direccion,
            'notas'     => $request->notas,
            'activo'    => $request->has('activo') ? 1 : ($request->input('activo', 1) ? 1 : 0),
        ]);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['ok' => true, 'id' => $usuario->id]);
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario registrado correctamente.');
    }

    public function edit(int $id)
    {
        $this->requireAdmin();
        $usuario = Usuario::findOrFail($id);
        $roles = [
            'admin'      => 'Administrador General',
            'vendedor'   => 'Vendedor / Cajero',
            'supervisor' => 'Supervisor de Ventas y Caja',
            'bodeguero'  => 'Encargado de Inventario / Bodega',
            'contador'   => 'Auditor / Contador',
        ];
        $sucursales = ['Sucursal Central', 'Sucursal Norte', 'Sucursal Sur', 'Bodega Principal'];
        return view('usuarios.form', compact('usuario', 'roles', 'sucursales'));
    }

    public function update(Request $request, int $id)
    {
        $this->requireAdmin();
        $user = Usuario::findOrFail($id);
        $request->validate([
            'nombre'    => 'required|string|max:100',
            'email'     => 'required|email|unique:usuarios,email,'.$id,
            'rol'       => 'required|in:admin,vendedor,supervisor,bodeguero,contador',
            'password'  => 'nullable|min:6|confirmed',
            'telefono'  => 'nullable|string|max:50',
            'cargo'     => 'nullable|string|max:100',
            'sucursal'  => 'nullable|string|max:100',
            'direccion' => 'nullable|string|max:255',
            'notas'     => 'nullable|string',
        ]);

        $data = $request->only(['nombre','email','rol','telefono','cargo','sucursal','direccion','notas']);
        $data['activo'] = $request->has('activo') ? 1 : 0;
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        if ($request->ajax() || $request->expectsJson()) {
            return response()->json(['ok' => true]);
        }

        return redirect()->route('usuarios.index')->with('success', 'Usuario actualizado correctamente.');
    }

    public function confirmDelete(int $id)
    {
        $this->requireAdmin();
        $usuario = Usuario::findOrFail($id);
        return view('usuarios.eliminar', compact('usuario'));
    }

    public function destroy(int $id)
    {
        $this->requireAdmin();
        $user = Usuario::findOrFail($id);
        if ($user->id === $this->userId()) {
            return redirect()->route('usuarios.index')->with('error', 'No puedes eliminar tu propio usuario de sesión.');
        }
        $user->delete();
        return redirect()->route('usuarios.index')->with('success', 'Usuario eliminado correctamente.');
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