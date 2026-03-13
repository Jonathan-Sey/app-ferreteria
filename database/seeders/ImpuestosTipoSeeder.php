<?php

namespace Database\Seeders;

use App\Models\ImpuestoTipo;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ImpuestosTipoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $impuestosTipo = [
            ['id' => 1, 'descripcion' => 'IVA', 'observaciones' => NULL],
            ['id' => 2, 'descripcion' => 'PETROLEO', 'observaciones' => NULL],
            ['id' => 3, 'descripcion' => 'TURISMO HOSPEDAJE', 'observaciones' => NULL],
            ['id' => 4, 'descripcion' => 'TURISMO PASAJES', 'observaciones' => NULL],
            ['id' => 5, 'descripcion' => 'TIMBRE DE PRENSA', 'observaciones' => NULL],
            ['id' => 6, 'descripcion' => 'BOMBEROS', 'observaciones' => NULL],
            ['id' => 7, 'descripcion' => 'TASA MUNICIPAL', 'observaciones' => NULL],
            ['id' => 8, 'descripcion' => 'BEBIDAS ALCOHOLICAS', 'observaciones' => NULL],
            ['id' => 9, 'descripcion' => 'TABACO', 'observaciones' => NULL],
            ['id' => 10, 'descripcion' => 'CEMENTO', 'observaciones' => NULL],
            ['id' => 11, 'descripcion' => 'BEBIDAS NO ALCOHOLICAS', 'observaciones' => NULL],
            ['id' => 12, 'descripcion' => 'TARIFA PORTUARIA', 'observaciones' => NULL],
            ['id' => 13, 'descripcion' => '-', 'observaciones' => NULL],
        ];

        ImpuestoTipo::insert($impuestosTipo);
    }
}
