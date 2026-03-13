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
        Schema::create('entidades', function (Blueprint $table) {
            $table->id();
            $table->string('tipo_entidad');
            $table->string('codigo_interno')->nullable();
            $table->foreignId('id_afiliacion_iva')->nullable()->constrained('afiliacion_iva');
            $table->string('cod_establecimiento')->nullable();
            $table->string('correo')->nullable();
            $table->string('nit')->nullable();
            $table->string('telefono')->nullable();
            $table->string('nombre_comercial')->nullable();
            $table->string('nombre');
            $table->string('direccion')->nullable();
            $table->string('codigo_postal')->nullable();
            $table->foreignId('id_municipio')->nullable()->constrained('municipios');
            $table->boolean('es_cliente')->default(false);
            $table->boolean('es_proveedor')->default(false);
            $table->boolean('es_empresa')->default(false);
            $table->tinyInteger('estado')->default(1);
            $table->foreignId('created_by')->nullable()->constrained('users');
            $table->foreignId('updated_by')->nullable()->constrained('users');
            $table->foreignId('deleted_by')->nullable()->constrained('users');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('entidades');
    }
};
