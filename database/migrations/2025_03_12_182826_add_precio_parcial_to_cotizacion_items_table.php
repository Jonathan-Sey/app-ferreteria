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
        Schema::table('cotizacion_items', function (Blueprint $table) {
            $table->decimal('precio_parcial', 12, 2)->after('descuento')->default(0); // Agrega el campo precio_parcial
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cotizacion_items', function (Blueprint $table) {
            $table->dropColumn('precio_parcial'); // Elimina el campo precio_parcial
        });
    }
};
