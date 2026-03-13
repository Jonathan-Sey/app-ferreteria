<?php

namespace Database\Seeders;

use App\Models\SucursalUsuario;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class SucursalUsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        SucursalUsuario::create([
            'id_sucursal' => 1,
            'id_usuario' => 1,
            'estado' => 1,
            'default' => 1,
        ]);
        // SucursalUsuario::create([
        //     'id_sucursal' => 1,
        //     'id_usuario' => 2,
        //     'estado' => 1,
        //     'default' => 1,
        // ]);
    }
}
