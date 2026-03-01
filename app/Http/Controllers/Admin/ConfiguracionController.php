<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ConfiguracionController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Configuracion', [
            'configs' => Configuracion::all()->keyBy('clave')
        ]);
    }

    public function update(Request $request)
    {
        $request->validate([
            'tipo_cambio'      => 'required|numeric|min:1',
            'ganancia_pct'     => 'required|numeric|min:0',
            'flete_usd'        => 'required|numeric|min:0',
            'arancel_pct'      => 'required|numeric|min:0',
            'comision_pct'     => 'required|numeric|min:0',
            'entrega_local_gtq'=> 'required|numeric|min:0',
            'anticipo_pct'     => 'required|numeric|min:1|max:100',
            'tiempo_estimado'  => 'required|string',
            'whatsapp'         => 'required|string',
        ]);

        foreach ($request->except('_token') as $clave => $valor) {
            Configuracion::set($clave, $valor);
        }

        return redirect()->back()->with('success', 'Configuración actualizada.');
    }
}