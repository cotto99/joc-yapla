<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cotizacion extends Model
{
    protected $table = 'cotizaciones'; // ← agregá esta línea

    protected $fillable = [
        'link_amazon', 'imagen_url', 'nombre_producto',
        'precio_usd', 'tipo_cambio', 'precio_gtq',
        'costo_flete', 'costo_arancel', 'costo_comision',
        'costo_entrega', 'ganancia', 'total_gtq',
        'anticipo_gtq', 'tiempo_estimado', 'ip_cliente', 'estado'
    ];

    public function pedido()
    {
        return $this->hasOne(Pedido::class);
    }
}