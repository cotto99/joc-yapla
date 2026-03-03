<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $table = 'pedidos';
    protected $fillable = [
        'codigo', 'cotizacion_id', 'nombre', 'apellido',
        'telefono', 'email', 'dpi', 'direccion',
        'comprobante_anticipo', 'monto_anticipo',
        'estado', 'notas',
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }

    public function trackings()
    {
        return $this->hasMany(PedidoTracking::class)->orderBy('created_at');
    }

    public static function generarCodigo(): string
    {
        $ultimo = self::latest()->first();
        $numero = $ultimo ? (intval(substr($ultimo->codigo ?? '0', -5)) + 1) : 1;
        return 'YAP-' . date('Y') . '-' . str_pad($numero, 5, '0', STR_PAD_LEFT);
    }
}