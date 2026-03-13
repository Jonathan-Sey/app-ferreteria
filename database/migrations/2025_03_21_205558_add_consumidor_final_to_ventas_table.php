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
        Schema::table('ventas', function (Blueprint $table) {
            // Agrega la nueva columna "consumidor_final"
            $table->string('consumidor_final', 255)
                  ->nullable() // Permite valores nulos
                  ->after('id_cliente'); // Coloca la columna después de "id_cliente"
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas', function (Blueprint $table) {
            // Elimina la columna "consumidor_final" si se revierte la migración
            $table->dropColumn('consumidor_final');
        });
    }
};