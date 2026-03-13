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
        Schema::create('cuotas_credito', function (Blueprint $table) {
            $table->id();
            $table->foreignId('credito_id')->constrained('creditos')->cascadeOnDelete();
            $table->integer('numero_cuota'); // Ej: 1, 2, 3...
            $table->decimal('monto', 10, 6);
            $table->timestamp('fecha_vencimiento');
            $table->timestamp('fecha_pago')->nullable();
            $table->string('estado')->default('Pendiente'); // Pendiente, Pagada, Vencida
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cuotas_credito');
    }
};
