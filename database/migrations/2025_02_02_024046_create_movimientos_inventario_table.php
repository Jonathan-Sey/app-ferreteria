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
        Schema::create('movimientos_inventario', function (Blueprint $table) {
            $table->id();
            $table->date('fecha');
            $table->unsignedBigInteger('id_sucursal');
            $table->enum('tipo_movimiento', ['ENTRADA', 'SALIDA', 'AJUSTE', 'TRASLADO']);
            $table->string('numero_documento', 50)->nullable();
            $table->text('observaciones')->nullable();
            $table->unsignedBigInteger('sucursal_destino')->nullable();  // Para casos de traslado
            $table->unsignedTinyInteger('estado')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('id_sucursal')->references('id')->on('sucursales')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('sucursal_destino')->references('id')->on('sucursales')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('movimientos_inventario');
    }
};
