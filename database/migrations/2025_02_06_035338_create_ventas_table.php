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
        Schema::create('ventas', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('tipoComprobante')->default(1)->comment('1 Factura, 2 Nota');
            $table->unsignedBigInteger('id_moneda')->default(1);
            $table->unsignedBigInteger('id_tipoDte')->nullable();
            $table->unsignedBigInteger('id_emisor')->nullable();
            $table->unsignedBigInteger('id_cliente')->nullable();
            $table->timestamp('fechahora_emision');
            $table->tinyInteger('certificada')->default(0);
            $table->unsignedBigInteger('id_certificador')->nullable();
            $table->string('no_autorizacion')->nullable()->comment('para ventas sin facturas, numero de doc');
            $table->string('serie')->nullable();
            $table->string('codigo_autorizacion')->nullable();
            $table->timestamp('fechahora_certificacion')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('estado')->default(1)->comment('0 eliminada, 1 Pendiente, 2 Pagada, 3 Cancelada, 4 Devuelta');
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('id_moneda')->references('id')->on('monedas')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('id_tipoDte')->references('id')->on('tipos_dte')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('id_emisor')->references('id')->on('entidades')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('id_cliente')->references('id')->on('entidades')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('id_certificador')->references('id')->on('certificadores')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventas');
    }
};
