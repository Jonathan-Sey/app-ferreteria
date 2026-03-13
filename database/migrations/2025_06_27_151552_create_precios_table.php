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
        Schema::create('precios', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_producto');
            $table->string('nombre', 100); // Nombre del precio
            $table->decimal('precio', 10, 2); // Precio del producto
            $table->text('descripcion')->nullable();

            $table->foreign('id_producto')->references('id')->on('productos')->onDelete('cascade');

            $table->index(['id_producto', 'nombre']); // Índice para mejorar el rendimiento de las consultas

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('precios');
    }
};
