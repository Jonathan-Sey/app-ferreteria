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
        Schema::create('cotizacion_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cotizacion'); // Relación con la cotización
            $table->unsignedBigInteger('id_producto'); // Relación con el producto
            $table->integer('cantidad'); // Cantidad del producto
            $table->decimal('precio_unitario', 12, 2); // Precio unitario del producto
            $table->decimal('descuento', 12, 2)->default(0); // Descuento aplicado al producto
            $table->decimal('total', 12, 2); // Total del ítem (calculado)
            $table->timestamps();

            // Claves foráneas
            $table->foreign('id_cotizacion')->references('id')->on('cotizacions')->onDelete('cascade');
            $table->foreign('id_producto')->references('id')->on('productos')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacion_items');
    }
};