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
        Schema::table('ventas_item', function (Blueprint $table) {
            $table->decimal('cantidad', 20, 6)->change();
            $table->decimal('precio_unitario', 20, 6)->change();
            $table->decimal('precio_parcial', 20, 6)->nullable()->default(0)->change();
            $table->decimal('descuento', 20, 6)->default(0)->nullable()->change();
            $table->decimal('otros_descuentos', 20, 6)->default(0)->nullable()->change();
            $table->decimal('total', 20, 6)->nullable()->default(0)->change();
            $table->decimal('impuesto', 20, 6)->default(0)->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('ventas_item', function (Blueprint $table) {
            $table->decimal('cantidad', 10, 6)->change();
            $table->decimal('precio_unitario', 10, 6)->change();
            $table->decimal('precio_parcial', 10, 6)->nullable()->default(0)->change();
            $table->decimal('descuento', 10, 6)->default(0)->nullable()->change();
            $table->decimal('otros_descuentos', 10, 6)->default(0)->nullable()->change();
            $table->decimal('total', 10, 6)->nullable()->default(0)->change();
            $table->decimal('impuesto', 10, 6)->default(0)->nullable()->change();
        });
    }
};
