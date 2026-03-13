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
        Schema::create('creditos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('venta_id')->constrained('ventas')->cascadeOnDelete();
            $table->decimal('monto_total', 10, 6);
            $table->decimal('saldo_pendiente', 10, 6);
            $table->string('plazo'); // Ej: "3 meses"
            $table->decimal('tasa_interes', 5, 2)->default(0);
            $table->string('estado')->default('Activo'); // Activo, Pagado, Vencido
            $table->timestamp('fecha_inicio');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('creditos');
    }
};
