<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pedido;
use App\Models\Cotizacion;
use App\Models\PedidoTracking;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Inertia\Inertia;

class PedidoController extends Controller
{
    public function index()
    {
        return Inertia::render('Admin/Pedidos', [
            'pedidos' => Pedido::with('cotizacion')
                ->orderByDesc('created_at')
                ->paginate(20),
        ]);
    }

    public function show(Pedido $pedido)
    {
        $pedido->load('cotizacion', 'trackings');

        return Inertia::render('Admin/PedidoDetalle', [
            'pedido' => $pedido,
        ]);
    }

    public function actualizarEstado(Request $request, Pedido $pedido)
    {
        $request->validate([
            'estado'   => 'required|in:pendiente,confirmado,en_camino,entregado,cancelado',
            'notas'    => 'nullable|string|max:500',
            'ubicacion'=> 'nullable|string|max:255',
            'comprobante_complemento' => [
                Rule::requiredIf($request->estado === 'entregado' && !$pedido->comprobante_complemento),
                'nullable', 'file', 'mimes:jpg,jpeg,png,pdf', 'max:5120',
            ],
            'monto_complemento' => [
                Rule::requiredIf($request->estado === 'entregado'),
                'nullable', 'numeric', 'min:0',
            ],
        ]);
    
        // Guardar comprobante complemento si viene
        $datosExtra = [];
        if ($request->hasFile('comprobante_complemento')) {
            $datosExtra['comprobante_complemento'] = $request->file('comprobante_complemento')
                ->store('complementos', 'public');
        }
        if ($request->filled('monto_complemento')) {
            $datosExtra['monto_complemento'] = $request->monto_complemento;
        }
    
        DB::transaction(function () use ($request, $pedido, $datosExtra) {
            $pedido->update(array_merge([
                'estado' => $request->estado,
                'notas'  => $request->notas,
            ], $datosExtra));
    
            PedidoTracking::create([
                'pedido_id'   => $pedido->id,
                'estado'      => $request->estado,
                'descripcion' => $request->notas ?? 'Estado actualizado.',
                'ubicacion'   => $request->ubicacion ?? null,
            ]);
        });
    
        return redirect()->back()->with('success', 'Estado actualizado.');
    }
    public function agregarTracking(Request $request, Pedido $pedido)
    {
        $request->validate([
            'estado'      => 'required|in:pendiente,confirmado,en_camino,entregado,cancelado',
            'descripcion' => 'required|string|max:500',
            'ubicacion'   => 'nullable|string|max:255',
        ]);

        DB::transaction(function () use ($request, $pedido) {
            $pedido->update(['estado' => $request->estado]);

            PedidoTracking::create([
                'pedido_id'   => $pedido->id,
                'estado'      => $request->estado,
                'descripcion' => $request->descripcion,
                'ubicacion'   => $request->ubicacion,
            ]);
        });

        return redirect()->back()->with('success', 'Tracking agregado.');
    }

    /**
     * Crear pedido manualmente desde una cotización (desde el admin)
     */
    public function crearDesdeCotizacion(Request $request, Cotizacion $cotizacion)
    {
        if ($cotizacion->pedido) {
            return redirect()->back()->withErrors(['error' => 'Esta cotización ya tiene un pedido.']);
        }

        $request->validate([
            'nombre'    => 'required|string|max:255',
            'apellido'  => 'required|string|max:255',
            'telefono'  => 'required|string|max:20',
            'email'     => 'nullable|email',
            'dpi'       => 'nullable|string|max:20',
            'direccion' => 'required|string',
            'notas'     => 'nullable|string',
        ]);

        DB::transaction(function () use ($request, $cotizacion) {
            $pedido = Pedido::create([
                'codigo'        => Pedido::generarCodigo(),
                'cotizacion_id' => $cotizacion->id,
                'nombre'        => $request->nombre,
                'apellido'      => $request->apellido,
                'telefono'      => $request->telefono,
                'email'         => $request->email,
                'dpi'           => $request->dpi,
                'direccion'     => $request->direccion,
                'monto_anticipo'=> $cotizacion->anticipo_gtq,
                'estado'        => 'confirmado',
                'notas'         => $request->notas,
            ]);

            PedidoTracking::create([
                'pedido_id'   => $pedido->id,
                'estado'      => 'confirmado',
                'descripcion' => 'Pedido creado manualmente por el administrador.',
            ]);

            $cotizacion->update(['estado' => 'pedido']);
        });

        return redirect()->route('admin.pedidos')->with('success', 'Pedido creado exitosamente.');
    }
}