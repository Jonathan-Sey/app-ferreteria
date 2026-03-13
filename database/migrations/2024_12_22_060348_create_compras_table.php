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
        Schema::create('compras', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('tipoComprobante')->default(1)->comment('1 Factura, 2 Nota');
            $table->unsignedBigInteger('id_moneda')->default(1);
            $table->unsignedBigInteger('id_tipoDte')->nullable();
            $table->unsignedBigInteger('id_proveedor');
            $table->unsignedBigInteger('id_receptor')->nullable();
            $table->timestamp('fechahora_emision');
            $table->unsignedBigInteger('id_certificador')->nullable();
            $table->string('no_autorizacion')->nullable()->comment('para compras sin facturas, numero de doc');
            $table->string('serie')->nullable();
            $table->string('codigo_autorizacion')->nullable();
            $table->timestamp('fechahora_certificacion')->nullable();
            $table->text('notes')->nullable();
            $table->unsignedTinyInteger('estado')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();

            $table->foreign('id_moneda')->references('id')->on('monedas')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('id_tipoDte')->references('id')->on('tipos_dte')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('id_proveedor')->references('id')->on('entidades')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('id_receptor')->references('id')->on('entidades')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('id_certificador')->references('id')->on('certificadores')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compras');
    }
};
