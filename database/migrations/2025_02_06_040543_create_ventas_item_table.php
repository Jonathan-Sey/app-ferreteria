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
        Schema::create('ventas_item', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_venta');
            $table->integer('numerolinea')->nullable();
            $table->string('tipo');
            $table->decimal('cantidad', 10, 6);
            $table->unsignedBigInteger('producto_id');
            $table->decimal('precio_unitario', 10, 6);
            $table->decimal('precio_parcial', 10, 6)->nullable()->default(0);
            $table->decimal('descuento', 10, 6)->default(0)->nullable();
            $table->decimal('otros_descuentos', 10, 6)->default(0)->nullable();
            $table->decimal('total', 10, 6)->nullable()->default(0);
            $table->decimal('impuesto', 10, 6)->default(0)->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('producto_id')->references('id')->on('productos')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('id_venta')->references('id')->on('ventas')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas_item');
    }
};
