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
        Schema::create('escenarios', function (Blueprint $table) {
            $table->id();
            $table->integer('cod_escenario');
            $table->unsignedBigInteger('id_frases');
            $table->string('escenario', 1000);
            $table->string('efecto');
            $table->string('nombre', 500);
            $table->string('tipo_dte');
            $table->string('frase_automatica');
            $table->timestamps();

            $table->foreign('id_frases')->references('id')->on('frases')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('escenarios');
    }
};
