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
        $user = Usuario::findOrFail($this->userId());
        return view('perfil.index', compact('user'));
    }

    public function actualizarPerfil(Request $request)
    {
        $user = Usuario::findOrFail($this->userId());
        $request->validate([
            'nombre'   => 'required|string|max:100',
            'email'    => 'required|email|unique:usuarios,email,' . $user->id,
            'password' => 'nullable|min:6|confirmed',
        ]);

        $data = ['nombre' => $request->nombre, 'email' => $request->email];
        if ($request->filled('password')) {
            $data['password'] = bcrypt($request->password);
        }
        $user->update($data);

        // Actualizar sesión
        session(['user' => $user->fresh()->toArray()]);

        return back()->with('success', 'Perfil actualizado correctamente.');
    }
}
