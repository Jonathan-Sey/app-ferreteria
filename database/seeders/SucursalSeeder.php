<?php

namespace Database\Seeders;

use App\Models\Sucursal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SucursalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Sucursal::create([
            'nombre' => 'Tienda Principal',
            'direccion' => '',
            'telefono' => '',
            'id_municipio' => 1,
            'tipo' => '1',
            'estado' => 1,
        ]);
    }
}
