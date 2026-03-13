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
        Schema::create('cotizacions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_cliente')->nullable(); // Relación con el cliente (Entidad)
            
            $table->unsignedBigInteger('id_sucursal')->nullable(); // Relación con la sucursal
            $table->dateTime('fecha_emision'); // Fecha de emisión de la cotización
            $table->decimal('total', 12, 2)->default(0); // Total de la cotización
            $table->string('estado')->default('pendiente'); // Estado de la cotización (pendiente, aprobada, rechazada)
            $table->text('notes')->nullable(); // Notas adicionales
            $table->timestamps();
            $table->softDeletes(); // Soft deletes
            $table->unsignedBigInteger('created_by')->nullable(); // Usuario que creó la cotización
            $table->unsignedBigInteger('updated_by')->nullable(); // Usuario que actualizó la cotización
            $table->unsignedBigInteger('deleted_by')->nullable(); // Usuario que eliminó la cotización

            // Claves foráneas
            $table->foreign('id_cliente')->references('id')->on('entidades')->onDelete('restrict')->onUpdate('restrict');
            $table->foreign('id_sucursal')->references('id')->on('sucursales')->onDelete('restrict')->onUpdate('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cotizacions');
    }
};