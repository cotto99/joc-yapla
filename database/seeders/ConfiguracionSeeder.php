<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ConfiguracionSeeder extends Seeder
{
    public function run(): void
    {
        $configs = [
            ['clave' => 'tipo_cambio',       'valor' => '7.80',  'descripcion' => 'Tipo de cambio USD a GTQ'],
            ['clave' => 'ganancia_pct',       'valor' => '15',    'descripcion' => 'Porcentaje de ganancia sobre precio USD'],
            ['clave' => 'flete_usd',          'valor' => '10',    'descripcion' => 'Costo de flete en USD por libra'],
            ['clave' => 'arancel_pct',        'valor' => '12',    'descripcion' => 'Porcentaje de arancel/impuesto importación'],
            ['clave' => 'comision_pct',       'valor' => '5',     'descripcion' => 'Comisión del agente/gestor en %'],
            ['clave' => 'entrega_local_gtq',  'valor' => '50',    'descripcion' => 'Costo entrega local en Guatemala (GTQ)'],
            ['clave' => 'anticipo_pct',       'valor' => '50',    'descripcion' => 'Porcentaje de anticipo requerido'],
            ['clave' => 'tiempo_estimado',    'valor' => '15 a 21 días hábiles', 'descripcion' => 'Tiempo estimado de entrega'],
            ['clave' => 'whatsapp',           'valor' => '50212345678', 'descripcion' => 'WhatsApp de contacto'],
        ];

        foreach ($configs as $config) {
            DB::table('configuraciones')->updateOrInsert(
                ['clave' => $config['clave']],
                array_merge($config, ['created_at' => now(), 'updated_at' => now()])
            );
        }
    }
}