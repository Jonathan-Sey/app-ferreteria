<?php

namespace Database\Seeders;

use App\Models\AfiliacionIva;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class AfiliacionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        AfiliacionIva::insert([
            ['id' => 1, 'abreviacion' => 'GEN', 'nombre' => 'General'],
            ['id' => 2, 'abreviacion' => 'PEQ', 'nombre' => 'Pequeño Contribuyente'],
            ['id' => 3, 'abreviacion' => '-', 'nombre' => 'Pequeño contribuyente Regimen electronico'],
            ['id' => 4, 'abreviacion' => '-', 'nombre' => 'Contribuyente Agropecuario'],
            ['id' => 5, 'abreviacion' => '-', 'nombre' => 'Contribuyente Agropecuario Regimen Electronico Especial'],
            ['id' => 6, 'abreviacion' => 'EXE', 'nombre' => 'Sin afiliacion al IVA (EXE)'],
        ]);
    }
}
