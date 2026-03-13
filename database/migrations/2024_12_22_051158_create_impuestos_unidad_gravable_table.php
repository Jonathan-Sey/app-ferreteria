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
        Schema::create('impuestos_unidad_gravable', function (Blueprint $table) {
            $table->id();
            $table->integer('codigo');
            $table->string('nombre', 500);
            $table->string('nombre_corto', 500);
            $table->decimal('tasa_monto', 8, 2);
            $table->unsignedBigInteger('id_cvimpuestostipo');
            $table->foreign('id_cvimpuestostipo')->references('id')->on('impuestos_tipo')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('impuestos_unidad_gravable');
    }
};
