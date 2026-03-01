<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pedido extends Model
{
    protected $fillable = [
        'cotizacion_id', 'nombre', 'apellido', 'telefono',
        'email', 'dpi', 'direccion', 'comprobante_anticipo',
        'monto_anticipo', 'estado', 'notas'
    ];

    public function cotizacion()
    {
        return $this->belongsTo(Cotizacion::class);
    }
}