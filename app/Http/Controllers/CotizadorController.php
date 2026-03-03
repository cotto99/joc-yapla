<?php
namespace App\Http\Controllers;

use App\Models\Cotizacion;
use App\Models\Pedido;
use App\Models\Configuracion;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Illuminate\Support\Facades\Http;
use App\Models\PedidoTracking;
use Illuminate\Support\Facades\DB;

class CotizadorController extends Controller
{
    public function index()
    {
        return Inertia::render('Cotizador/Index');
    }

    public function storePedido(Request $request, Cotizacion $cotizacion)
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
        'comprobante_anticipo' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        'notas'     => 'nullable|string',
    ]);

    $comprobante = null;
    if ($request->hasFile('comprobante_anticipo')) {
        $comprobante = $request->file('comprobante_anticipo')
            ->store('comprobantes', 'public');
    }

    DB::transaction(function () use ($request, $cotizacion, $comprobante) {
        $pedido = Pedido::create([
            'codigo'               => Pedido::generarCodigo(),
            'cotizacion_id'        => $cotizacion->id,
            'nombre'               => $request->nombre,
            'apellido'             => $request->apellido,
            'telefono'             => $request->telefono,
            'email'                => $request->email,
            'dpi'                  => $request->dpi,
            'direccion'            => $request->direccion,
            'comprobante_anticipo' => $comprobante,
            'monto_anticipo'       => $cotizacion->anticipo_gtq,
            'estado'               => 'pendiente',
            'notas'                => $request->notas,
        ]);

        PedidoTracking::create([
            'pedido_id'   => $pedido->id,
            'estado'      => 'pendiente',
            'descripcion' => 'Pedido recibido. Estamos verificando tu comprobante de pago.',
        ]);

        $cotizacion->update(['estado' => 'pedido']);

        session(['ultimo_pedido_codigo' => $pedido->codigo]);
    });

    return redirect()->route('cotizador.gracias');
}
    public function cotizar(Request $request)
    {
        $request->validate([
            'link_amazon'     => 'required|url',
            'precio_usd'      => 'required|numeric|min:0.01',
            'nombre_producto' => 'nullable|string|max:255',
        ]);
    
        $tipoCambio = $this->obtenerTipoCambioBanguat();
    
        $gananciaPct = (float) Configuracion::get('ganancia_pct', 15);
        $fleteUsd    = (float) Configuracion::get('flete_usd', 10);
        $arancelPct  = (float) Configuracion::get('arancel_pct', 12);
        $comisionPct = (float) Configuracion::get('comision_pct', 5);
        $entregaGtq  = (float) Configuracion::get('entrega_local_gtq', 50);
        $anticipoPct = (float) Configuracion::get('anticipo_pct', 50);
        $tiempoEst   = Configuracion::get('tiempo_estimado', '15 a 21 días hábiles');
    
        $precioUsd = (float) $request->precio_usd;
        $precioGtq = $precioUsd * $tipoCambio;
        $ganancia  = $precioGtq * ($gananciaPct / 100);
        $flete     = $fleteUsd * $tipoCambio;
        $arancel   = $precioGtq * ($arancelPct / 100);
        $comision  = $precioGtq * ($comisionPct / 100);
        $totalGtq  = $precioGtq + $ganancia + $flete + $arancel + $comision + $entregaGtq;
        $anticipo  = $totalGtq * ($anticipoPct / 100);
    
        // Extraer imagen automáticamente del link de Amazon
        $imagenUrl = $this->extraerImagenAmazon($request->link_amazon);
    
        $cotizacion = Cotizacion::create([
            'link_amazon'     => $request->link_amazon,
            'imagen_url'      => $imagenUrl,
            'nombre_producto' => $request->nombre_producto,
            'precio_usd'      => $precioUsd,
            'tipo_cambio'     => $tipoCambio,
            'precio_gtq'      => round($precioGtq, 2),
            'costo_flete'     => round($flete, 2),
            'costo_arancel'   => round($arancel, 2),
            'costo_comision'  => round($comision, 2),
            'costo_entrega'   => $entregaGtq,
            'ganancia'        => round($ganancia, 2),
            'total_gtq'       => round($totalGtq, 2),
            'anticipo_gtq'    => round($anticipo, 2),
            'tiempo_estimado' => $tiempoEst,
            'ip_cliente'      => $request->ip(),
            'estado'          => 'consultada',
        ]);
    
        return redirect()->route('cotizador.resultado', $cotizacion->id);
    }
    
    /**
     * Extrae la imagen principal de un link de Amazon
     */
    private function extraerImagenAmazon(string $url): ?string
    {
        try {
            // Extraer el ASIN del link de Amazon
            // Formatos: /dp/ASIN, /gp/product/ASIN, /product/ASIN
            preg_match(
                '/(?:dp|gp\/product|product)\/([A-Z0-9]{10})/i',
                $url,
                $matches
            );
    
            if (empty($matches[1])) {
                return null;
            }
    
            $asin = $matches[1];
    
            // URL directa de imagen de Amazon con el ASIN
            // Amazon tiene este patrón estándar para imágenes principales
            return "https://images-na.ssl-images-amazon.com/images/P/{$asin}.01.LZZZZZZZ.jpg";
    
        } catch (\Exception $e) {
            return null;
        }
    }
    private function obtenerTipoCambioBanguat(): float
    {
        try {
            $response = Http::timeout(5)->get('https://www.banguat.gob.gt/variables/json/');
            if ($response->successful()) {
                $data = $response->json();
                return (float) ($data['VarDolar']['compra'] ?? 7.80);
            }
        } catch (\Exception $e) {
            // Si falla usar el configurado
        }
        return (float) Configuracion::get('tipo_cambio', 7.80);
    }

    public function pedido(Request $request, Cotizacion $cotizacion)
    {
        $request->validate([
            'nombre'     => 'required|string|max:255',
            'apellido'   => 'required|string|max:255',
            'telefono'   => 'required|string|max:20',
            'email'      => 'nullable|email',
            'dpi'        => 'nullable|string|max:20',
            'direccion'  => 'required|string',
            'comprobante_anticipo' => 'required|file|mimes:jpg,jpeg,png,pdf|max:5120',
            'notas'      => 'nullable|string',
        ]);

        $path = $request->file('comprobante_anticipo')
                        ->store('comprobantes', 'public');

        Pedido::create([
            'cotizacion_id'        => $cotizacion->id,
            'nombre'               => $request->nombre,
            'apellido'             => $request->apellido,
            'telefono'             => $request->telefono,
            'email'                => $request->email,
            'dpi'                  => $request->dpi,
            'direccion'            => $request->direccion,
            'comprobante_anticipo' => $path,
            'monto_anticipo'       => $cotizacion->anticipo_gtq,
            'estado'               => 'pendiente',
            'notas'                => $request->notas,
        ]);

        $cotizacion->update(['estado' => 'pedido']);

        return redirect()->route('cotizador.gracias');
    }

  
    public function resultado(Cotizacion $cotizacion)
{
    return Inertia::render('Cotizador/Resultado', [
        'cotizacion' => $cotizacion
    ]);
}

public function trackingForm()
{
    return Inertia::render('Cotizador/Tracking', [
        'pedido' => null,
        'error'  => null,
    ]);
}

public function tracking(Request $request)
{
    $request->validate([
        'codigo' => 'required|string'
    ]);

    $pedido = Pedido::with(['trackings', 'cotizacion'])
        ->where('codigo', strtoupper(trim($request->codigo)))
        ->first();

    if (!$pedido) {
        return back()->with('error', 'No encontramos ningún pedido con ese código.');
    }

    return Inertia::render('Cotizador/Tracking', [
        'pedido' => $pedido,
        'error'  => null,
    ]);
}

public function gracias()
{
    $codigo = session('ultimo_pedido_codigo');
    if (!$codigo) return redirect('/');

    $pedido = Pedido::where('codigo', $codigo)->firstOrFail();
    return Inertia::render('Cotizador/Gracias', ['pedido' => $pedido]);
}

}