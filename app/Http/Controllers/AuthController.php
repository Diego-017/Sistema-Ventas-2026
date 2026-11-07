<?php
namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function loginForm()
    {
        if (session()->has('user_id')) return redirect()->route('dashboard');
        return view('autenticacion.login');
    }

    public function login(Request $request)
    {
        $request->validate(['email' => 'required|email', 'password' => 'required']);

        $user = Usuario::where('email', $request->email)->where('activo', 1)->first();

        if ($user && password_verify($request->password, $user->password)) {
            $request->session()->regenerate();
            session(['user_id' => $user->id, 'user' => $user->toArray()]);
            return redirect()->route('dashboard');
        }

        return back()->withInput($request->only('email'))
                     ->with('error', 'Credenciales incorrectas.');
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect()->route('login');
    }

    public function perfil()
    {
        $user = Usuario::find($this->userId()) ?? Usuario::first() ?? new Usuario(['nombre' => 'Usuario', 'email' => 'admin@ventaspro.com', 'rol' => 'admin']);
        return view('perfil.index', compact('user'));
    }

    public function actualizarPerfil(Request $request)
    {
        $user = Usuario::find($this->userId()) ?? Usuario::first() ?? new Usuario(['nombre' => 'Usuario', 'email' => 'admin@ventaspro.com', 'rol' => 'admin']);
        if ($user->rol !== 'admin') {
            return back()->with('error', 'Acceso solo para administradores: No tiene permisos para modificar la información de perfil.');
        }
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'email'    => 'required|email|unique:usuarios,email,' . ($user->id ?? 0),
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = ['nombre' => $request->nombre, 'email' => $request->email];
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }
        if ($user->exists) {
            $user->update($data);
            session(['user' => $user->fresh()->toArray()]);
        }

        return back()->with('success', 'Perfil actualizado correctamente.');
    }
}
