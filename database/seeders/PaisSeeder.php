<?php

namespace Database\Seeders;

use App\Models\Pais;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaisSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Pais::insert([
            ['id' => 1, 'nombre' => 'Guatemala', 'abreviatura' => 'GT', 'region_codigo' => 'CA'],
            ['id' => 2, 'nombre' => 'Bolivia', 'abreviatura' => 'BOL', 'region_codigo' => 'SA'],
            ['id' => 3, 'nombre' => 'Brasil', 'abreviatura' => 'BRA', 'region_codigo' => 'SA'],
            ['id' => 4, 'nombre' => 'Canadá', 'abreviatura' => 'CAN', 'region_codigo' => 'NA'],
            ['id' => 5, 'nombre' => 'Chile', 'abreviatura' => 'CHL', 'region_codigo' => 'SA'],
            ['id' => 6, 'nombre' => 'Colombia', 'abreviatura' => 'COL', 'region_codigo' => 'SA'],
            ['id' => 7, 'nombre' => 'Costa Rica', 'abreviatura' => 'CRI', 'region_codigo' => 'CA'],
            ['id' => 8, 'nombre' => 'Cuba', 'abreviatura' => 'CUB', 'region_codigo' => 'CA'],
            ['id' => 9, 'nombre' => 'Ecuador', 'abreviatura' => 'ECU', 'region_codigo' => 'SA'],
            ['id' => 10, 'nombre' => 'El Salvador', 'abreviatura' => 'SLV', 'region_codigo' => 'CA'],
            ['id' => 11, 'nombre' => 'Argentina', 'abreviatura' => 'ARG', 'region_codigo' => 'SA'],
            ['id' => 12, 'nombre' => 'Honduras', 'abreviatura' => 'HND', 'region_codigo' => 'CA'],
            ['id' => 13, 'nombre' => 'México', 'abreviatura' => 'MEX', 'region_codigo' => 'NA'],
            ['id' => 14, 'nombre' => 'Nicaragua', 'abreviatura' => 'NIC', 'region_codigo' => 'CA'],
            ['id' => 15, 'nombre' => 'Panamá', 'abreviatura' => 'PAN', 'region_codigo' => 'CA'],
            ['id' => 16, 'nombre' => 'Paraguay', 'abreviatura' => 'PRY', 'region_codigo' => 'SA'],
            ['id' => 17, 'nombre' => 'Perú', 'abreviatura' => 'PER', 'region_codigo' => 'SA'],
            ['id' => 18, 'nombre' => 'República Dominicana', 'abreviatura' => 'DOM', 'region_codigo' => 'CA'],
            ['id' => 19, 'nombre' => 'Uruguay', 'abreviatura' => 'URY', 'region_codigo' => 'SA'],
            ['id' => 20, 'nombre' => 'Venezuela', 'abreviatura' => 'VEN', 'region_codigo' => 'SA'],
            ['id' => 21, 'nombre' => 'Estados Unidos', 'abreviatura' => 'USA', 'region_codigo' => 'NA'],
        ]);
    }
}
