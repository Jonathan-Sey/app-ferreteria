<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('venta_metodo_pago')) {
            Schema::create('venta_metodo_pago', function (Blueprint $table) {
                $table->foreignId('venta_id')
                      ->constrained('ventas')
                      ->cascadeOnDelete();
                
                $table->foreignId('metodo_pago_id')
                      ->constrained('metodos_pago');
                
                $table->decimal('monto', 12, 2);
                $table->string('referencia', 100)->nullable();
                $table->timestamps();
                
                $table->primary(['venta_id', 'metodo_pago_id']);
            });
        } else {
            // Solo modificar columnas existentes si es necesario
            Schema::table('venta_metodo_pago', function (Blueprint $table) {
                if (!Schema::hasColumn('venta_metodo_pago', 'referencia')) {
                    $table->string('referencia', 100)->nullable()->after('monto');
                }
                
                // Asegurar el tipo decimal correcto
                $table->decimal('monto', 12, 2)->change();
            });
        }
    }

    public function down(): void
    {
        // No dropear la tabla para evitar pérdida de datos
        Schema::table('venta_metodo_pago', function (Blueprint $table) {
            $table->dropForeign(['venta_id']);
            $table->dropForeign(['metodo_pago_id']);
        });
    }
};