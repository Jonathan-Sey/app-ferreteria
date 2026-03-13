<?php

namespace Database\Seeders;

use App\Models\Moneda;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MonedasSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $monedas = [
            [
                'codigo' => 'GTQ',
                'nombre' => 'Quetzal',
                'simbolo' => 'Q',
            ],
            [
                'codigo' => 'USD',
                'nombre' => 'Dólar estadounidense',
                'simbolo' => '$',
            ],
            // Agrega más monedas según sea necesario
        ];

        Moneda::insert($monedas);
    }
}
