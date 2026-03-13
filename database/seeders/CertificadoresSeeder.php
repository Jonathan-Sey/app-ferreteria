<?php

namespace Database\Seeders;

use App\Models\Certificador;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CertificadoresSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $certificadores = [
            [
                'nit' => '16693949',
                'nombre' => 'Superintendencia de Administracion Tributaria',
                'estado' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ],
            // [
            //     'nit' => '12345678',
            //     'nombre' => 'Certificador 2',
            //     'estado' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
            // [
            //     'nit' => '87654321',
            //     'nombre' => 'Certificador 3',
            //     'estado' => 1,
            //     'created_at' => now(),
            //     'updated_at' => now(),
            // ],
        ];

        Certificador::insert($certificadores);
    }
}
