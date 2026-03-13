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
        Schema::create('productos', function (Blueprint $table) {
            $table->id();
            $table->string("codigo", 15)->unique(); // Código único
            $table->string("nombre", 100); // Nombre del producto
            $table->text("descripcion"); // Descripción del producto
            $table->date("fecha"); // Fecha de creación
            // $table->string('imagen')->nullable(); // Imagen del producto (opcional)
            $table->string('imagen')->nullable(); // Imagen del producto (opcional)
            $table->decimal('precio_compra', 8, 2); // Precio de compra
            $table->decimal('precio_venta', 8, 2); // Precio de venta
            $table->decimal('precio_mayoreo', 8, 2); // Precio por mayoreo

            // Relacionar con otras tablas (id_marca, id_presentacion, id_categoria)
            $table->unsignedBigInteger('id_marca');
            //$table->unsignedBigInteger('id_presentacion');
            $table->unsignedBigInteger('id_categorias');
            // Claves foráneas para las relaciones
            $table->foreign('id_marca')->references('id')->on('marcas')->onDelete('cascade');
            //$table->foreign('id_presentacion')->references('id')->on('presentaciones')->onDelete('cascade');
            $table->foreign('id_categorias')->references('id')->on('categorias')->onDelete('cascade');
            // Estado del producto, valor por defecto 1 (activo)
            $table->unsignedTinyInteger('estado')->default(1);

            $table->softDeletes(); // Soporte para eliminar de manera "blanda"
            $table->timestamps(); // Fechas de creación y actualización
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('productos'); // Eliminar tabla productos
    }
};
