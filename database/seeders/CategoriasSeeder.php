<?php

namespace Database\Seeders;

use App\Models\Categoria;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categorias = [
             [
                 'nombre' => 'Electrónica',
                 'descripcion' => 'Productos electrónicos y gadgets',
                 'created_at' => now(),
                 'updated_at' => now(),
             ],
             [
                 'nombre' => 'Ropa',
                 'descripcion' => 'Ropa y accesorios de moda',
                 'created_at' => now(),
                 'updated_at' => now(),
             ],
             [
                 'nombre' => 'Hogar',
                 'descripcion' => 'Artículos para el hogar y decoración',
                 'created_at' => now(),
                 'updated_at' => now(),
             ],
             [
                 'nombre' => 'Deportes',
                 'descripcion' => 'Equipos y accesorios deportivos',
                 'created_at' => now(),
                 'updated_at' => now(),
             ],
             [
                 'nombre' => 'Libros',
                 'descripcion' => 'Libros y material de lectura',
                 'created_at' => now(),
                 'updated_at' => now(),
             ],
        ];

        Categoria::insert($categorias);
    }
}
