<?php

namespace Database\Seeders;

use App\Models\Marca;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MarcasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $marcas = [
             [
                 'nombre' => 'Sony',
                 'descripcion' => 'Marca de productos electrónicos',
                 'created_at' => now(),
                 'updated_at' => now(),
             ],
             [
                 'nombre' => 'Samsung',
                 'descripcion' => 'Marca de productos electrónicos y electrodomésticos',
                 'created_at' => now(),
                 'updated_at' => now(),
             ],
             [
                 'nombre' => 'Nike',
                 'descripcion' => 'Marca de ropa y accesorios deportivos',
                 'created_at' => now(),
                 'updated_at' => now(),
             ],
             [
                 'nombre' => 'Adidas',
                 'descripcion' => 'Marca de ropa y accesorios deportivos',
                 'created_at' => now(),
                 'updated_at' => now(),
             ],
             [
                 'nombre' => 'Apple',
                 'descripcion' => 'Marca de productos electrónicos y software',
                 'created_at' => now(),
                 'updated_at' => now(),
             ],
        ];

        Marca::insert($marcas);
    }
}
