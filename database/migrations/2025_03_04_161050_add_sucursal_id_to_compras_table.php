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
        Schema::table('compras', function (Blueprint $table) {
            $table->unsignedBigInteger('id_sucursal')->nullable()->after('id'); // Agrega la columna
            $table->foreign('id_sucursal')->references('id')->on('sucursales')->onDelete('restrict')->onUpdate('restrict'); // Agrega la clave foránea
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('compras', function (Blueprint $table) {
            $table->dropForeign(['id_sucursal']); // Elimina la clave foránea
            $table->dropColumn('id_sucursal'); // Elimina la columna
        });
    }
};
