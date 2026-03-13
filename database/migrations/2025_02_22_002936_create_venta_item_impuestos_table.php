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
        Schema::create('venta_item_impuestos', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_venta_item');
            $table->unsignedBigInteger('id_impuesto');
            $table->decimal('monto_gravable', 13, 6);
            $table->decimal('monto_impuesto', 13, 6);

            $table->foreign('id_venta_item')->references('id')->on('ventas_item')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('id_impuesto')->references('id')->on('impuestos_unidad_gravable')->onDelete('restrict')->onUpdate('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('venta_item_impuestos');
    }
};
