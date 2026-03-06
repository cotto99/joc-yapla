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
        Schema::table('pedidos', function (Blueprint $table) {
            $table->boolean('envio_gratis')->default(false)->after('direccion');
            $table->string('comprobante_complemento')->nullable()->after('comprobante_anticipo');
            $table->decimal('monto_complemento', 10, 2)->nullable()->after('comprobante_complemento');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            //
        });
    }
};
