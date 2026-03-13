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
        Schema::create('clientes', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('tipo_entidad')->default(1); 
            $table->string('codigo_interno')->nullable();
            $table->unsignedBigInteger('id_afiliacion_iva')->nullable();
            $table->string('cod_establecimiento')->nullable();
            $table->string('correo')->nullable();
            $table->string('nit')->nullable();
            $table->string('telefono')->nullable();
            $table->string('nombre_comercial')->nullable();
            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->string('codigo_postal')->nullable();
            $table->unsignedBigInteger('id_municipio')->nullable();
            $table->unsignedTinyInteger('estado')->default(1);
            $table->timestamps();
            $table->softDeletes();
            $table->unsignedTinyInteger('created_by')->nullable();
            $table->unsignedTinyInteger('updated_by')->nullable();
            $table->unsignedTinyInteger('deleted_by')->nullable();
        
            // Relaciones
            $table->foreign('id_afiliacion_iva')->references('id')->on('afiliacion_iva');
            $table->foreign('id_municipio')->references('id')->on('municipios');
        });
        
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('clientes');
    }
};
