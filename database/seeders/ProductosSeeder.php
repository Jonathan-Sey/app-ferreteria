<?php

namespace Database\Seeders;

use App\Models\Producto;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ProductosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $productos = [
            // [
            //     'codigo' => 'P001',
            //     'nombre' => 'Producto 1',
            //     'descripcion' => 'Descripción del Producto 1',
            //     'fecha' => '2024-01-01',
            //     'imagen' => 'producto1.jpg',
            //     'id_marca' => 1,
            //     'id_categorias' => 1,
            //     'precio_compra' => 100.00,
            //     'precio_venta' => 120.00, // 20% de margen de ganancia
            //     'precio_mayoreo' => 110.00, // 10% de margen de ganancia
            //     'estado' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'codigo' => 'P002',
            //     'nombre' => 'Producto 2',
            //     'descripcion' => 'Descripción del Producto 2',
            //     'fecha' => '2024-01-02',
            //     'imagen' => 'producto2.jpg',
            //     'id_marca' => 2,
            //     'id_categorias' => 2,
            //     'precio_compra' => 200.00,
            //     'precio_venta' => 240.00, // 20% de margen de ganancia
            //     'precio_mayoreo' => 220.00, // 10% de margen de ganancia
            //     'estado' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'codigo' => 'P003',
            //     'nombre' => 'Producto 3',
            //     'descripcion' => 'Descripción del Producto 3',
            //     'fecha' => '2024-01-03',
            //     'imagen' => 'producto3.jpg',
            //     'id_marca' => 3,
            //     'id_presentacion' => 3,
            //     'id_categorias' => 3,
            //     'precio_compra' => 300.00,
            //     'precio_venta' => 360.00, // 20% de margen de ganancia
            //     'precio_mayoreo' => 330.00, // 10% de margen de ganancia
            //     'estado' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
        ];

        Producto::insert($productos);
    }
}