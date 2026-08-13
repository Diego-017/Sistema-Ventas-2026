<?php
namespace App\Http\Controllers;

use App\Models\Configuracion;
use Illuminate\Http\Request;

class ConfiguracionController extends Controller
{
    public function index()
    {
        $this->requireAdmin();
        $config = Configuracion::all_config();
        return view('configuracion.index', compact('config'));
    }

    public function update(Request $request)
    {
        $this->requireAdmin();
        $request->validate([
            'nombre_negocio' => 'required|string|max:100',
            'moneda'         => 'required|string|max:10',
            'simbolo_moneda' => 'required|string|max:5',
            'iva'            => 'required|numeric|min:0|max:100',
            'direccion'      => 'nullable|string|max:255',
            'telefono'       => 'nullable|string|max:30',
            'email_negocio'  => 'nullable|email',
        ]);

        foreach ($request->except(['_token','_method']) as $clave => $valor) {
            Configuracion::set($clave, $valor);
        }

        return back()->with('success', 'Configuración guardada correctamente.');
    }
}
