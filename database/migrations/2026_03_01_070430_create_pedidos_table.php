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
    Schema::create('pedidos', function (Blueprint $table) {
        $table->id();
        $table->foreignId('cotizacion_id')->constrained('cotizaciones')->onDelete('cascade');
        $table->string('nombre');
        $table->string('apellido');
        $table->string('telefono');
        $table->string('email')->nullable();
        $table->string('dpi')->nullable();
        $table->text('direccion');
        $table->string('comprobante_anticipo')->nullable();
        $table->decimal('monto_anticipo', 10, 2);
        $table->enum('estado', ['pendiente', 'confirmado', 'en_camino', 'entregado', 'cancelado'])->default('pendiente');
        $table->text('notas')->nullable();
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
