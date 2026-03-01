<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
{
    Schema::create('cotizaciones', function (Blueprint $table) {
        $table->id();
        $table->string('link_amazon');
        $table->string('imagen_url')->nullable();
        $table->string('nombre_producto')->nullable();
        $table->decimal('precio_usd', 10, 2);
        $table->decimal('tipo_cambio', 8, 4);
        $table->decimal('precio_gtq', 10, 2);
        $table->decimal('costo_flete', 10, 2)->default(0);
        $table->decimal('costo_arancel', 10, 2)->default(0);
        $table->decimal('costo_comision', 10, 2)->default(0);
        $table->decimal('costo_entrega', 10, 2)->default(0);
        $table->decimal('ganancia', 10, 2)->default(0);
        $table->decimal('total_gtq', 10, 2);
        $table->decimal('anticipo_gtq', 10, 2)->default(0);
        $table->string('tiempo_estimado')->nullable();
        $table->string('ip_cliente')->nullable();
        $table->enum('estado', ['consultada', 'pedido', 'cancelada'])->default('consultada');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizaciones');
    }
};
