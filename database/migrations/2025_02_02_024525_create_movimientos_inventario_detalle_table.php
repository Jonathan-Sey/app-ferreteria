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
        Schema::create('movimientos_inventario_detalle', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_movimiento');
            $table->unsignedBigInteger('id_producto');
            $table->decimal('cantidad', 10, 2);
            $table->decimal('costo_unitario', 10, 2)->nullable();
            $table->timestamps();

            $table->foreign('id_movimiento')->references('id')->on('movimientos_inventario')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('id_producto')->references('id')->on('productos')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario_detalle');
    }
};
